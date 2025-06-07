<?php

namespace App\Http\Controllers;

use App\Models\Franchise;
use App\Models\User;
use App\Enums\UserRole; // Certifique-se que UserRole está correto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FranchiseController extends Controller
{
  // O método formatFranchiseData é um helper e não precisa de autorização direta aqui,
  // pois será chamado por métodos que já foram autorizados.
  protected function formatFranchiseData(Franchise $franchise): array
  {
    $franchise->load('user');
    return [
      'id' => $franchise->id,
      'user_id' => $franchise->user_id,
      'maxcam_email' => $franchise->maxcam_email,
      'cnpj' => $franchise->cnpj,
      'max_client' => $franchise->max_client,
      'contract_start_date' => $franchise->contract_start_date ? $franchise->contract_start_date->format('d/m/Y') : 'N/A',
      'contract_start_date_form' => $franchise->contract_start_date ? $franchise->contract_start_date->format('Y-m-d') : null,
      'actuation_region' => $franchise->actuation_region,
      'document_url' => $franchise->document_url,
      'document_full_url' => $franchise->document_url ? Storage::disk('public')->url($franchise->document_url) : null,
      'observations' => $franchise->observations,
      'created_at' => $franchise->created_at->format('d/m/Y H:i:s'),
      'updated_at' => $franchise->updated_at->format('d/m/Y H:i:s'),
      'user' => $franchise->user ? [
        'id' => $franchise->user->id,
        'name' => $franchise->user->name,
        'email' => $franchise->user->email,
      ] : null,
    ];
  }

  public function index(Request $request): Response
  {
    // Autoriza se o usuário logado pode ver qualquer registro do tipo Franchise
    // FranchisePolicy@viewAny e Gate::before (para admin) decidirão.
    // Conforme nossa policy, apenas admins verão esta lista.
    $this->authorize('viewAny', Franchise::class);

    $searchTerm = $request->input('search');

    $query = Franchise::with('user');

    // Aplica o filtro de busca se um termo foi fornecido
    if ($searchTerm) {
      $query->where(function ($q) use ($searchTerm) {
        $q->where('cnpj', 'like', "%{$searchTerm}%")
          ->orWhere('maxcam_email', 'like', "%{$searchTerm}%")
          ->orWhere('actuation_region', 'like', "%{$searchTerm}%")
          ->orWhereHas('user', function ($uq) use ($searchTerm) {
            $uq->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('email', 'like', "%{$searchTerm}%");
          });
      });
    }

    $franchises = $query->orderByDesc('created_at') // Mantido 'created_at' da tabela franchises
    ->paginate(10)
      ->withQueryString() // Importante para manter o search e outros params na paginação
      ->through(fn ($franchise) => [
        'id' => $franchise->id,
        'cnpj' => $franchise->cnpj,
        'maxcam_email' => $franchise->maxcam_email,
        'actuation_region' => $franchise->actuation_region,
        // Assegura que user existe antes de acessar suas propriedades
        'user_name' => $franchise->user ? $franchise->user->name : 'N/A',
        'user_email' => $franchise->user ? $franchise->user->email : 'N/A',
      ]);

    return Inertia::render('Franchises/Index', [
      'franchises' => $franchises,
      'filters' => $request->only(['search']), // Passa o filtro de volta para a view
    ]);
  }

  public function create(): Response
  {
    // Autoriza se o usuário logado pode criar um Franchise
    // FranchisePolicy@create e Gate::before (para admin) decidirão.
    // Conforme nossa policy, apenas admins podem criar.
    $this->authorize('create', Franchise::class);

    return Inertia::render('Franchises/Create');
  }

  public function store(Request $request) // Pode retornar Illuminate\Http\RedirectResponse
  {
    // Autoriza se o usuário logado pode criar um Franchise
    $this->authorize('create', Franchise::class);

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'maxcam_email' => 'required|string|email|max:255|unique:franchises,maxcam_email',
      'cnpj' => 'required|string|max:18|unique:franchises,cnpj',
      'max_client' => 'required|integer|min:0',
      'contract_start_date' => 'required|date',
      'actuation_region' => 'required|string|max:255',
      'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
      'observations' => 'nullable|string',
    ]);

    $documentPath = null;
    if ($request->hasFile('document_file')) {
      $documentPath = $request->file('document_file')->store('franchise_documents', 'public');
    }

    DB::transaction(function () use ($request, $documentPath) {
      $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password')),
        'role' => UserRole::FRANCHISE->value,
        'email_verified_at' => now(),
      ]);

      $user->franchise()->create([
        'maxcam_email' => $request->input('maxcam_email'),
        'cnpj' => $request->input('cnpj'),
        'max_client' => $request->input('max_client'),
        'contract_start_date' => $request->input('contract_start_date'),
        'actuation_region' => $request->input('actuation_region'),
        'document_url' => $documentPath,
        'observations' => $request->input('observations'),
      ]);
    });

    return redirect()->route('franchises.index')->with('success', 'Franqueado e usuário associado criados com sucesso.');
  }

  public function show(Franchise $franchise): Response
  {
    // Autoriza se o usuário logado pode ver este $franchise específico
    // FranchisePolicy@view e Gate::before (para admin) decidirão.
    $this->authorize('view', $franchise);

    return Inertia::render('Franchises/Show', [
      'franchise_data' => $this->formatFranchiseData($franchise),
    ]);
  }

  public function edit(Franchise $franchise): Response
  {
    // Autoriza se o usuário logado pode atualizar este $franchise
    // FranchisePolicy@update e Gate::before (para admin) decidirão.
    $this->authorize('update', $franchise);

    return Inertia::render('Franchises/Edit', [
      'franchise_data' => $this->formatFranchiseData($franchise),
    ]);
  }

  public function update(Request $request, Franchise $franchise) // Pode retornar Illuminate\Http\RedirectResponse
  {
    // Autoriza se o usuário logado pode atualizar este $franchise
    $this->authorize('update', $franchise);

    $user = $franchise->user;

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
      'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
      'maxcam_email' => ['required', 'string', 'email', 'max:255', Rule::unique('franchises', 'maxcam_email')->ignore($franchise->id)],
      'cnpj' => ['required', 'string', 'max:14', Rule::unique('franchises', 'cnpj')->ignore($franchise->id)],
      'max_client' => 'required|integer|min:0',
      'contract_start_date' => 'required|date',
      'actuation_region' => 'required|string|max:255',
      'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
      'observations' => 'nullable|string',
    ]);

    $documentPath = $franchise->document_url;
    if ($request->hasFile('document_file')) {
      if ($franchise->document_url) {
        Storage::disk('public')->delete($franchise->document_url);
      }
      $documentPath = $request->file('document_file')->store('franchise_documents', 'public');
    }

    DB::transaction(function () use ($request, $user, $franchise, $documentPath) {
      $userData = [
        'name' => $request->input('name'),
        'email' => $request->input('email'),
      ];
      if ($request->filled('password')) {
        $userData['password'] = Hash::make($request->input('password'));
      }
      $user->update($userData);

      $franchiseData = $request->only([
        'maxcam_email', 'cnpj', 'max_client', 'contract_start_date',
        'actuation_region', 'observations'
      ]);
      $franchiseData['document_url'] = $documentPath;
      $franchise->update($franchiseData);
    });

    return redirect()->route('franchises.index')->with('success', 'Franqueado atualizado com sucesso.');
  }

  public function destroy(Franchise $franchise) // Pode retornar Illuminate\Http\RedirectResponse
  {
    // Autoriza se o usuário logado pode deletar este $franchise
    // FranchisePolicy@delete e Gate::before (para admin) decidirão.
    // Conforme nossa policy, apenas admins podem deletar.
    $this->authorize('delete', $franchise);

    DB::transaction(function () use ($franchise) {
      if ($franchise->document_url) {
        Storage::disk('public')->delete($franchise->document_url);
      }
      $user = $franchise->user; // Pega o usuário antes de deletar a franquia, se a relação for nullable
      $franchise->delete();
      if ($user) {
        $user->delete(); // Deleta o usuário associado
      }
    });

    return redirect()->route('franchises.index')->with('success', 'Franqueado e usuário associado excluídos com sucesso.');
  }
}

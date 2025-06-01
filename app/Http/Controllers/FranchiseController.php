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
      'document_url' => $franchise->document_url, // Caminho relativo salvo
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

  public function index(): Response
  {
    $franchises = Franchise::with('user')
      ->orderByDesc('created_at')
      ->paginate(10)
      ->through(fn ($franchise) => [
        'id' => $franchise->id,
        'cnpj' => $franchise->cnpj,
        'maxcam_email' => $franchise->maxcam_email,
        'actuation_region' => $franchise->actuation_region,
        'user_name' => $franchise->user->name,
        'user_email' => $franchise->user->email,
      ]);

    return Inertia::render('Franchises/Index', [
      'franchises' => $franchises,
    ]);
  }

  public function create(): Response
  {
    return Inertia::render('Franchises/Create');
  }

  public function store(Request $request)
  {
    $request->validate([
      // User fields
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      // Franchise fields
      'maxcam_email' => 'required|string|email|max:255|unique:franchises,maxcam_email',
      'cnpj' => 'required|string|max:18|unique:franchises,cnpj', // Ajuste max se necessário para CNPJ formatado
      'max_client' => 'required|integer|min:0',
      'contract_start_date' => 'required|date',
      'actuation_region' => 'required|string|max:255',
      'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120', // Ex: 5MB max
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
        'role' => UserRole::FRANCHISE->value, // Usando o Enum
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
    return Inertia::render('Franchises/Show', [
      'franchise_data' => $this->formatFranchiseData($franchise),
    ]);
  }

  public function edit(Franchise $franchise): Response
  {
    return Inertia::render('Franchises/Edit', [
      'franchise_data' => $this->formatFranchiseData($franchise),
    ]);
  }

  public function update(Request $request, Franchise $franchise)
  {
    $user = $franchise->user;

    $request->validate([
      // User fields
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
      'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
      // Franchise fields
      'maxcam_email' => ['required', 'string', 'email', 'max:255', Rule::unique('franchises', 'maxcam_email')->ignore($franchise->id)],
      'cnpj' => ['required', 'string', 'max:18', Rule::unique('franchises', 'cnpj')->ignore($franchise->id)],
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

  public function destroy(Franchise $franchise)
  {
    DB::transaction(function () use ($franchise) {
      if ($franchise->document_url) {
        Storage::disk('public')->delete($franchise->document_url);
      }
      $user = $franchise->user;
      $franchise->delete();
      if ($user) {
        $user->delete();
      }
    });

    return redirect()->route('franchises.index')->with('success', 'Franqueado e usuário associado excluídos com sucesso.');
  }
}

<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreFranchiseRequest;
use App\Http\Requests\UpdateFranchiseRequest;
use App\Models\Franchise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
    $this->authorize('viewAny', Franchise::class);

    $searchTerm = $request->input('search');

    $query = Franchise::with('user');

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

    $franchises = $query->orderByDesc('created_at')
      ->paginate(10)
      ->withQueryString()
      ->through(fn ($franchise) => [
        'id' => $franchise->id,
        'cnpj' => $franchise->cnpj,
        'maxcam_email' => $franchise->maxcam_email,
        'actuation_region' => $franchise->actuation_region,
        'user_name' => $franchise->user ? $franchise->user->name : 'N/A',
        'user_email' => $franchise->user ? $franchise->user->email : 'N/A',
      ]);

    return Inertia::render('Franchises/Index', [
      'franchises' => $franchises,
      'filters' => $request->only(['search']),
    ]);
  }

  public function create(): Response
  {
    $this->authorize('create', Franchise::class);

    return Inertia::render('Franchises/Create');
  }

  public function store(StoreFranchiseRequest $request)
  {
    $validatedData = $request->validated();

    $documentPath = null;
    if ($request->hasFile('document_file')) {
      $documentPath = $request->file('document_file')->store('franchise_documents', 'public');
    }

    DB::transaction(function () use ($validatedData, $documentPath) {
      $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
        'role' => UserRole::FRANCHISE->value,
        'email_verified_at' => now(),
      ]);

      $user->franchise()->create([
        'maxcam_email' => $validatedData['maxcam_email'],
        'cnpj' => $validatedData['cnpj'],
        'max_client' => $validatedData['max_client'],
        'contract_start_date' => $validatedData['contract_start_date'],
        'actuation_region' => $validatedData['actuation_region'],
        'document_url' => $documentPath,
        'observations' => $validatedData['observations'],
      ]);
    });

    return redirect()->route('franchises.index')->with('success', 'Franqueado e usuário associado criados com sucesso.');
  }

  public function show(Franchise $franchise): Response
  {
    $this->authorize('view', $franchise);

    return Inertia::render('Franchises/Show', [
      'franchise_data' => $this->formatFranchiseData($franchise),
    ]);
  }

  public function edit(Franchise $franchise): Response
  {
    $this->authorize('update', $franchise);

    return Inertia::render('Franchises/Edit', [
      'franchise_data' => $this->formatFranchiseData($franchise),
    ]);
  }

  public function update(UpdateFranchiseRequest $request, Franchise $franchise)
  {
    $validatedData = $request->validated();
    $user = $franchise->user;

    $documentPath = $franchise->document_url;
    if ($request->hasFile('document_file')) {
      if ($franchise->document_url) {
        Storage::disk('public')->delete($franchise->document_url);
      }
      $documentPath = $request->file('document_file')->store('franchise_documents', 'public');
    }

    DB::transaction(function () use ($validatedData, $user, $franchise, $documentPath) {
      $userData = [
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
      ];
      if (!empty($validatedData['password'])) {
        $userData['password'] = Hash::make($validatedData['password']);
      }
      $user->update($userData);

      $franchiseData = [
        'maxcam_email' => $validatedData['maxcam_email'],
        'cnpj' => $validatedData['cnpj'],
        'max_client' => $validatedData['max_client'],
        'contract_start_date' => $validatedData['contract_start_date'],
        'actuation_region' => $validatedData['actuation_region'],
        'observations' => $validatedData['observations'],
        'document_url' => $documentPath,
      ];
      $franchise->update($franchiseData);
    });

    return redirect()->route('franchises.index')->with('success', 'Franqueado atualizado com sucesso.');
  }

  public function destroy(Franchise $franchise)
  {
    $this->authorize('delete', $franchise);

    DB::transaction(function () use ($franchise) {
      if ($franchise->document_url) {
        Storage::disk('public')->delete($franchise->document_url);
      }

      $user = $franchise->user;
      $franchise->delete();

      $user?->delete();
    });

    return redirect()->route('franchises.index')->with('success', 'Franqueado e usuário associado excluídos com sucesso.');
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\User;
use App\Models\Address;
use App\Models\Client; // Importar Client
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importar Auth
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CollaboratorController extends Controller
{
  protected function formatCollaboratorData(Collaborator $collaborator): array
  {
    $collaborator->loadMissing(['user.addresses', 'client.user']);
    $mainAddress = $collaborator->user->addresses->first();

    return [
      'id' => $collaborator->id,
      'photo_url' => $collaborator->photo_url,
      'photo_full_url' => $collaborator->photo_url ? Storage::disk('public')->url($collaborator->photo_url) : null,
      'curriculum_url' => $collaborator->curriculum_url,
      'curriculum_full_url' => $collaborator->curriculum_url ? Storage::disk('public')->url($collaborator->curriculum_url) : null,
      'date_of_birth' => $collaborator->date_of_birth ? $collaborator->date_of_birth->format('Y-m-d') : null,
      'gender' => $collaborator->gender,
      'is_special_needs_person' => (bool) $collaborator->is_special_needs_person,
      'marital_status' => $collaborator->marital_status,
      'scholarity' => $collaborator->scholarity,
      'father_name' => $collaborator->father_name,
      'mother_name' => $collaborator->mother_name,
      'nationality' => $collaborator->nationality,
      'personal_email' => $collaborator->personal_email,
      'business_email' => $collaborator->business_email,
      'phone' => $collaborator->phone,
      'cellphone' => $collaborator->cellphone,
      'emergency_phone' => $collaborator->emergency_phone,
      'department' => $collaborator->department,
      'position' => $collaborator->position,
      'type_of_contract' => $collaborator->type_of_contract,
      'salary' => $collaborator->salary,
      'admission_date' => $collaborator->admission_date ? $collaborator->admission_date->format('Y-m-d') : null,
      'direct_superior_name' => $collaborator->direct_superior_name,
      'hierarchical_degree' => $collaborator->hierarchical_degree,
      'observations' => $collaborator->observations,
      'contract_start_date' => $collaborator->contract_start_date ? $collaborator->contract_start_date->format('Y-m-d') : null,
      'contract_expiration' => $collaborator->contract_expiration ? $collaborator->contract_expiration->format('Y-m-d') : null,
      'cpf' => $collaborator->cpf,
      'rg' => $collaborator->rg,
      'cnh' => $collaborator->cnh,
      'reservista' => $collaborator->reservista,
      'titulo_eleitor' => $collaborator->titulo_eleitor,
      'zona_eleitoral' => $collaborator->zona_eleitoral,
      'pis_ctps_numero' => $collaborator->pis_ctps_numero,
      'ctps_serie' => $collaborator->ctps_serie,
      'banco' => $collaborator->banco,
      'agencia' => $collaborator->agencia,
      'conta_corrente' => $collaborator->conta_corrente,
      'client_id' => $collaborator->client_id,
      'client_name' => $collaborator->client && $collaborator->client->user ? $collaborator->client->user->name : 'N/A',
      'created_at' => $collaborator->created_at->format('d/m/Y H:i:s'),
      'updated_at' => $collaborator->updated_at->format('d/m/Y H:i:s'),
      'user' => $collaborator->user ? [
        'id' => $collaborator->user->id,
        'name' => $collaborator->user->name,
        'email' => $collaborator->user->email,
      ] : null,
      'address' => $mainAddress ? [
        'id' => $mainAddress->id,
        'cep' => $mainAddress->cep,
        'street' => $mainAddress->street,
        'number' => $mainAddress->number,
        'complement' => $mainAddress->complement,
        'neighborhood' => $mainAddress->neighborhood,
        'state' => $mainAddress->state,
        'city' => $mainAddress->city,
      ] : null,
    ];
  }

  public function index(): Response
  {
    $this->authorize('viewAny', Collaborator::class);

    $user = Auth::user();
    $query = Collaborator::with(['user.addresses', 'client.user']);

    if ($user->role === UserRole::FRANCHISE) {
      if ($user->franchise) {
        $franchiseId = $user->franchise->id;
        $query->whereHas('client', function ($q) use ($franchiseId) {
          $q->where('franchise_id', $franchiseId);
        });
      } else {
        $query->whereRaw('1 = 0');
      }
    } elseif ($user->role === UserRole::CLIENT) {
      if ($user->client) {
        $query->where('client_id', $user->client->id);
      } else {
        $query->whereRaw('1 = 0');
      }
    }
    // Admin vê todos

    $collaborators = $query->orderByDesc('collaborators.created_at')
      ->paginate(10)
      ->through(fn ($collaborator) => [
        'id' => $collaborator->id,
        'photo_full_url' => $collaborator->photo_url ? Storage::disk('public')->url($collaborator->photo_url) : null,
        'user_name' => $collaborator->user->name,
        'user_email' => $collaborator->user->email,
        'department' => $collaborator->department,
        'position' => $collaborator->position,
        'client_name' => $collaborator->client && $collaborator->client->user ? $collaborator->client->user->name : 'N/A',
        'city' => $collaborator->user->addresses->first()->city ?? 'N/A',
      ]);

    return Inertia::render('Collaborators/Index', [
      'collaborators' => $collaborators,
    ]);
  }

  public function create(): Response
  {
    $this->authorize('create', Collaborator::class);

    $clients = [];
    $user = Auth::user();

    if ($user->role === UserRole::ADMIN) {
      $clients = Client::with('user')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
    } elseif ($user->role === UserRole::FRANCHISE && $user->franchise) {
      $clients = Client::with('user')->where('franchise_id', $user->franchise->id)->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
    }
    // Se for UserRole::CLIENT, o client_id será o do próprio usuário e não precisa de lista para seleção.

    return Inertia::render('Collaborators/Create', [
      'clients' => $clients,
      // Passar o ID do cliente do usuário logado se ele for um cliente, para o form usar se necessário
      'auth_client_id' => $user->role === UserRole::CLIENT && $user->client ? $user->client->id : null,
    ]);
  }

  public function store(Request $request)
  {
    $this->authorize('create', Collaborator::class);

    $loggedInUser = Auth::user();

    $validationRules = [ /* Suas regras de validação completas como no seu código original */
      // User
      'user.name' => 'required|string|max:255',
      'user.email' => 'required|string|email|max:255|unique:users,email',
      'user.password' => ['required', 'confirmed', Rules\Password::defaults()],
      // Collaborator
      'collaborator.photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'collaborator.curriculum_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
      'collaborator.date_of_birth' => 'nullable|date',
      'collaborator.gender' => 'nullable|string|max:255',
      'collaborator.is_special_needs_person' => 'boolean',
      'collaborator.marital_status' => 'nullable|string|max:255',
      'collaborator.scholarity' => 'nullable|string|max:255',
      'collaborator.father_name' => 'nullable|string|max:255',
      'collaborator.mother_name' => 'nullable|string|max:255',
      'collaborator.nationality' => 'nullable|string|max:255',
      'collaborator.personal_email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('collaborators', 'personal_email')],
      'collaborator.business_email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('collaborators', 'business_email')],
      'collaborator.phone' => 'nullable|string|max:20',
      'collaborator.cellphone' => 'nullable|string|max:20',
      'collaborator.emergency_phone' => 'nullable|string|max:20',
      'collaborator.department' => 'nullable|string|max:255',
      'collaborator.position' => 'nullable|string|max:255',
      'collaborator.type_of_contract' => 'nullable|string|max:255',
      'collaborator.salary' => 'nullable|numeric|min:0',
      'collaborator.admission_date' => 'nullable|date',
      'collaborator.direct_superior_name' => 'nullable|string|max:255',
      'collaborator.hierarchical_degree' => 'nullable|string|max:255',
      'collaborator.observations' => 'nullable|string',
      'collaborator.contract_start_date' => 'nullable|date',
      'collaborator.contract_expiration' => 'nullable|date|after_or_equal:collaborator.contract_start_date',
      'collaborator.cpf' => ['nullable', 'string', 'max:14', Rule::unique('collaborators', 'cpf')],
      'collaborator.rg' => 'nullable|string|max:20',
      'collaborator.cnh' => ['nullable', 'string', 'max:20', Rule::unique('collaborators', 'cnh')],
      'collaborator.reservista' => 'nullable|string|max:30',
      'collaborator.titulo_eleitor' => 'nullable|string|max:30',
      'collaborator.zona_eleitoral' => 'nullable|string|max:10',
      'collaborator.pis_ctps_numero' => 'nullable|string|max:30',
      'collaborator.ctps_serie' => 'nullable|string|max:20',
      'collaborator.banco' => 'nullable|string|max:255',
      'collaborator.agencia' => 'nullable|string|max:10',
      'collaborator.conta_corrente' => 'nullable|string|max:20',
      // Address
      'address.cep' => 'required_with:address.street,address.city,address.state|nullable|string|max:9',
      'address.street' => 'required_with:address.cep,address.city,address.state|nullable|string|max:255',
      'address.number' => 'nullable|string|max:20',
      'address.complement' => 'nullable|string|max:255',
      'address.neighborhood' => 'nullable|string|max:255',
      'address.state' => 'required_with:address.cep,address.street,address.city|nullable|string|max:2', // UF
      'address.city' => 'required_with:address.cep,address.street,address.state|nullable|string|max:255',
    ];
    if ($loggedInUser->role === UserRole::ADMIN || $loggedInUser->role === UserRole::FRANCHISE) {
      $validationRules['collaborator.client_id'] = 'required|exists:clients,id';
    }
    $validated = $request->validate($validationRules);

    $userData = $validated['user'];
    $collaboratorData = $validated['collaborator'];
    $addressData = $validated['address'] ?? null;

    // Definir client_id corretamente
    if ($loggedInUser->role === UserRole::CLIENT) {
      if (!$loggedInUser->client) abort(403, 'Usuário cliente não associado a um registro de cliente.');
      $collaboratorData['client_id'] = $loggedInUser->client->id;
    } elseif (isset($validated['collaborator']['client_id'])) {
      $clientIdFromRequest = $validated['collaborator']['client_id'];
      if ($loggedInUser->role === UserRole::FRANCHISE) {
        if(!$loggedInUser->franchise) abort(403, 'Usuário franqueado não associado a uma franquia.');
        $client = Client::where('id', $clientIdFromRequest)
          ->where('franchise_id', $loggedInUser->franchise->id)
          ->first();
        if (!$client) {
          abort(403, 'Franqueado não pode atribuir colaborador a este cliente.');
        }
      }
      $collaboratorData['client_id'] = $clientIdFromRequest;
    } else {
      // Admin ou Franqueado não especificaram client_id (validação já deve ter pego)
      abort(403, 'Client ID é obrigatório e não foi fornecido ou é inválido.');
    }


    if ($request->hasFile('collaborator.photo_file')) {
      $collaboratorData['photo_url'] = $request->file('collaborator.photo_file')->store('collaborator_photos', 'public');
    }
    if ($request->hasFile('collaborator.curriculum_file')) {
      $collaboratorData['curriculum_url'] = $request->file('collaborator.curriculum_file')->store('collaborator_curriculums', 'public');
    }

    DB::transaction(function () use ($userData, $collaboratorData, $addressData) {
      $user = User::create([
        'name' => $userData['name'],
        'email' => $userData['email'],
        'password' => Hash::make($userData['password']),
        'role' => UserRole::COLLABORATOR->value,
        'email_verified_at' => now(),
      ]);
      $user->collaborator()->create($collaboratorData);
      if ($addressData && !empty(array_filter($addressData))) {
        $user->addresses()->create($addressData);
      }
    });

    return redirect()->route('collaborators.index')->with('success', 'Colaborador criado com sucesso.');
  }

  public function show(Collaborator $collaborator): Response
  {
    $this->authorize('view', $collaborator);
    return Inertia::render('Collaborators/Show', [
      'collaborator_data' => $this->formatCollaboratorData($collaborator),
    ]);
  }

  public function edit(Collaborator $collaborator): Response
  {
    $this->authorize('update', $collaborator);
    $clients = [];
    $user = Auth::user();
    if ($user->role === UserRole::ADMIN) {
      $clients = Client::with('user')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
    } elseif ($user->role === UserRole::FRANCHISE && $user->franchise) {
      $clients = Client::with('user')->where('franchise_id', $user->franchise->id)->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
    }
    $collaborator->loadMissing(['user.addresses', 'client.user']);

    return Inertia::render('Collaborators/Edit', [
      'collaborator_data' => $this->formatCollaboratorData($collaborator),
      'clients' => $clients,
    ]);
  }

  public function update(Request $request, Collaborator $collaborator)
  {
    $this->authorize('update', $collaborator);

    $collaboratorUser = $collaborator->user; // Usuário do colaborador que está sendo editado
    $mainAddress = $collaboratorUser->addresses->first();
    $loggedInUser = Auth::user(); // Usuário logado que está fazendo a ação

    $validationRules = [ /* Suas regras de validação completas */
      // User
      'user.name' => 'required|string|max:255',
      'user.email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($collaboratorUser->id)],
      'user.password' => ['nullable', 'confirmed', Rules\Password::defaults()],
      // Collaborator
      'collaborator.photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'collaborator.curriculum_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
      'collaborator.date_of_birth' => 'nullable|date',
      'collaborator.gender' => 'nullable|string|max:255',
      'collaborator.is_special_needs_person' => 'boolean',
      'collaborator.marital_status' => 'nullable|string|max:255',
      'collaborator.scholarity' => 'nullable|string|max:255',
      'collaborator.father_name' => 'nullable|string|max:255',
      'collaborator.mother_name' => 'nullable|string|max:255',
      'collaborator.nationality' => 'nullable|string|max:255',
      'collaborator.personal_email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('collaborators', 'personal_email')->ignore($collaborator->id)],
      'collaborator.business_email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('collaborators', 'business_email')->ignore($collaborator->id)],
      'collaborator.phone' => 'nullable|string|max:20',
      'collaborator.cellphone' => 'nullable|string|max:20',
      'collaborator.emergency_phone' => 'nullable|string|max:20',
      'collaborator.department' => 'nullable|string|max:255',
      'collaborator.position' => 'nullable|string|max:255',
      'collaborator.type_of_contract' => 'nullable|string|max:255',
      'collaborator.salary' => 'nullable|numeric|min:0',
      'collaborator.admission_date' => 'nullable|date',
      'collaborator.direct_superior_name' => 'nullable|string|max:255',
      'collaborator.hierarchical_degree' => 'nullable|string|max:255',
      'collaborator.observations' => 'nullable|string',
      'collaborator.contract_start_date' => 'nullable|date',
      'collaborator.contract_expiration' => 'nullable|date|after_or_equal:collaborator.contract_start_date',
      'collaborator.cpf' => ['nullable', 'string', 'max:14', Rule::unique('collaborators', 'cpf')->ignore($collaborator->id)],
      'collaborator.rg' => 'nullable|string|max:20',
      'collaborator.cnh' => ['nullable', 'string', 'max:20', Rule::unique('collaborators', 'cnh')->ignore($collaborator->id)],
      'collaborator.reservista' => 'nullable|string|max:30',
      'collaborator.titulo_eleitor' => 'nullable|string|max:30',
      'collaborator.zona_eleitoral' => 'nullable|string|max:10',
      'collaborator.pis_ctps_numero' => 'nullable|string|max:30',
      'collaborator.ctps_serie' => 'nullable|string|max:20',
      'collaborator.banco' => 'nullable|string|max:255',
      'collaborator.agencia' => 'nullable|string|max:10',
      'collaborator.conta_corrente' => 'nullable|string|max:20',
      // Address
      'address.cep' => 'required_with:address.street,address.city,address.state|nullable|string|max:9',
      'address.street' => 'required_with:address.cep,address.city,address.state|nullable|string|max:255',
      'address.number' => 'nullable|string|max:20',
      'address.complement' => 'nullable|string|max:255',
      'address.neighborhood' => 'nullable|string|max:255',
      'address.state' => 'required_with:address.cep,address.street,address.city|nullable|string|max:2',
      'address.city' => 'required_with:address.cep,address.street,address.state|nullable|string|max:255',
    ];
    if ($loggedInUser->role === UserRole::ADMIN || $loggedInUser->role === UserRole::FRANCHISE) {
      $validationRules['collaborator.client_id'] = 'required|exists:clients,id';
    }
    $validated = $request->validate($validationRules);

    $userData = $validated['user'];
    $collaboratorData = $validated['collaborator'];
    $addressData = $validated['address'] ?? null;

    if ($request->hasFile('collaborator.photo_file')) {
      if ($collaborator->photo_url) { Storage::disk('public')->delete($collaborator->photo_url); }
      $collaboratorData['photo_url'] = $request->file('collaborator.photo_file')->store('collaborator_photos', 'public');
    }
    if ($request->hasFile('collaborator.curriculum_file')) {
      if ($collaborator->curriculum_url) { Storage::disk('public')->delete($collaborator->curriculum_url); }
      $collaboratorData['curriculum_url'] = $request->file('collaborator.curriculum_file')->store('collaborator_curriculums', 'public');
    }

    DB::transaction(function () use ($userData, $collaboratorData, $addressData, $collaboratorUser, $collaborator, $mainAddress, $loggedInUser) {
      if (!empty($userData['password'])) {
        $userData['password'] = Hash::make($userData['password']);
      } else {
        unset($userData['password']);
      }
      $collaboratorUser->update($userData);

      // Apenas Admin ou Franqueado (que pode editar este colaborador) podem mudar o client_id
      if (($loggedInUser->role === UserRole::ADMIN || $loggedInUser->role === UserRole::FRANCHISE) && isset($collaboratorData['client_id'])) {
        $clientIdFromRequest = $collaboratorData['client_id'];
        // Validação extra para Franqueado: só pode atribuir a um de seus clientes
        if ($loggedInUser->role === UserRole::FRANCHISE) {
          if(!$loggedInUser->franchise) abort(403, 'Usuário franqueado não associado a uma franquia.');
          $client = Client::where('id', $clientIdFromRequest)
            ->where('franchise_id', $loggedInUser->franchise->id)
            ->first();
          if (!$client) {
            abort(403, 'Franqueado não pode atribuir colaborador a este cliente.');
          }
        }
        // Se passou na policy 'update' e aqui, então pode setar.
        // A policy já deve ter validado se este franqueado PODE editar este colaborador.
      } else {
        // Se não for admin/franqueado, ou client_id não está no request, não muda o client_id.
        // A policy de update deve garantir que um Cliente não possa mudar o client_id do seu colaborador.
        unset($collaboratorData['client_id']);
      }
      $collaborator->update($collaboratorData);

      if ($addressData && !empty(array_filter($addressData))) {
        if ($mainAddress) {
          $mainAddress->update($addressData);
        } else {
          $collaboratorUser->addresses()->create($addressData);
        }
      } elseif ($mainAddress && (empty($addressData) || empty(array_filter($addressData)))) {
        // $mainAddress->delete(); // Opção de deletar endereço se campos forem esvaziados
      }
    });

    return redirect()->route('collaborators.index')->with('success', 'Colaborador atualizado com sucesso.');
  }

  public function destroy(Collaborator $collaborator)
  {
    $this->authorize('delete', $collaborator);

    DB::transaction(function () use ($collaborator) {
      if ($collaborator->photo_url) {
        Storage::disk('public')->delete($collaborator->photo_url);
      }
      if ($collaborator->curriculum_url) {
        Storage::disk('public')->delete($collaborator->curriculum_url);
      }
      $user = $collaborator->user;
      if ($user) {
        $user->addresses()->delete();
      }
      $collaborator->delete();
      if ($user) {
        $user->delete();
      }
    });
    return redirect()->route('collaborators.index')->with('success', 'Colaborador e dados associados excluídos com sucesso.');
  }
}

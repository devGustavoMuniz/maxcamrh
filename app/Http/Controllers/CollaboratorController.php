<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\User;
use App\Models\Address;
use App\Enums\UserRole;
use Illuminate\Http\Request;
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
    $collaborator->load(['user.addresses']); // Carrega usuário e seus endereços
    $mainAddress = $collaborator->user->addresses->first(); // Pega o primeiro endereço como principal

    return [
      'id' => $collaborator->id,
      // Collaborator fields
      'photo_url' => $collaborator->photo_url,
      'photo_full_url' => $collaborator->photo_url ? Storage::disk('public')->url($collaborator->photo_url) : null,
      'curriculum_url' => $collaborator->curriculum_url,
      'curriculum_full_url' => $collaborator->curriculum_url ? Storage::disk('public')->url($collaborator->curriculum_url) : null,
      'date_of_birth' => $collaborator->date_of_birth ? $collaborator->date_of_birth->format('Y-m-d') : null,
      'gender' => $collaborator->gender,
      'is_special_needs_person' => $collaborator->is_special_needs_person,
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
      'created_at' => $collaborator->created_at->format('d/m/Y H:i:s'),
      'updated_at' => $collaborator->updated_at->format('d/m/Y H:i:s'),
      // User fields
      'user' => $collaborator->user ? [
        'id' => $collaborator->user->id,
        'name' => $collaborator->user->name,
        'email' => $collaborator->user->email,
      ] : null,
      // Address fields (do primeiro endereço)
      'address' => $mainAddress ? [
        'id' => $mainAddress->id, // Para update
        'cep' => $mainAddress->cep,
        'street' => $mainAddress->street,
        'number' => $mainAddress->number,
        'complement' => $mainAddress->complement,
        'neighborhood' => $mainAddress->neighborhood,
        'state' => $mainAddress->state,
        'city' => $mainAddress->city,
      ] : null, // Envia nulo se não houver endereço
    ];
  }


  public function index(): Response
  {
    $collaborators = Collaborator::with(['user.addresses' => function ($query) {
      // Opcional: ordenar ou pegar apenas o primeiro endereço se houver muitos
      // $query->orderBy('created_at', 'asc')->limit(1);
    }])
      ->orderByDesc('created_at')
      ->paginate(10)
      ->through(fn ($collaborator) => [
        'id' => $collaborator->id,
        'photo_full_url' => $collaborator->photo_url ? Storage::disk('public')->url($collaborator->photo_url) : null,
        'user_name' => $collaborator->user->name,
        'user_email' => $collaborator->user->email,
        'department' => $collaborator->department,
        'position' => $collaborator->position,
        'city' => $collaborator->user->addresses->first()->city ?? 'N/A', // Pega a cidade do primeiro endereço
      ]);

    return Inertia::render('Collaborators/Index', [
      'collaborators' => $collaborators,
    ]);
  }

  public function create(): Response
  {
    return Inertia::render('Collaborators/Create');
  }

  public function store(Request $request)
  {
    // Validação agrupada
    $validated = $request->validate([
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
      'collaborator.personal_email' => 'nullable|string|email|max:255|unique:collaborators,personal_email',
      'collaborator.business_email' => 'nullable|string|email|max:255|unique:collaborators,business_email',
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
      'collaborator.cpf' => 'nullable|string|max:14|unique:collaborators,cpf', // Adicionar validação de formato de CPF se necessário
      'collaborator.rg' => 'nullable|string|max:20',
      'collaborator.cnh' => 'nullable|string|max:20|unique:collaborators,cnh',
      'collaborator.reservista' => 'nullable|string|max:30',
      'collaborator.titulo_eleitor' => 'nullable|string|max:30',
      'collaborator.zona_eleitoral' => 'nullable|string|max:10',
      'collaborator.pis_ctps_numero' => 'nullable|string|max:30',
      'collaborator.ctps_serie' => 'nullable|string|max:20',
      'collaborator.banco' => 'nullable|string|max:255',
      'collaborator.agencia' => 'nullable|string|max:10',
      'collaborator.conta_corrente' => 'nullable|string|max:20',
      // Address (opcional, mas se fornecido, alguns campos são obrigatórios)
      'address.cep' => 'required_with:address.street,address.city,address.state|nullable|string|max:9',
      'address.street' => 'required_with:address.cep,address.city,address.state|nullable|string|max:255',
      'address.number' => 'nullable|string|max:20',
      'address.complement' => 'nullable|string|max:255',
      'address.neighborhood' => 'nullable|string|max:255',
      'address.state' => 'required_with:address.cep,address.street,address.city|nullable|string|max:2', // UF
      'address.city' => 'required_with:address.cep,address.street,address.state|nullable|string|max:255',
    ]);

    $userData = $validated['user'];
    $collaboratorData = $validated['collaborator'];
    $addressData = $validated['address'] ?? null; // Pode ser nulo se não fornecido

    // Uploads
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

      if ($addressData && !empty(array_filter($addressData))) { // Cria endereço apenas se dados foram fornecidos
        $user->addresses()->create($addressData);
      }
    });

    return redirect()->route('collaborators.index')->with('success', 'Colaborador criado com sucesso.');
  }

  public function show(Collaborator $collaborator): Response
  {
    return Inertia::render('Collaborators/Show', [
      'collaborator_data' => $this->formatCollaboratorData($collaborator),
    ]);
  }

  public function edit(Collaborator $collaborator): Response
  {
    return Inertia::render('Collaborators/Edit', [
      'collaborator_data' => $this->formatCollaboratorData($collaborator),
    ]);
  }

  public function update(Request $request, Collaborator $collaborator)
  {
    $user = $collaborator->user;
    $mainAddress = $user->addresses->first(); // Pega o primeiro endereço para atualização

    // Validação agrupada
    $validated = $request->validate([
      // User
      'user.name' => 'required|string|max:255',
      'user.email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
      'user.password' => ['nullable', 'confirmed', Rules\Password::defaults()],
      // Collaborator
      'collaborator.photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'collaborator.curriculum_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
      // ... (copiar todas as validações de 'collaborator' do store, ajustando unique)
      'collaborator.personal_email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('collaborators', 'personal_email')->ignore($collaborator->id)],
      'collaborator.business_email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('collaborators', 'business_email')->ignore($collaborator->id)],
      'collaborator.cpf' => ['nullable', 'string', 'max:14', Rule::unique('collaborators', 'cpf')->ignore($collaborator->id)],
      'collaborator.cnh' => ['nullable', 'string', 'max:20', Rule::unique('collaborators', 'cnh')->ignore($collaborator->id)],
      // ... (restante dos campos de collaborator)
      'collaborator.date_of_birth' => 'nullable|date',
      'collaborator.gender' => 'nullable|string|max:255',
      'collaborator.is_special_needs_person' => 'boolean',
      'collaborator.marital_status' => 'nullable|string|max:255',
      'collaborator.scholarity' => 'nullable|string|max:255',
      'collaborator.father_name' => 'nullable|string|max:255',
      'collaborator.mother_name' => 'nullable|string|max:255',
      'collaborator.nationality' => 'nullable|string|max:255',
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
      'collaborator.rg' => 'nullable|string|max:20',
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
    ]);

    $userData = $validated['user'];
    $collaboratorData = $validated['collaborator'];
    $addressData = $validated['address'] ?? null;

    // Uploads
    if ($request->hasFile('collaborator.photo_file')) {
      if ($collaborator->photo_url) {
        Storage::disk('public')->delete($collaborator->photo_url);
      }
      $collaboratorData['photo_url'] = $request->file('collaborator.photo_file')->store('collaborator_photos', 'public');
    }
    if ($request->hasFile('collaborator.curriculum_file')) {
      if ($collaborator->curriculum_url) {
        Storage::disk('public')->delete($collaborator->curriculum_url);
      }
      $collaboratorData['curriculum_url'] = $request->file('collaborator.curriculum_file')->store('collaborator_curriculums', 'public');
    }

    DB::transaction(function () use ($userData, $collaboratorData, $addressData, $user, $collaborator, $mainAddress) {
      // User
      if (!empty($userData['password'])) {
        $userData['password'] = Hash::make($userData['password']);
      } else {
        unset($userData['password']); // Não atualiza a senha se vazia
      }
      $user->update($userData);

      // Collaborator
      $collaborator->update($collaboratorData);

      // Address
      if ($addressData && !empty(array_filter($addressData))) { // Se algum dado de endereço foi enviado
        if ($mainAddress) {
          $mainAddress->update($addressData);
        } else {
          // Cria um novo endereço se não existia antes
          $user->addresses()->create($addressData);
        }
      } elseif ($mainAddress && (empty($addressData) || empty(array_filter($addressData)))) {
        // Se os campos de endereço foram esvaziados e existia um endereço, remove-o
        // $mainAddress->delete(); // Ou apenas desassocia, dependendo da regra
      }
    });

    return redirect()->route('collaborators.index')->with('success', 'Colaborador atualizado com sucesso.');
  }

  public function destroy(Collaborator $collaborator)
  {
    DB::transaction(function () use ($collaborator) {
      if ($collaborator->photo_url) {
        Storage::disk('public')->delete($collaborator->photo_url);
      }
      if ($collaborator->curriculum_url) {
        Storage::disk('public')->delete($collaborator->curriculum_url);
      }

      $user = $collaborator->user;
      // Excluir endereços associados ao usuário
      $user->addresses()->delete(); // Isso excluirá todos os endereços do usuário
      $collaborator->delete(); // Exclui o registro do colaborador
      $user->delete(); // Exclui o usuário (onDelete('cascade') na FK de collaborators cuidaria disso também)
    });

    return redirect()->route('collaborators.index')->with('success', 'Colaborador e dados associados excluídos com sucesso.');
  }
}

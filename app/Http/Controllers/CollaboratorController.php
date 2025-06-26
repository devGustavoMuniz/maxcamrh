<?php

namespace App\Http\Controllers;

use App\Actions\Collaborators\StoreCollaboratorAction;
use App\Actions\Collaborators\UpdateCollaboratorAction;
use App\Enums\UserRole;
use App\Http\Requests\StoreCollaboratorRequest;
use App\Http\Requests\UpdateCollaboratorRequest;
use App\Models\Client;
use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CollaboratorController extends Controller
{
    protected function formatCollaboratorData(Collaborator $collaborator): array
    {
        $collaborator->loadMissing(['user.addresses', 'client.user']);
        $mainAddress = $collaborator->user->addresses?->first();

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

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Collaborator::class);
        /** @var User $user */
        $user = $request->user();

        $collaborators = Collaborator::with(['user.addresses', 'client.user'])
            ->withFilters($request->only(['search', 'client_id']))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($collaborator) => [
                'id' => $collaborator->id,
                'photo_full_url' => $collaborator->photo_url ? Storage::disk('public')->url($collaborator->photo_url) : null,
                'user_name' => $collaborator->user->name,
                'user_email' => $collaborator->user->email,
                'department' => $collaborator->department,
                'position' => $collaborator->position,
                'client_name' => $collaborator->client?->user?->name ?? 'N/A',
                'city' => $collaborator->user->addresses?->first()->city ?? 'N/A',
            ]);

        $clientsForDropdown = collect();
        if ($user->role === UserRole::ADMIN || $user->role === UserRole::FRANCHISE) {
            $clientsQuery = Client::query()->with('user');
            if ($user->role === UserRole::FRANCHISE && $user->franchise) {
                $clientsQuery->where('franchise_id', $user->franchise->id);
            }
            $clientsForDropdown = $clientsQuery->get()->map(fn ($client) => [
                'id' => $client->id,
                'name' => $client->user->name,
            ]);
        }

        return Inertia::render('Collaborators/Index', [
            'collaborators' => $collaborators,
            'clients' => $clientsForDropdown,
            'filters' => $request->only(['search', 'client_id'])
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Collaborator::class);
        /** @var User $user */
        $user = Auth::user();

        $clients = [];
        if ($user->role === UserRole::ADMIN) {
            $clients = Client::with('user')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
        } elseif ($user->role === UserRole::FRANCHISE && $user->franchise) {
            $clients = Client::with('user')->where('franchise_id', $user->franchise->id)->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
        }

        return Inertia::render('Collaborators/Create', [
            'clients' => $clients,
            'auth_client_id' => $user->role === UserRole::CLIENT && $user->client ? $user->client->id : null,
        ]);
    }

    public function store(StoreCollaboratorRequest $request, StoreCollaboratorAction $storeCollaborator)
    {
        $storeCollaborator->execute($request);

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
        /** @var User $user */
        $user = Auth::user();
        $clients = [];

        if ($user->role === UserRole::ADMIN) {
            $clients = Client::with('user')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
        } elseif ($user->role === UserRole::FRANCHISE && $user->franchise) {
            $clients = Client::with('user')->where('franchise_id', $user->franchise->id)->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
        }

        return Inertia::render('Collaborators/Edit', [
            'collaborator_data' => $this->formatCollaboratorData($collaborator),
            'clients' => $clients,
        ]);
    }

    public function update(UpdateCollaboratorRequest $request, Collaborator $collaborator, UpdateCollaboratorAction $updateCollaborator)
    {
        $updateCollaborator->execute($collaborator, $request);

        return redirect()->route('collaborators.index')->with('success', 'Colaborador atualizado com sucesso.');
    }

    public function destroy(Collaborator $collaborator)
    {
        $this->authorize('delete', $collaborator);

        DB::transaction(fn() => $collaborator->delete());

        return redirect()->route('collaborators.index')->with('success', 'Colaborador e dados associados excluídos com sucesso.');
    }
}

<?php

namespace App\Http\Controllers\Collaborator;

use App\Actions\Collaborators\StoreCollaboratorAction;
use App\Actions\Collaborators\UpdateCollaboratorAction;
use App\Enums\UserRole;
use App\Http\Requests\StoreCollaboratorRequest;
use App\Http\Requests\UpdateCollaboratorRequest;
use App\Http\Resources\CollaboratorResource;
use App\Models\Client;
use App\Models\Collaborator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CollaboratorController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Collaborator::class);
        $user = $request->user();

        $collaborators = Collaborator::with(['user.address', 'client.user'])
            ->withFilters($request->only(['search', 'client_id']))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

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
            'collaborators' => CollaboratorResource::collection($collaborators),

            'clients' => $clientsForDropdown,
            'filters' => $request->only(['search', 'client_id'])
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize('create', Collaborator::class);
        $user = Auth::user();

        $clients = [];
        if ($user->role === UserRole::ADMIN) {
            $clients = Client::with('user')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
        }
        if ($user->role === UserRole::FRANCHISE && $user->franchise) {
            $clients = Client::with('user')->where('franchise_id', $user->franchise->id)->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
        }

        return Inertia::render('Collaborators/Create', [
            'clients' => $clients,
            'auth_client_id' => $user->role === UserRole::CLIENT && $user->client ? $user->client->id : null,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreCollaboratorRequest $request, StoreCollaboratorAction $storeCollaborator): RedirectResponse
    {
        $storeCollaborator->execute($request);

        return redirect()->route('collaborators.index')->with('success', 'Colaborador criado com sucesso.');
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(Collaborator $collaborator): Response
    {
        $this->authorize('update', $collaborator);
        $user = Auth::user();
        $clients = [];

        if ($user->role === UserRole::ADMIN) {
            $clients = Client::with('user')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
        }
        if ($user->role === UserRole::FRANCHISE && $user->franchise) {
            $clients = Client::with('user')->where('franchise_id', $user->franchise->id)->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->user->name . ($c->cnpj ? ' (CNPJ: ' . $c->cnpj . ')' : '')]);
        }

        return Inertia::render('Collaborators/Edit', [
            'collaborator_data' => (new CollaboratorResource($collaborator->loadMissing(['user.address', 'client.user'])))->resolve(),
            'clients' => $clients,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateCollaboratorRequest $request, Collaborator $collaborator, UpdateCollaboratorAction $updateCollaborator): RedirectResponse
    {
        $updateCollaborator->execute($collaborator, $request);

        return redirect()->route('collaborators.index')->with('success', 'Colaborador atualizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Collaborator $collaborator): RedirectResponse
    {
        $this->authorize('delete', $collaborator);

        DB::transaction(fn() => $collaborator->delete());

        return redirect()->route('collaborators.index')->with('success', 'Colaborador e dados associados excluídos com sucesso.');
    }
}

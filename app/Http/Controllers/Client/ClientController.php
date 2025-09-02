<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Actions\Clients\StoreClientAction;
use App\Actions\Clients\UpdateClientAction;
use App\Enums\UserRole;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Franchise;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ClientController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Client::class);
        $user = $request->user();

        $clients = Client::with(['user', 'franchise.user'])
            ->withFilters($request->only(['search', 'franchise_id']))
            ->orderByDesc('clients.created_at')
            ->paginate(10)
            ->withQueryString();

        $franchisesForDropdown = collect();
        if ($user->role === UserRole::ADMIN) {
            $franchisesForDropdown = Franchise::query()->with('user')->get()->map(fn ($franchise) => [
                'id' => $franchise->id,
                'name' => $franchise->user->name,
            ]);
        }

        return Inertia::render('Clients/Index', [
            'clients' => ClientResource::collection($clients),
            'filters' => $request->only(['search', 'franchise_id']),
            'franchises' => $franchisesForDropdown,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize('create', Client::class);

        $franchises = [];
        if (Auth::user()->role === UserRole::ADMIN) {
            $franchises = Franchise::with('user')
                ->get()
                ->map(fn($franchise) => [
                    'id' => $franchise->id,
                    'name' => $franchise->user?->name ?? ('Franquia ID: ' . $franchise->id),
                ]);
        }

        return Inertia::render('Clients/Create', [
            'franchises' => $franchises,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreClientRequest $request, StoreClientAction $storeClient): RedirectResponse
    {
        $storeClient($request);

        return redirect()->route('clients.index')->with('success', 'Cliente criado com sucesso.');
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(Client $client): Response
    {
        $this->authorize('update', $client);

        $franchises = [];
        if (Auth::user()->role === UserRole::ADMIN) {
            $franchises = Franchise::with('user')
                ->get()
                ->map(fn($franchise) => [
                    'id' => $franchise->id,
                    'name' => $franchise->user?->name ?? ('Franquia ID: ' . $franchise->id),
                ]);
        }

        return Inertia::render('Clients/Edit', [
            'client_data' => (new ClientResource($client->loadMissing(['user', 'franchise.user'])))->resolve(),
            'franchises' => $franchises,
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client, UpdateClientAction $updateClient)
    {
        $updateClient($client, $request);

        return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente e usuário associado excluídos com sucesso.');
    }
}

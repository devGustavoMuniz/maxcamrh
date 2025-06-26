<?php

namespace App\Http\Controllers;

use App\Actions\Clients\StoreClientAction;
use App\Actions\Clients\UpdateClientAction;
use App\Enums\UserRole;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    protected function formatClientData(Client $client): array
    {
        $client->loadMissing(['user', 'franchise.user']);
        return [
            'id' => $client->id,
            'cnpj' => $client->cnpj,
            'test_number' => $client->test_number ?: 'N/A',
            'contract_end_date' => $client->contract_end_date ? $client->contract_end_date->format('d/m/Y') : 'N/A',
            'contract_end_date_form' => $client->contract_end_date ? $client->contract_end_date->format('Y-m-d') : null,
            'is_monthly_contract' => (bool) $client->is_monthly_contract,
            'phone' => $client->phone ?: 'N/A',
            'logo_url' => $client->logo_url,
            'logo_full_url' => $client->logo_url ? Storage::disk('public')->url($client->logo_url) : null,
            'franchise_id' => $client->franchise_id,
            'franchise_name' => $client->franchise && $client->franchise->user ? $client->franchise->user->name : 'N/A',
            'created_at' => $client->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $client->updated_at->format('d/m/Y H:i:s'),
            'user' => $client->user ? [
                'id' => $client->user->id,
                'name' => $client->user->name,
                'email' => $client->user->email,
                'role' => $client->user->role instanceof UserRole ? $client->user->role->value : $client->user->role,
                'user_created_at' => $client->user->created_at->format('d/m/Y H:i:s'),
            ] : null,
        ];
    }

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Client::class);
        $user = $request->user();

        $clients = Client::with(['user', 'franchise.user'])
            ->withFilters($request->only(['search', 'franchise_id']))
            ->orderByDesc('clients.created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($client) => [
                'id' => $client->id,
                'cnpj' => $client->cnpj,
                'phone' => $client->phone,
                'user_name' => $client->user->name,
                'user_email' => $client->user->email,
                'franchise_name' => $client->franchise?->user?->name ?? 'N/A',
                'logo_full_url' => $client->logo_url ? Storage::disk('public')->url($client->logo_url) : null,
            ]);

        $franchisesForDropdown = collect();
        if ($user->role === UserRole::ADMIN) {
            $franchisesForDropdown = Franchise::query()->with('user')->get()->map(fn ($franchise) => [
                'id' => $franchise->id,
                'name' => $franchise->user->name,
            ]);
        }

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search', 'franchise_id']),
            'franchisees' => $franchisesForDropdown,
        ]);
    }

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

    public function store(StoreClientRequest $request, StoreClientAction $storeClient)
    {
        $storeClient->execute(
            $request->validated(),
            $request->file('logo_file'),
            $request->user()
        );

        return redirect()->route('clients.index')->with('success', 'Cliente criado com sucesso.');
    }

    public function show(Client $client): Response
    {
        $this->authorize('view', $client);
        return Inertia::render('Clients/Show', [
            'client_data' => $this->formatClientData($client),
        ]);
    }

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
            'client_data' => $this->formatClientData($client),
            'franchises' => $franchises,
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client, UpdateClientAction $updateClient)
    {
        $updateClient->execute(
            $client,
            $request->validated(),
            $request->file('logo_file'),
            $request->user()
        );

        return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente e usuário associado excluídos com sucesso.');
    }
}

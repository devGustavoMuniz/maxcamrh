<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Collaborator;
use App\Models\User;
use App\Models\Franchise;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
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

      $user = Auth::user();
      $query = Client::with(['user.addresses', 'franchise.user']);

      if ($request->filled('franchise_id')) {
        $query->where('franchise_id', $request->input('franchise_id'));
      }

      if ($request->filled('search')) {
        $searchTerm = $request->input('search');
        $query->where(function ($q) use ($searchTerm) {
          $q->whereHas('user', function ($uq) use ($searchTerm) {
            $lowerSearchTerm = strtolower($searchTerm);
            $uq->whereRaw('LOWER(name) LIKE ?', ["%{$lowerSearchTerm}%"])
              ->orWhereRaw('LOWER(email) LIKE ?', ["%{$lowerSearchTerm}%"]);
          });
        });
      }

      if ($user->role === UserRole::CLIENT) {
        if ($user->client) {
          $query->where('client_id', $user->client->id);
        } else {
          $query->whereRaw('1 = 0');
        }
      }

      $franchisesForDropdown = collect();

      if ($user->role === UserRole::ADMIN) {
        $franchisesQuery = Franchise::query()->with('user');

        $franchisesForDropdown = $franchisesQuery->get()->map(function ($client) {
          return [
            'id' => $client->id,
            'name' => $client->user->name,
          ];
        });
      }

        $clients = $query->orderByDesc('clients.created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($client) => [
                'id' => $client->id,
                'cnpj' => $client->cnpj,
                'phone' => $client->phone,
                'user_name' => $client->user->name,
                'user_email' => $client->user->email,
                'franchise_name' => $client->franchise && $client->franchise->user ? $client->franchise->user->name : 'N/A',
                'logo_full_url' => $client->logo_url ? Storage::disk('public')->url($client->logo_url) : null,
            ]);

        // dd($clients); // Para depuração, agora deveria mostrar dados

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search', 'client_id']),
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
          'name' => $franchise->user ? $franchise->user->name : ('Franquia ID: ' . $franchise->id),
        ]);
    }

    return Inertia::render('Clients/Create', [
      'franchises' => $franchises,
    ]);
  }

  public function store(Request $request)
  {
    $this->authorize('create', Client::class);

    $loggedInUser = Auth::user();
    $validationRules = [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'cnpj' => 'required|string|max:255|unique:clients,cnpj',
      'test_number' => 'nullable|string|max:255',
      'contract_end_date' => 'nullable|date',
      'is_monthly_contract' => 'required|boolean',
      'phone' => 'nullable|string|max:255',
      'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    if ($loggedInUser->role === UserRole::ADMIN) {
      $validationRules['franchise_id'] = 'nullable|exists:franchises,id';
    }

    $validatedData = $request->validate($validationRules);

    $logoPath = null;
    if ($request->hasFile('logo_file')) {
      $logoPath = $request->file('logo_file')->store('client_logos', 'public');
    }

    DB::transaction(function () use ($validatedData, $logoPath, $loggedInUser) {
      $clientUser = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
        'role' => UserRole::CLIENT->value,
        'email_verified_at' => now(),
      ]);

      $clientData = [
        'cnpj' => $validatedData['cnpj'],
        'test_number' => $validatedData['test_number'],
        'contract_end_date' => $validatedData['contract_end_date'],
        'is_monthly_contract' => $validatedData['is_monthly_contract'],
        'phone' => $validatedData['phone'],
        'logo_url' => $logoPath,
      ];

      if ($loggedInUser->role === UserRole::ADMIN) {
        $clientData['franchise_id'] = $validatedData['franchise_id'];
      } elseif ($loggedInUser->role === UserRole::FRANCHISE) {
        if (!$loggedInUser->franchise) {
          abort(403, 'Usuário franqueado não está associado a uma franquia.');
        }
        $clientData['franchise_id'] = $loggedInUser->franchise->id;
      } else {
        abort(403, 'Ação não permitida para este papel.');
      }

      $clientUser->client()->create($clientData);
    });

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
          'name' => $franchise->user ? $franchise->user->name : ('Franquia ID: ' . $franchise->id),
        ]);
    }
    $client->loadMissing('user');

    return Inertia::render('Clients/Edit', [
      'client_data' => $this->formatClientData($client),
      'franchises' => $franchises,
    ]);
  }

  public function update(Request $request, Client $client)
  {
    $this->authorize('update', $client);

    $clientUser = $client->user;
    $loggedInUser = Auth::user();

    $validationRules = [
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($clientUser->id)],
      'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
      'cnpj' => ['required', 'string', 'max:255', Rule::unique('clients', 'cnpj')->ignore($client->id)],
      'test_number' => 'nullable|string|max:255',
      'contract_end_date' => 'nullable|date',
      'is_monthly_contract' => 'required|boolean',
      'phone' => 'nullable|string|max:255',
      'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    if ($loggedInUser->role === UserRole::ADMIN) {
      $validationRules['franchise_id'] = 'nullable|exists:franchises,id';
    }

    $validatedData = $request->validate($validationRules);

    $logoPath = $client->logo_url;
    if ($request->hasFile('logo_file')) {
      if ($client->logo_url) {
        Storage::disk('public')->delete($client->logo_url);
      }
      $logoPath = $request->file('logo_file')->store('client_logos', 'public');
    }

    DB::transaction(function () use ($request, $validatedData, $clientUser, $client, $logoPath, $loggedInUser) {
      $userDataToUpdate = [
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
      ];
      if ($request->filled('password')) {
        $userDataToUpdate['password'] = Hash::make($validatedData['password']);
      }
      $clientUser->update($userDataToUpdate);

      $clientDataToUpdate = [
        'cnpj' => $validatedData['cnpj'],
        'test_number' => $validatedData['test_number'],
        'contract_end_date' => $validatedData['contract_end_date'],
        'is_monthly_contract' => $validatedData['is_monthly_contract'],
        'phone' => $validatedData['phone'],
        'logo_url' => $logoPath,
      ];

      if ($loggedInUser->role === UserRole::ADMIN) {
        $clientDataToUpdate['franchise_id'] = $validatedData['franchise_id'];
      }

      $client->update($clientDataToUpdate);
    });

    return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
  }

  public function destroy(Client $client)
  {
    $this->authorize('delete', $client);

    DB::transaction(function () use ($client) {
      if ($client->logo_url) {
        Storage::disk('public')->delete($client->logo_url);
      }
      $user = $client->user;

      $client->delete();
      if ($user) {
        $user->delete();
      }
    });

    return redirect()->route('clients.index')->with('success', 'Cliente e usuário associado excluídos com sucesso.');
  }
}

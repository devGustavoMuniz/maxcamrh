<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <-- Adicione esta linha
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
  // Helper para formatar dados do cliente para a view
  protected function formatClientData(Client $client): array
  {
    $client->load('user'); // Garante que o usuário está carregado
    return [
      'id' => $client->id,
      'cnpj' => $client->cnpj,
      'test_number' => $client->test_number ?: 'N/A',
      'contract_end_date' => $client->contract_end_date ? $client->contract_end_date->format('d/m/Y') : 'N/A',
      'contract_end_date_form' => $client->contract_end_date ? $client->contract_end_date->format('Y-m-d') : null, // Para formulários
      'is_monthly_contract' => $client->is_monthly_contract,
      'phone' => $client->phone ?: 'N/A',
      'logo_url' => $client->logo_url, // O caminho relativo salvo no BD
      'logo_full_url' => $client->logo_url ? Storage::disk('public')->url($client->logo_url) : null, // URL completa para exibição
      'created_at' => $client->created_at->format('d/m/Y H:i:s'),
      'updated_at' => $client->updated_at->format('d/m/Y H:i:s'),
      'user' => $client->user ? [
        'id' => $client->user->id,
        'name' => $client->user->name,
        'email' => $client->user->email,
        'role' => $client->user->role->value,
        'user_created_at' => $client->user->created_at->format('d/m/Y H:i:s'),
      ] : null,
    ];
  }


  public function index(): Response
  {
    $clients = Client::with('user')
      ->orderByDesc('created_at')
      ->paginate(10)
      ->through(fn ($client) => [ // Usando map para transformar
        'id' => $client->id,
        'cnpj' => $client->cnpj,
        'is_monthly_contract' => $client->is_monthly_contract,
        'phone' => $client->phone,
        'user_name' => $client->user->name,
        'user_email' => $client->user->email,
        'logo_full_url' => $client->logo_url ? Storage::disk('public')->url($client->logo_url) : null,
      ]);

    return Inertia::render('Clients/Index', [
      'clients' => $clients,
    ]);
  }

  public function create(): Response
  {
    return Inertia::render('Clients/Create');
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'cnpj' => 'required|string|max:255|unique:clients,cnpj',
      'test_number' => 'nullable|string|max:255',
      'contract_end_date' => 'nullable|date',
      'is_monthly_contract' => 'required|boolean',
      'phone' => 'nullable|string|max:255',
      'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Alterado de logo_url para logo_file
    ]);

    $logoPath = null;
    if ($request->hasFile('logo_file')) {
      $logoPath = $request->file('logo_file')->store('client_logos', 'public'); // Salva em storage/app/public/client_logos
    }

    DB::transaction(function () use ($request, $logoPath) {
      $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password')),
        'role' => UserRole::CLIENT->value,
        'email_verified_at' => now(),
      ]);

      $user->client()->create([
        'cnpj' => $request->input('cnpj'),
        'test_number' => $request->input('test_number'),
        'contract_end_date' => $request->input('contract_end_date'),
        'is_monthly_contract' => $request->input('is_monthly_contract'),
        'phone' => $request->input('phone'),
        'logo_url' => $logoPath, // Salva o caminho do arquivo
      ]);
    });

    return redirect()->route('clients.index')->with('success', 'Cliente criado com sucesso.');
  }

  public function show(Client $client): Response
  {
    return Inertia::render('Clients/Show', [
      'client_data' => $this->formatClientData($client),
    ]);
  }

  public function edit(Client $client): Response
  {
    return Inertia::render('Clients/Edit', [
      'client_data' => $this->formatClientData($client),
    ]);
  }

  public function update(Request $request, Client $client)
  {
    $user = $client->user;

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
      'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
      'cnpj' => ['required', 'string', 'max:255', Rule::unique('clients', 'cnpj')->ignore($client->id)],
      'test_number' => 'nullable|string|max:255',
      'contract_end_date' => 'nullable|date',
      'is_monthly_contract' => 'required|boolean',
      'phone' => 'nullable|string|max:255',
      'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Para o novo upload
    ]);

    $logoPath = $client->logo_url; // Mantém o logo existente por padrão

    if ($request->hasFile('logo_file')) {
      // Exclui o logo antigo se existir
      if ($client->logo_url) {
        Storage::disk('public')->delete($client->logo_url);
      }
      // Salva o novo logo
      $logoPath = $request->file('logo_file')->store('client_logos', 'public');
    }

    DB::transaction(function () use ($request, $user, $client, $logoPath) {
      $userData = [
        'name' => $request->input('name'),
        'email' => $request->input('email'),
      ];
      if ($request->filled('password')) {
        $userData['password'] = Hash::make($request->input('password'));
      }
      $user->update($userData);

      $clientData = $request->except(['name', 'email', 'password', 'password_confirmation', 'logo_file']);
      $clientData['logo_url'] = $logoPath; // Atualiza com o novo caminho do logo (ou o antigo se não mudou)
      $client->update($clientData);
    });

    return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
  }

  public function destroy(Client $client)
  {
    DB::transaction(function () use ($client) {
      // Exclui o logo se existir
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

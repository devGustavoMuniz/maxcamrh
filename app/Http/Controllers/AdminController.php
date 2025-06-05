<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response; // Mantido, embora alguns métodos retornem RedirectResponse

class AdminController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response // Modificado para aceitar Request
  {
    $this->authorize('viewAny', User::class);

    $searchTerm = $request->input('search'); // Captura o termo de busca

    $query = User::where('role', UserRole::ADMIN->value);

    if ($searchTerm) {
      $query->where(function ($q) use ($searchTerm) {
        $q->where('name', 'like', "%{$searchTerm}%")
          ->orWhere('email', 'like', "%{$searchTerm}%");
      });
    }

    $admins = $query->orderBy('name')
      ->paginate(10)
      ->withQueryString() // Mantém o search param na paginação
      ->through(fn ($user) => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'created_at' => $user->created_at->toFormattedDateString(),
      ]);

    return Inertia::render('Admins/Index', [
      'admins' => $admins,
      'filters' => $request->only(['search']), // Passa o filtro de volta para a view
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(): Response
  {
    // Autoriza se o usuário logado pode criar um User
    // A UserPolicy@create decidirá
    $this->authorize('create', User::class);

    return Inertia::render('Admins/Create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) // Tipo de retorno pode ser Illuminate\Http\RedirectResponse
  {
    // Autoriza se o usuário logado pode criar um User
    // A UserPolicy@create decidirá
    $this->authorize('create', User::class);

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => UserRole::ADMIN->value,
      'email_verified_at' => now(),
    ]);

    return redirect()->route('admins.index')->with('success', 'Administrador criado com sucesso.');
  }

  /**
   * Display the specified resource.
   */
  public function show(User $admin): Response
  {
    // Autoriza se o usuário logado pode ver este $admin específico
    // A UserPolicy@view decidirá
    $this->authorize('view', $admin);

    // Adicionada verificação para garantir que apenas admins sejam mostrados por este controller
    if ($admin->role !== UserRole::ADMIN->value) {
      abort(404); // Ou 403, mas 404 se a rota /admins/{id} não deveria achar não-admins
    }

    return Inertia::render('Admins/Show', [
      'admin_user' => [
        'id' => $admin->id,
        'name' => $admin->name,
        'email' => $admin->email,
        'role' => $admin->role->value, // Supondo que UserRole é um Enum com um atributo 'value'
        'created_at' => $admin->created_at->format('d/m/Y H:i:s'),
        'email_verified_at' => $admin->email_verified_at ? $admin->email_verified_at->format('d/m/Y H:i:s') : 'Não verificado',
      ],
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(User $admin) // Tipo de retorno pode ser Response
  {
    // Autoriza se o usuário logado pode atualizar este $admin
    // A UserPolicy@update decidirá (convencionalmente, 'edit' usa a permissão de 'update')
    $this->authorize('update', $admin);

    // Adicionada verificação para garantir que apenas admins sejam editados por este controller
    if ($admin->role !== UserRole::ADMIN->value) {
      abort(404);
    }

    return Inertia::render('Admins/Edit', [
      'admin_user' => [
        'id' => $admin->id,
        'name' => $admin->name,
        'email' => $admin->email,
      ],
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, User $admin) // Tipo de retorno pode ser Illuminate\Http\RedirectResponse
  {
    // Autoriza se o usuário logado pode atualizar este $admin
    // A UserPolicy@update decidirá
    $this->authorize('update', $admin);

    // Adicionada verificação para garantir que apenas admins sejam atualizados por este controller
    if ($admin->role !== UserRole::ADMIN->value) {
      abort(403); // Não deveria ter chegado aqui se o edit já barrou, mas como defesa
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
      'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
    ]);

    $admin->name = $request->name;
    $admin->email = $request->email;

    if ($request->filled('password')) {
      $admin->password = Hash::make($request->password);
    }

    $admin->save();

    return redirect()->route('admins.index')->with('success', 'Administrador atualizado com sucesso.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(User $admin) // Tipo de retorno pode ser Illuminate\Http\RedirectResponse
  {
    // Autoriza se o usuário logado pode deletar este $admin
    // A UserPolicy@delete decidirá (e deve incluir a lógica de não se auto-deletar)
    $this->authorize('delete', $admin);

    // Esta verificação específica do controller pode ser mantida como uma camada extra,
    // ou totalmente delegada à Policy. Se a policy for bem específica sobre
    // quem um admin pode deletar (ex: apenas outros admins), esta checagem de role
    // pode ser redundante ou parte da lógica da policy.
    if ($admin->role !== UserRole::ADMIN->value) {
      // Se a UserPolicy@delete já não cobrir que um admin só pode deletar outro admin (ou qualquer user),
      // esta linha garante que este controller só delete admins.
      // Se a UserPolicy@delete for genérica para qualquer user, esta linha é uma restrição do AdminController.
      abort(403, 'Este usuário não é um administrador e não pode ser excluído por esta rota.');
    }

    // A lógica de não se auto-deletar deve estar na UserPolicy@delete.
    // Se a policy retornar false para $user->id === $model->id, a exceção de autorização
    // será lançada antes de chegar aqui. A linha abaixo seria então desnecessária.
    // if ($admin->id === auth()->id()) {
    //   return redirect()->route('admins.index')->with('error', 'Você não pode excluir sua própria conta de administrador.');
    // }

    $admin->delete();

    return redirect()->route('admins.index')->with('success', 'Administrador excluído com sucesso.');
  }
}

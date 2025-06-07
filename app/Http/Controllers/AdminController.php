<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
  public function index(Request $request): Response
  {
    $this->authorize('viewAny', User::class);

    $searchTerm = $request->input('search');

    $query = User::where('role', UserRole::ADMIN);

    if ($searchTerm) {
      $query->where(function ($q) use ($searchTerm) {
        $q->where('name', 'like', "%{$searchTerm}%")
          ->orWhere('email', 'like', "%{$searchTerm}%");
      });
    }

    $admins = $query->orderBy('name')
      ->paginate(10)
      ->withQueryString()
      ->through(fn ($user) => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'created_at' => $user->created_at->toFormattedDateString(),
      ]);

    return Inertia::render('Admins/Index', [
      'admins' => $admins,
      'filters' => $request->only(['search']),
    ]);
  }

  public function create(): Response
  {
    $this->authorize('create', User::class);

    return Inertia::render('Admins/Create');
  }

  public function store(Request $request)
  {
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
      'role' => UserRole::ADMIN,
      'email_verified_at' => now(),
    ]);

    return redirect()->route('admins.index')->with('success', 'Administrador criado com sucesso.');
  }

  public function show(User $admin): Response
  {
    $this->authorize('view', $admin);

    if ($admin->role !== UserRole::ADMIN) {
      abort(404);
    }

    return Inertia::render('Admins/Show', [
      'admin_user' => [
        'id' => $admin->id,
        'name' => $admin->name,
        'email' => $admin->email,
        'role' => $admin->role->value,
        'created_at' => $admin->created_at->format('d/m/Y H:i:s'),
        'email_verified_at' => $admin->email_verified_at ? $admin->email_verified_at->format('d/m/Y H:i:s') : 'Não verificado',
      ],
    ]);
  }

  public function edit(User $admin)
  {
    $this->authorize('update', $admin);

    if ($admin->role !== UserRole::ADMIN) {
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

  public function update(Request $request, User $admin)
  {
    $this->authorize('update', $admin);

    if ($admin->role !== UserRole::ADMIN) {
      abort(403);
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

  public function destroy(User $admin)
  {
    $this->authorize('delete', $admin);

    if ($admin->role !== UserRole::ADMIN) {
      abort(403, 'Este usuário não é um administrador e não pode ser excluído por esta rota.');
    }

    $admin->delete();

    return redirect()->route('admins.index')->with('success', 'Administrador excluído com sucesso.');
  }
}

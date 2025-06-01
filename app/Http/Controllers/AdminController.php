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
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
      return Inertia::render('Admins/Index', [
        'admins' => User::where('role', UserRole::ADMIN->value)
          ->orderBy('name')
          ->paginate(10)
          ->withQueryString()
          ->through(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->toFormattedDateString(),
          ]),
      ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
      return Inertia::render('Admins/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
      if ($admin->role !== UserRole::ADMIN->value) {
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $admin)
    {
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
    public function update(Request $request, User $admin)
    {
      if ($admin->role !== UserRole::ADMIN->value) {
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
      if ($admin->role !== UserRole::ADMIN->value) {
        abort(403);
      }

      if ($admin->id === auth()->id()) {
        return redirect()->route('admins.index')->with('error', 'Você não pode excluir sua própria conta de administrador.');
      }

      $admin->delete();

      return redirect()->route('admins.index')->with('success', 'Administrador excluído com sucesso.');
    }
}

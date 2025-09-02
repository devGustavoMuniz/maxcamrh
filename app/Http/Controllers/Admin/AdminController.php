<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Admins\StoreAdminAction;
use App\Actions\Admins\UpdateAdminAction;
use App\Enums\UserRole;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $admins = User::where('role', UserRole::ADMIN)
            ->withFilters($request->only('search'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admins/Index', [
            'admins' => UserResource::collection($admins),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize('create', User::class);
        return Inertia::render('Admins/Create');
    }

    public function store(StoreAdminRequest $request, StoreAdminAction $storeAdmin): RedirectResponse
    {
        $storeAdmin->execute($request);

        return redirect()->route('admins.index')->with('success', 'Administrador criado com sucesso.');
    }

    public function edit(User $admin): Response
    {
        $this->authorize('update', $admin);

        if ($admin->role !== UserRole::ADMIN) {
            abort(404);
        }

        return Inertia::render('Admins/Edit', [
            'admin_user' => (new UserResource($admin))->resolve(),
        ]);
    }

    public function update(UpdateAdminRequest $request, User $admin, UpdateAdminAction $updateAdmin): RedirectResponse
    {
        $updateAdmin->execute($admin, $request);

        return redirect()->route('admins.index')->with('success', 'Administrador atualizado com sucesso.');
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(User $admin): RedirectResponse
    {
        $this->authorize('delete', $admin);

        if ($admin->role !== UserRole::ADMIN) {
            abort(403, 'Este usuário não é um administrador e não pode ser excluído por esta rota.');
        }

        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Administrador excluído com sucesso.');
    }
}

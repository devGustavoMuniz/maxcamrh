<?php

namespace App\Http\Controllers;

use App\Actions\Franchises\StoreFranchiseAction;
use App\Actions\Franchises\UpdateFranchiseAction;
use App\Http\Requests\StoreFranchiseRequest;
use App\Http\Requests\UpdateFranchiseRequest;
use App\Http\Resources\FranchiseResource;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FranchiseController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Franchise::class);

        $franchises = Franchise::with('user')
            ->withFilters($request->only('search'))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Franchises/Index', [
            'franchises' => FranchiseResource::collection($franchises),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Franchise::class);
        return Inertia::render('Franchises/Create');
    }

    public function store(StoreFranchiseRequest $request, StoreFranchiseAction $storeFranchise)
    {
        $storeFranchise->execute(
            $request->validated(),
            $request->file('document_file')
        );

        return redirect()->route('franchises.index')->with('success', 'Franqueado e usuário associado criados com sucesso.');
    }

    public function edit(Franchise $franchise): Response
    {
        $this->authorize('update', $franchise);
        return Inertia::render('Franchises/Edit', [
            'franchise_data' => new FranchiseResource($franchise->load('user')),
        ]);
    }

    public function update(UpdateFranchiseRequest $request, Franchise $franchise, UpdateFranchiseAction $updateFranchise)
    {
        $updateFranchise->execute(
            $franchise,
            $request->validated(),
            $request->file('document_file')
        );

        return redirect()->route('franchises.index')->with('success', 'Franqueado atualizado com sucesso.');
    }

    public function destroy(Franchise $franchise)
    {
        $this->authorize('delete', $franchise);

        $franchise->delete();

        return redirect()->route('franchises.index')->with('success', 'Franqueado e usuário associado excluídos com sucesso.');
    }
}

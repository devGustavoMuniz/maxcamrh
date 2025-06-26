<?php

namespace App\Http\Controllers;

use App\Actions\Franchises\StoreFranchiseAction;
use App\Actions\Franchises\UpdateFranchiseAction;
use App\Http\Requests\StoreFranchiseRequest;
use App\Http\Requests\UpdateFranchiseRequest;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FranchiseController extends Controller
{
    protected function formatFranchiseData(Franchise $franchise): array
    {
        $franchise->load('user');
        return [
            'id' => $franchise->id,
            'user_id' => $franchise->user_id,
            'maxcam_email' => $franchise->maxcam_email,
            'cnpj' => $franchise->cnpj,
            'max_client' => $franchise->max_client,
            'contract_start_date' => $franchise->contract_start_date ? $franchise->contract_start_date->format('d/m/Y') : 'N/A',
            'contract_start_date_form' => $franchise->contract_start_date ? $franchise->contract_start_date->format('Y-m-d') : null,
            'actuation_region' => $franchise->actuation_region,
            'document_url' => $franchise->document_url,
            'document_full_url' => $franchise->document_url ? Storage::disk('public')->url($franchise->document_url) : null,
            'observations' => $franchise->observations,
            'created_at' => $franchise->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $franchise->updated_at->format('d/m/Y H:i:s'),
            'user' => $franchise->user ? [
                'id' => $franchise->user->id,
                'name' => $franchise->user->name,
                'email' => $franchise->user->email,
            ] : null,
        ];
    }

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Franchise::class);

        $franchises = Franchise::with('user')
            ->withFilters($request->only('search'))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($franchise) => [
                'id' => $franchise->id,
                'cnpj' => $franchise->cnpj,
                'maxcam_email' => $franchise->maxcam_email,
                'actuation_region' => $franchise->actuation_region,
                'user_name' => $franchise->user?->name,
                'user_email' => $franchise->user?->email,
            ]);

        return Inertia::render('Franchises/Index', [
            'franchises' => $franchises,
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

    public function show(Franchise $franchise): Response
    {
        $this->authorize('view', $franchise);
        return Inertia::render('Franchises/Show', [
            'franchise_data' => $this->formatFranchiseData($franchise),
        ]);
    }

    public function edit(Franchise $franchise): Response
    {
        $this->authorize('update', $franchise);
        return Inertia::render('Franchises/Edit', [
            'franchise_data' => $this->formatFranchiseData($franchise),
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

<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Franchise;
use App\Enums\UserRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReportController extends Controller
{
    /**
     * RF013 - Relatório: Clientes por Franqueado
     * Exibe a tela de geração do relatório de clientes por franqueado
     *
     * @throws AuthorizationException
     */
    public function clientsByFranchiseIndex(Request $request): InertiaResponse
    {
        $user = $request->user();

        // Apenas administradores podem acessar este relatório
        if ($user->role !== UserRole::ADMIN) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar este relatório.');
        }

        $franchises = Franchise::with('user')
            ->get()
            ->map(fn($franchise) => [
                'id' => $franchise->id,
                'name' => $franchise->user?->name ?? ('Franquia ID: ' . $franchise->id),
            ]);

        return Inertia::render('Reports/ClientsByFranchise', [
            'franchises' => $franchises,
        ]);
    }

    /**
     * RF013 - Relatório: Clientes por Franqueado
     * Gera o relatório de clientes por franqueado
     *
     * @throws AuthorizationException
     */
    public function clientsByFranchiseGenerate(Request $request)
    {
        $user = $request->user();

        // Apenas administradores podem acessar este relatório
        if ($user->role !== UserRole::ADMIN) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar este relatório.');
        }

        $request->validate([
            'franchise_id' => 'required|exists:franchises,id',
            'format' => 'required|in:pdf,csv',
        ]);

        $franchiseId = $request->input('franchise_id');
        $format = $request->input('format');

        $franchise = Franchise::with('user')->findOrFail($franchiseId);

        $clients = Client::with(['user', 'franchise.user'])
            ->where('franchise_id', $franchiseId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Se não houver clientes, retornar aviso
        if ($clients->isEmpty()) {
            return back()->with('warning', 'Nenhum cliente encontrado para o franqueado selecionado.');
        }

        switch ($format) {
            case 'csv':
                return $this->generateClientsCsv($clients, $franchise);

            case 'pdf':
                return $this->generateClientsPdf($clients, $franchise);
        }
    }

    /**
     * Exibe a tela de geração do relatório de colaboradores por cliente
     *
     * @throws AuthorizationException
     */
    public function collaboratorsByClientIndex(Request $request): InertiaResponse
    {
        $user = $request->user();

        // Administradores e franqueados podem acessar
        if (!in_array($user->role, [UserRole::ADMIN, UserRole::FRANCHISE])) {
            abort(403, 'Acesso negado.');
        }

        $clientsQuery = Client::with(['user', 'franchise.user']);

        // Franqueados só veem seus próprios clientes
        if ($user->role === UserRole::FRANCHISE) {
            $clientsQuery->where('franchise_id', $user->franchise?->id);
        }

        $clients = $clientsQuery->get()->map(fn($client) => [
            'id' => $client->id,
            'name' => $client->user?->name ?? ('Cliente ID: ' . $client->id),
        ]);

        return Inertia::render('Reports/CollaboratorsByClient', [
            'clients' => $clients,
        ]);
    }

    /**
     * Gera o relatório de colaboradores por cliente
     *
     * @throws AuthorizationException
     */
    public function collaboratorsByClientGenerate(Request $request)
    {
        $user = $request->user();

        // Administradores e franqueados podem acessar
        if (!in_array($user->role, [UserRole::ADMIN, UserRole::FRANCHISE])) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'format' => 'required|in:pdf,csv',
        ]);

        $clientId = $request->input('client_id');
        $format = $request->input('format');

        $client = Client::with(['user', 'franchise.user'])->findOrFail($clientId);

        // Verificar se franqueado tem acesso ao cliente
        if ($user->role === UserRole::FRANCHISE && $client->franchise_id !== $user->franchise?->id) {
            abort(403, 'Acesso negado ao cliente selecionado.');
        }

        $collaborators = Collaborator::with(['user', 'client.user'])
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Se não houver colaboradores, retornar aviso
        if ($collaborators->isEmpty()) {
            return back()->with('warning', 'Nenhum colaborador encontrado para o cliente selecionado.');
        }

        switch ($format) {
            case 'csv':
                return $this->generateCollaboratorsCsv($collaborators, $client);

            case 'pdf':
                return $this->generateCollaboratorsPdf($collaborators, $client);
        }
    }

    /**
     * Gera CSV de clientes
     */
    private function generateClientsCsv($clients, $franchise)
    {
        $filename = 'clientes_' . str_replace(' ', '_', strtolower($franchise->user->name)) . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function() use ($clients, $franchise) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Cabeçalho
            fputcsv($file, [
                'Nome',
                'Email',
                'CNPJ',
                'Telefone',
                'Contrato Mensal',
                'Data Fim Contrato',
                'Data de Criação',
            ], ';');

            // Dados
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->user->name,
                    $client->user->email,
                    $client->cnpj,
                    $client->phone,
                    $client->is_monthly_contract ? 'Sim' : 'Não',
                    $client->contract_end_date->format('d/m/Y'),
                    $client->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Gera PDF de clientes
     */
    private function generateClientsPdf($clients, $franchise)
    {
        $html = '<html><head><meta charset="UTF-8"><style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style></head><body>';

        $html .= '<h1>Relatório de Clientes - ' . htmlspecialchars($franchise->user->name) . '</h1>';
        $html .= '<p>Gerado em: ' . now()->format('d/m/Y H:i:s') . '</p>';

        $html .= '<table>';
        $html .= '<thead><tr>';
        $html .= '<th>Nome</th><th>Email</th><th>CNPJ</th><th>Telefone</th><th>Contrato Mensal</th><th>Fim Contrato</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($clients as $client) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($client->user->name) . '</td>';
            $html .= '<td>' . htmlspecialchars($client->user->email) . '</td>';
            $html .= '<td>' . htmlspecialchars($client->cnpj) . '</td>';
            $html .= '<td>' . htmlspecialchars($client->phone) . '</td>';
            $html .= '<td>' . ($client->is_monthly_contract ? 'Sim' : 'Não') . '</td>';
            $html .= '<td>' . $client->contract_end_date->format('d/m/Y') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        $filename = 'relatorio_clientes_' . str_replace(' ', '_', strtolower($franchise->user->name)) . '_' . date('Y-m-d_His') . '.html';

        return Response::make($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }

    /**
     * Gera CSV de colaboradores
     */
    private function generateCollaboratorsCsv($collaborators, $client)
    {
        $filename = 'colaboradores_' . str_replace(' ', '_', strtolower($client->user->name)) . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function() use ($collaborators, $client) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Cabeçalho
            fputcsv($file, [
                'Nome',
                'Email',
                'CPF',
                'Cargo',
                'Departamento',
                'Salário',
                'Data de Admissão',
                'Data de Criação',
            ], ';');

            // Dados
            foreach ($collaborators as $collaborator) {
                fputcsv($file, [
                    $collaborator->user->name,
                    $collaborator->user->email,
                    $collaborator->cpf ?? 'N/A',
                    $collaborator->position ?? 'N/A',
                    $collaborator->department ?? 'N/A',
                    $collaborator->salary ? 'R$ ' . number_format($collaborator->salary, 2, ',', '.') : 'N/A',
                    $collaborator->admission_date ? $collaborator->admission_date->format('d/m/Y') : 'N/A',
                    $collaborator->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Gera PDF de colaboradores
     */
    private function generateCollaboratorsPdf($collaborators, $client)
    {
        $html = '<html><head><meta charset="UTF-8"><style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
            th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style></head><body>';

        $html .= '<h1>Relatório de Colaboradores - ' . htmlspecialchars($client->user->name) . '</h1>';
        $html .= '<p>Gerado em: ' . now()->format('d/m/Y H:i:s') . '</p>';

        $html .= '<table>';
        $html .= '<thead><tr>';
        $html .= '<th>Nome</th><th>Email</th><th>CPF</th><th>Cargo</th><th>Depto</th><th>Salário</th><th>Admissão</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($collaborators as $collaborator) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($collaborator->user->name) . '</td>';
            $html .= '<td>' . htmlspecialchars($collaborator->user->email) . '</td>';
            $html .= '<td>' . htmlspecialchars($collaborator->cpf ?? 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars($collaborator->position ?? 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars($collaborator->department ?? 'N/A') . '</td>';
            $html .= '<td>' . ($collaborator->salary ? 'R$ ' . number_format($collaborator->salary, 2, ',', '.') : 'N/A') . '</td>';
            $html .= '<td>' . ($collaborator->admission_date ? $collaborator->admission_date->format('d/m/Y') : 'N/A') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        $filename = 'relatorio_colaboradores_' . str_replace(' ', '_', strtolower($client->user->name)) . '_' . date('Y-m-d_His') . '.html';

        return Response::make($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }
}

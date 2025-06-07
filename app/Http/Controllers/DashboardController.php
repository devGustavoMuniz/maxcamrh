<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Models\Franchise;
use App\Models\Client;
use App\Models\Collaborator;
use App\Enums\UserRole;

class DashboardController extends Controller
{
  public function __invoke(Request $request): Response
  {
    $user = Auth::user();
    $stats = [];

    if ($user->role === UserRole::ADMIN) {
      $stats['totalAdmins'] = User::where('role', UserRole::ADMIN)->count();
      $stats['totalFranchises'] = Franchise::count();
      $stats['totalClients'] = Client::count();
      $stats['totalCollaborators'] = Collaborator::count();
    } elseif ($user->role === UserRole::FRANCHISE) {
      if ($user->franchise) {
        $clientIds = $user->franchise->clients()->pluck('id');
        $stats['myTotalClients'] = $clientIds->count();
        $stats['myTotalCollaborators'] = Collaborator::whereIn('client_id', $clientIds)->count();
      } else {
        $stats['myTotalClients'] = 0;
        $stats['myTotalCollaborators'] = 0;
      }
    } elseif ($user->role === UserRole::CLIENT) {
      if ($user->client) {
        $stats['myCompanyCollaborators'] = $user->client->collaborators()->count();
      } else {
        $stats['myCompanyCollaborators'] = 0;
      }
    }

    return Inertia::render('Dashboard', [
      'stats' => $stats,
    ]);
  }
}

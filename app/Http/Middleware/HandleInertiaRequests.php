<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
      $user = $request->user();
      return array_merge(parent::share($request), [
        'auth' => [
          'user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,

             'can' => [
                'access_admins_module' => $user->can('access-admins-module'),
                'access_franchises_module' => $user->can('access-franchises-module'),
                'access_clients_module' => $user->can('access-clients-module'),
                'access_collaborators_module' => $user->can('access-collaborators-module'),
             ],

            'franchise_id' => $user->role === 'franchise' && $user->franchise ? $user->franchise->id : null,
            'client_id' => $user->role === 'client' && $user->client ? $user->client->id : null,
          ] : null,
        ],
      ]);
    }
}

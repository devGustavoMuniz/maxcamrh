<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // Importa a classe base do Laravel

class Controller extends BaseController // Não é abstract e estende BaseController
{
  use AuthorizesRequests, ValidatesRequests; // Usa os traits necessários
}

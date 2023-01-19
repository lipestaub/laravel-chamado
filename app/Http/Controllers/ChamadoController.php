<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ChamadoController extends Controller
{
    public function formulario() {
        $dados = [];

        return view('index', $dados);
    }
}

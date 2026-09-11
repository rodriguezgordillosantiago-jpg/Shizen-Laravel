<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller

{
    public function showRegistroUsuario()
    {
        return view('registro_usuario');
    }
    public function showRegistroNegocio()
    {
        return view('registro_negocio');
    }
    public function showRegistroRepartidor()
    {
        return view('registro_repartidor');
    }
}

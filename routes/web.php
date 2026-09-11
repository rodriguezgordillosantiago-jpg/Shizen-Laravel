<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AuthController;

// Página principal
Route::get('/', function () {
    return view('index');
});

// Redirecciones de "Volver al inicio"
Route::get('/index.php', function () { return redirect('/'); });
Route::get('/index.html', function () { return redirect('/'); });
Route::get('/php/index.html', function () { return redirect('/'); });
Route::get('/php/index.php', function () { return redirect('/'); });
Route::get('/pages/index.html', function () { return redirect('/'); });
Route::get('/pages/index.php', function () { return redirect('/'); });

// Categorías
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/php/categorias.php', [CategoriaController::class, 'index']);

// Promociones
Route::get('/promociones', function () {
    return view('promociones');
});
Route::get('/php/promociones.php', function () {
    return view('promociones');
});

// Formularios de Registro
Route::get('/registro-usuario', [AuthController::class, 'showRegistroUsuario']);
Route::get('/php/registro_usuario.php', [AuthController::class, 'showRegistroUsuario']);

Route::get('/registro-negocio', [AuthController::class, 'showRegistroNegocio']);
Route::get('/php/registro_negocio.php', [AuthController::class, 'showRegistroNegocio']);

Route::get('/registro-repartidor', [AuthController::class, 'showRegistroRepartidor']);
Route::get('/php/registro_repartidor.php', [AuthController::class, 'showRegistroRepartidor']);

// Login / Modal de Ingreso
Route::get('/login', function () {
    return view('login');
});
Route::get('/php/login.php', function () {
    return view('login');
});

Route::post('/auth/login.php', function () {
    return redirect('/');
});
Route::post('/auth/login', function () {
    return redirect('/');
});
Route::post('/login', function () {
    return redirect('/');
});

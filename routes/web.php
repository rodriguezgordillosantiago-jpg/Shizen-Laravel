<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RepartidorController;
use App\Support\ImageUrl;

// Página principal
Route::get('/', [HomeController::class, 'index']);
Route::get('/negocios/{businessId}/menu', [HomeController::class, 'menu'])->name('business.menu');
Route::get('/favoritos', [HomeController::class, 'favorites'])->name('favorites');
Route::post('/negocios/{businessId}/favorito', [HomeController::class, 'toggleFavorite'])->name('business.favorite');

// Redirecciones de "Volver al inicio"
Route::get('/index.php', function () { return redirect('/'); });
Route::get('/index.html', function () { return redirect('/'); });
Route::get('/php/index.html', function () { return redirect('/'); });
Route::get('/php/index.php', function () { return redirect('/'); });
Route::get('/pages/index.html', function () { return redirect('/'); });
Route::get('/pages/index.php', function () { return redirect('/'); });

// Categorías
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categories');
Route::get('/php/categorias.php', [CategoriaController::class, 'index']);

// Promociones
Route::get('/promociones', function () {
    $promotions = \Illuminate\Support\Facades\DB::table('promociones as p')
        ->join('menu_items as m', 'p.id_menu_item', '=', 'm.id_menu_item')
        ->leftJoin('negocios as n', 'n.id_negocio', '=', 'p.id_negocio')
        ->where('p.activo', 1)
        ->select([
            'p.id_promocion as id',
            'p.id_menu_item',
            \Illuminate\Support\Facades\DB::raw("COALESCE(p.nombre, m.nombre) as promo_nombre"),
            \Illuminate\Support\Facades\DB::raw("COALESCE(p.descripcion, m.descripcion) as promo_desc"),
            'm.precio',
            'm.precio_promocion',
            \Illuminate\Support\Facades\DB::raw("COALESCE(p.imagen_url, m.imagen_url) as imagen_url"),
            'm.id_categoria',
            \Illuminate\Support\Facades\DB::raw("COALESCE(n.nombre, 'Restaurante Shizen') as negocio_nombre")
        ])
        ->orderByDesc('p.id_promocion')
        ->get()
        ->map(function ($item) {
            $item->imagen_url = \App\Support\ImageUrl::resolve($item->imagen_url);
            return $item;
        });

    return view('promociones', compact('promotions'));
});
Route::get('/php/promociones.php', function () {
    return redirect('/promociones');
});

// Formularios de Registro
Route::get('/registro-usuario', [AuthController::class, 'showRegistroUsuario']);
Route::get('/php/registro_usuario.php', [AuthController::class, 'showRegistroUsuario']);
Route::post('/registro-usuario', [AuthController::class, 'registerUser'])->name('registro.usuario');
Route::post('/php/registro_usuario.php', [AuthController::class, 'registerUser']);

Route::view('/registro-exitoso', 'registro_exitoso')->name('registro.exitoso');
Route::post('/registro-negocio/documentos', [AuthController::class, 'finishRegistroNegocio'])->name('registro.negocio.documentos');
Route::post('/registro-repartidor/documentos', [AuthController::class, 'finishRegistroRepartidor'])->name('registro.repartidor.documentos');

Route::get('/registro-negocio', [AuthController::class, 'showRegistroNegocio'])->name('registro.negocio');
Route::get('/php/registro_negocio.php', [AuthController::class, 'showRegistroNegocio']);
Route::post('/registro-negocio', [AuthController::class, 'continueRegistroNegocio']);
Route::post('/php/registro_negocio.php', [AuthController::class, 'continueRegistroNegocio']);

Route::get('/registro-repartidor', [AuthController::class, 'showRegistroRepartidor'])->name('registro.repartidor');
Route::get('/php/registro_repartidor.php', [AuthController::class, 'showRegistroRepartidor']);
Route::post('/registro-repartidor', [AuthController::class, 'continueRegistroRepartidor']);
Route::post('/php/registro_repartidor.php', [AuthController::class, 'continueRegistroRepartidor']);

// Login / Modal de Ingreso
Route::get('/login', function () {
    return view('login');
});
Route::get('/php/login.php', function () {
    return view('login');
});

Route::post('/auth/login.php', [AuthController::class, 'login']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/perfil', [AuthController::class, 'updateProfile'])->name('profile.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/repartidor/{section?}', [RepartidorController::class, 'index'])
    ->where('section', 'inicio|activos|pedidos|historial|perfil')
    ->name('courier.dashboard');
Route::post('/repartidor/estado', [RepartidorController::class, 'toggleStatus'])->name('courier.status');
Route::post('/repartidor/entregas/{deliveryId}/aceptar', [RepartidorController::class, 'accept'])->name('courier.delivery.accept');
Route::post('/repartidor/entregas/{deliveryId}/avanzar', [RepartidorController::class, 'advance'])->name('courier.delivery.advance');
Route::post('/repartidor/perfil', [RepartidorController::class, 'updateProfile'])->name('courier.profile.update');
Route::get('/pedidos', [OrderController::class, 'index'])->name('orders');
Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
Route::get('/pedidos/{orderId}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/pedidos/{orderId}/confirmar', [OrderController::class, 'confirm'])->name('orders.confirm');
Route::post('/pedidos/{orderId}/calificar', [OrderController::class, 'rate'])->name('orders.rate');

Route::get('/buscar', function (\Illuminate\Http\Request $request) {
    $term = trim((string) $request->query('q', ''));
    $categories = \Illuminate\Support\Facades\DB::table('categorias')
        ->when($term !== '', function ($query) use ($term) {
            $query->where('nombre', 'like', '%' . $term . '%');
        })
        ->orderBy('nombre')
        ->get();
    $dishes = \Illuminate\Support\Facades\DB::table('menu_items as m')
        ->leftJoin('negocios as n', 'm.id_negocio', '=', 'n.id_negocio')
        ->when($term !== '', function ($query) use ($term) {
            $query->where(function ($search) use ($term) {
                $search->where('m.nombre', 'like', '%' . $term . '%')
                    ->orWhere('m.descripcion', 'like', '%' . $term . '%');
            });
        })
        ->select('m.id_menu_item as id', 'm.nombre', 'm.descripcion', 'm.precio', 'm.imagen_url', 'm.id_categoria as categoria_id', 'n.nombre as negocio_nombre')
        ->orderBy('m.nombre')
        ->get()
        ->map(function ($dish) {
            $dish->imagen_url = ImageUrl::resolve($dish->imagen_url);
            return $dish;
        });

    return view('buscar', compact('term', 'categories', 'dishes'));
})->name('search');

// Sincronización de Carrito con BD
Route::match(['get', 'post'], '/php/sync_carrito.php', function (\Illuminate\Http\Request $request) {
    $authUser = $request->session()->get('auth_user');
    $userId = (int)($authUser['id_usuario'] ?? $_SESSION['id_usuario'] ?? 0);
    if ($userId <= 0) {
        return response()->json(['logged_in' => false, 'items' => []]);
    }

    if ($request->isMethod('post')) {
        $items = $request->json()->all();
        if (!is_array($items)) $items = [];

        \Illuminate\Support\Facades\DB::transaction(function () use ($userId, $items) {
            \Illuminate\Support\Facades\DB::table('carrito')->where('id_usuario', $userId)->delete();
            foreach ($items as $it) {
                $mId   = (int)($it['id'] ?? 0);
                $bId   = (int)($it['businessId'] ?? $it['id_negocio'] ?? 0);
                $cant  = (int)($it['quantity'] ?? 1);
                $price = (float)($it['price'] ?? 0);

                if ($mId > 0 && $cant > 0) {
                    if ($bId <= 0) {
                        $bId = (int) \Illuminate\Support\Facades\DB::table('menu_items')->where('id_menu_item', $mId)->value('id_negocio');
                    }
                    if ($bId > 0) {
                        \Illuminate\Support\Facades\DB::table('carrito')->insert([
                            'id_usuario' => $userId,
                            'id_negocio' => $bId,
                            'id_menu_item' => $mId,
                            'cantidad' => $cant,
                            'precio_unitario' => $price,
                            'fecha_actualizacion' => now(),
                        ]);
                    }
                }
            }
        });
        return response()->json(['success' => true, 'count' => count($items)]);
    }

    $items = \Illuminate\Support\Facades\DB::table('carrito as c')
        ->join('menu_items as m', 'm.id_menu_item', '=', 'c.id_menu_item')
        ->join('negocios as n', 'n.id_negocio', '=', 'c.id_negocio')
        ->where('c.id_usuario', $userId)
        ->orderByDesc('c.fecha_actualizacion')
        ->orderByDesc('c.id_carrito')
        ->select([
            'c.id_carrito',
            'c.id_menu_item as id',
            'c.id_negocio as businessId',
            'c.cantidad as quantity',
            'c.precio_unitario as price',
            'c.fecha_actualizacion',
            'm.nombre as name',
            'm.imagen_url as image',
            'n.nombre as restaurant',
        ])
        ->get()
        ->map(function ($it) {
            return [
                'id' => (int)$it->id,
                'businessId' => (int)$it->businessId,
                'quantity' => (int)$it->quantity,
                'price' => (float)$it->price,
                'name' => $it->name,
                'image' => \App\Support\ImageUrl::resolve($it->image),
                'restaurant' => $it->restaurant,
                'addedAt' => strtotime((string)$it->fecha_actualizacion) * 1000,
            ];
        });

    return response()->json(['logged_in' => true, 'items' => $items]);
});
Route::match(['get', 'post'], '/sync_carrito', function (\Illuminate\Http\Request $request) {
    return redirect('/php/sync_carrito.php', 307);
});

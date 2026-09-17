<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Categorías
        $categories = [
            [
                'id_categoria' => 1,
                'nombre' => 'Comidas Rapidas',
                'icon' => '🍔',
                'descripcion' => 'Burgers, tacos y wraps plant-based para cuando el tiempo apremia.',
                'cover_img' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=800&h=400&fit=crop&auto=format',
                'bg_color' => '#fff8e1',
                'accent_color' => '#f59e0b',
            ],
            [
                'id_categoria' => 2,
                'nombre' => 'Cenas',
                'icon' => '🍝',
                'descripcion' => 'Platos elaborados para una cena especial o en familia.',
                'cover_img' => 'https://images.unsplash.com/photo-1473093226795-af9932fe5856?w=800&h=400&fit=crop&auto=format',
                'bg_color' => '#ede9fe',
                'accent_color' => '#7c3aed',
            ],
            [
                'id_categoria' => 3,
                'nombre' => 'Bowls & Ensaladas',
                'icon' => '🥗',
                'descripcion' => 'Bowls frescos, coloridos y cargados de nutrientes.',
                'cover_img' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=400&fit=crop&auto=format',
                'bg_color' => '#dcfce7',
                'accent_color' => '#16a34a',
            ],
            [
                'id_categoria' => 4,
                'nombre' => 'Postres',
                'icon' => '🍰',
                'descripcion' => 'Dulces sin culpa: 100% veganos, sin lácteos ni huevos.',
                'cover_img' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&h=400&fit=crop&auto=format',
                'bg_color' => '#fce7f3',
                'accent_color' => '#db2777',
            ],
            [
                'id_categoria' => 5,
                'nombre' => 'Bebidas',
                'icon' => '🥤',
                'descripcion' => 'Jugos naturales, smoothies y bebidas frías para hidratarte.',
                'cover_img' => 'https://images.unsplash.com/photo-1622597467836-f3285f2131b8?w=800&h=400&fit=crop&auto=format',
                'bg_color' => '#dbeafe',
                'accent_color' => '#2563eb',
            ],
            [
                'id_categoria' => 6,
                'nombre' => 'Desayunos',
                'icon' => '🌅',
                'descripcion' => 'Empieza el día con energía: tostadas, granola y mucho más.',
                'cover_img' => 'assets/image-6.png',
                'bg_color' => '#fef9c3',
                'accent_color' => '#ca8a04',
            ],
            [
                'id_categoria' => 7,
                'nombre' => 'Snacks',
                'icon' => '🍿',
                'descripcion' => 'Para el antojo del momento: chips, dips y snacks saludables.',
                'cover_img' => 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=800&h=400&fit=crop&auto=format',
                'bg_color' => '#f0fdf4',
                'accent_color' => '#15803d',
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('categorias')->updateOrInsert(['id_categoria' => $cat['id_categoria']], $cat);
        }

        // Usuario administrador / demo
        $userId = 1;
        DB::table('usuario')->updateOrInsert(
            ['id_usuario' => $userId],
            [
                'nombre' => 'Usuario',
                'apellido' => 'Demo',
                'direccion' => 'Calle 100 # 15-20',
                'email' => 'demo@shizen.com',
                'password_hash' => Hash::make('12345678'),
                'rol' => 'usuario',
                'ciudad' => 'Chapinero',
            ]
        );

        // Usuario Negocio
        $negocioUserId = 2;
        DB::table('usuario')->updateOrInsert(
            ['id_usuario' => $negocioUserId],
            [
                'nombre' => 'Green',
                'apellido' => 'Bowl',
                'direccion' => 'Carrera 7 # 72-41',
                'email' => 'greenbowl@shizen.com',
                'password_hash' => Hash::make('12345678'),
                'rol' => 'Negocio',
                'ciudad' => 'Chapinero',
            ]
        );

        // Negocios
        DB::table('negocios')->updateOrInsert(
            ['id_negocio' => 1],
            [
                'id_usuario' => $negocioUserId,
                'gmail_negocio' => 'greenbowl@shizen.com',
                'nombre' => 'Green Bowl Colombia',
                'direccion' => 'Carrera 7 # 72-41',
                'cedula' => '900123456',
                'hora_apertura' => '08:00',
                'hora_cierre' => '21:00',
                'logo_url' => 'assets/logo_negocio.png',
                'rut_url' => 'uploads/negocios/demo-rut.pdf',
                'documento_identidad_representante_url' => 'uploads/negocios/demo-id.pdf',
                'certificado_bancario_url' => 'uploads/negocios/demo-bancario.pdf',
                'certificado_camara_comercio_url' => null,
            ]
        );

        // Platos de prueba
        $dishes = [
            [
                'id_menu_item' => 1,
                'id_negocio' => 1,
                'nombre' => 'Burger Clásica Vegana',
                'descripcion' => 'Pan artesanal, medallón de lenteja y champiñón, lechuga, tomate y salsa especial.',
                'precio' => 22000,
                'stock' => 50,
                'imagen_url' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=600&h=400&fit=crop&auto=format',
                'id_categoria' => 1,
                'on_promo' => 0,
            ],
            [
                'id_menu_item' => 2,
                'id_negocio' => 1,
                'nombre' => 'Pasta Alfredo Plant-Based',
                'descripcion' => 'Fettuccine con salsa cremosa de marañón, ajo asado y perejil fresco.',
                'precio' => 26000,
                'stock' => 30,
                'imagen_url' => 'https://images.unsplash.com/photo-1473093226795-af9932fe5856?w=600&h=400&fit=crop&auto=format',
                'id_categoria' => 2,
                'on_promo' => 0,
            ],
            [
                'id_menu_item' => 3,
                'id_negocio' => 1,
                'nombre' => 'Buddha Bowl Proteico',
                'descripcion' => 'Quinoa, tofu marinado, garbanzos crocantes, aguacate y aderezo tahini.',
                'precio' => 24000,
                'stock' => 40,
                'imagen_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&h=400&fit=crop&auto=format',
                'id_categoria' => 3,
                'on_promo' => 1,
                'precio_promocion' => 18000,
            ],
            [
                'id_menu_item' => 4,
                'id_negocio' => 1,
                'nombre' => 'Tarta de Chocolate Vegana',
                'descripcion' => 'Base de nueces y cacao puro con ganache de chocolate amargo.',
                'precio' => 14000,
                'stock' => 25,
                'imagen_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&h=400&fit=crop&auto=format',
                'id_categoria' => 4,
                'on_promo' => 0,
            ],
            [
                'id_menu_item' => 5,
                'id_negocio' => 1,
                'nombre' => 'Smoothie Antioxidante',
                'descripcion' => 'Frutos rojos, leche de almendras y semillas de chía.',
                'precio' => 12000,
                'stock' => 60,
                'imagen_url' => 'https://images.unsplash.com/photo-1622597467836-f3285f2131b8?w=600&h=400&fit=crop&auto=format',
                'id_categoria' => 5,
                'on_promo' => 0,
            ],
        ];

        foreach ($dishes as $dish) {
            DB::table('menu_items')->updateOrInsert(['id_menu_item' => $dish['id_menu_item']], $dish);
        }

        // Calificación demo
        DB::table('calificacion')->updateOrInsert(
            ['id_calificacion' => 1],
            [
                'id_negocio' => 1,
                'id_usuario' => 1,
                'comentario' => '¡Excelente comida y atención!',
                'puntuacion' => 5,
            ]
        );
    }
}


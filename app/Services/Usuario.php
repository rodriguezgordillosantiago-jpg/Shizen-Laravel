<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Usuario
{
    public function findById(int $userId): ?object
    {
        return DB::table('usuario')->where('id_usuario', $userId)->first();
    }

    public function emailExists(string $email): bool
    {
        return DB::table('usuario')->where('email', $email)->exists();
    }

    public function authenticate(string $email, string $password): ?object
    {
        $user = DB::table('usuario')->where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password_hash)) {
            return null;
        }

        return $user;
    }

    public function register(array $data): int
    {
        return (int) DB::table('usuario')->insertGetId([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'rol' => $data['rol'] ?? 'Usuario',
            'direccion' => $data['direccion'] ?? null,
            'ciudad' => $data['ciudad'] ?? null,
        ], 'id_usuario');
    }
}

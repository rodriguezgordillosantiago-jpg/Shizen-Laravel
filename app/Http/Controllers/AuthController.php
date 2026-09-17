<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    public function continueRegistroNegocio(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:150'],
            'direccion' => ['required', 'string', 'min:5', 'max:150'],
            'hora_apertura' => ['required', 'date_format:H:i'],
            'hora_cierre' => ['required', 'date_format:H:i'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terminos' => ['required', 'in:aceptado'],
        ]);

        $request->session()->put('registro_negocio', $validated);

        return view('documentos_negocio');
    }

    public function continueRegistroRepartidor(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
            'apellido' => ['required', 'string', 'min:2', 'max:100'],
            'vehiculo' => ['required', 'in:Bicicleta,Moto,Patineta electrica,Carro'],
            'direccion' => ['required', 'string', 'min:5', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terminos' => ['required', 'in:aceptado'],
        ]);

        $request->session()->put('registro_repartidor', $validated);

        return view('documentos_repartidor');
    }

    public function finishRegistroNegocio(Request $request)
    {
        $registro = $request->session()->get('registro_negocio');
        abort_unless($registro, 419, 'La sesión del registro expiró.');

        $validated = $request->validate([
            'cedula' => ['required', 'digits_between:6,12'],
            'logo_negocio' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'rut' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'documento_identidad_representante' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'certificado_bancario' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'certificado_camara_comercio' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        DB::transaction(function () use ($registro, $validated, $request): void {
            $idUsuario = $this->createUsuario($registro, 'Negocio');
            $slug = Str::slug($registro['nombre']) . '-' . Str::random(8);

            DB::table('negocios')->insert([
                'id_usuario' => $idUsuario,
                'gmail_negocio' => $registro['email'],
                'nombre' => $registro['nombre'],
                'direccion' => $registro['direccion'],
                'cedula' => $validated['cedula'],
                'hora_apertura' => $registro['hora_apertura'],
                'hora_cierre' => $registro['hora_cierre'],
                'logo_url' => $this->storeUpload($request->file('logo_negocio'), 'negocios', $slug),
                'rut_url' => $this->storeUpload($request->file('rut'), 'negocios', $slug),
                'documento_identidad_representante_url' => $this->storeUpload($request->file('documento_identidad_representante'), 'negocios', $slug),
                'certificado_bancario_url' => $this->storeUpload($request->file('certificado_bancario'), 'negocios', $slug),
                'certificado_camara_comercio_url' => $request->hasFile('certificado_camara_comercio')
                    ? $this->storeUpload($request->file('certificado_camara_comercio'), 'negocios', $slug)
                    : null,
            ]);
        });

        $request->session()->forget('registro_negocio');

        return redirect()->route('registro.exitoso');
    }

    public function finishRegistroRepartidor(Request $request)
    {
        $registro = $request->session()->get('registro_repartidor');
        abort_unless($registro, 419, 'La sesión del registro expiró.');

        $validated = $request->validate([
            'cedula' => ['required', 'digits_between:6,12'],
            'foto_repartidor' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'documento_cedula' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        DB::transaction(function () use ($registro, $validated, $request): void {
            $idUsuario = $this->createUsuario($registro, 'Repartidor');
            $slug = Str::slug($registro['nombre'] . '-' . $registro['apellido']) . '-' . Str::random(8);

            DB::table('repartidor')->insert([
                'id_usuario' => $idUsuario,
                'nombre' => $registro['nombre'],
                'apellido' => $registro['apellido'],
                'email_repartidor' => $registro['email'],
                'direccion' => $registro['direccion'],
                'cedula' => $validated['cedula'],
                'vehiculo' => $registro['vehiculo'],
                'foto_url' => $this->storeUpload($request->file('foto_repartidor'), 'repartidores', $slug),
                'cedula_documento_url' => $this->storeUpload($request->file('documento_cedula'), 'repartidores', $slug),
                'estado' => 'Pendiente',
            ]);
        });

        $request->session()->forget('registro_repartidor');

        return redirect()->route('registro.exitoso');
    }

    private function createUsuario(array $registro, string $rol): int
    {
        $idUsuario = ((int) DB::table('usuario')->lockForUpdate()->max('id_usuario')) + 1;

        DB::table('usuario')->insert([
            'id_usuario' => $idUsuario,
            'nombre' => $registro['nombre'],
            'apellido' => $registro['apellido'] ?? 'Negocio',
            'direccion' => $registro['direccion'],
            'email' => strtolower(trim($registro['email'])),
            'password_hash' => Hash::make($registro['password']),
            'rol' => $rol,
            'ciudad' => 'Chapinero',
        ]);

        return $idUsuario;
    }

    private function storeUpload(?UploadedFile $file, string $folder, string $prefix): string
    {
        abort_unless($file, 422, 'Falta un archivo requerido.');

        $directory = public_path('uploads/' . $folder);
        File::ensureDirectoryExists($directory);
        $filename = $prefix . '-' . Str::random(12) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/' . $folder . '/' . $filename;
    }

    public function registerUser(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'apellido' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terminos' => ['required', 'in:aceptado'],
        ]);

        DB::transaction(function () use ($validated): void {
            $nextId = ((int) DB::table('usuario')
                ->lockForUpdate()
                ->max('id_usuario')) + 1;

            DB::table('usuario')->insert([
                'id_usuario' => $nextId,
                'nombre' => trim($validated['nombre']),
                'apellido' => trim($validated['apellido']),
                'email' => strtolower(trim($validated['email'])),
                'password_hash' => Hash::make($validated['password']),
                'rol' => 'usuario',
                'ciudad' => 'Chapinero',
            ]);
        });

        return redirect()->route('registro.exitoso');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = DB::table('usuario')
            ->where('email', strtolower(trim($validated['email'])))
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password_hash)) {
            return back()->withErrors(['email' => 'El correo o la contraseña no son correctos.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        $request->session()->put('auth_user', [
            'id_usuario' => $user->id_usuario,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'email' => $user->email,
            'direccion' => $user->direccion,
            'ciudad' => $user->ciudad,
            'rol' => $user->rol,
        ]);

        if (strtolower((string) $user->rol) === 'repartidor') {
            return redirect()->route('courier.dashboard');
        }

        $redirect = (string) $request->input('redirect', '/');
        if (!Str::startsWith($redirect, '/') || Str::startsWith($redirect, '//')) {
            $redirect = '/';
        }

        return redirect()->to($redirect);
    }

    public function updateProfile(Request $request)
    {
        $authUser = $request->session()->get('auth_user');
        abort_unless($authUser, 403);

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'apellido' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
        ]);

        $changes = [
            'nombre' => trim($validated['nombre']),
            'apellido' => trim($validated['apellido']),
            'direccion' => $validated['direccion'] ?? null,
            'ciudad' => $validated['ciudad'] ?? null,
        ];

        DB::table('usuario')->where('id_usuario', $authUser['id_usuario'])->update($changes);
        $request->session()->put('auth_user', array_merge($authUser, $changes));

        return back()->with('profile_success', 'Tus datos fueron actualizados.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('auth_user');
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

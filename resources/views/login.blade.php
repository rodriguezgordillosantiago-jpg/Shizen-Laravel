<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión | Shizen</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  <style>
    body { min-height: 100vh; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); display: flex; flex-direction: column; }
    .login-page { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
    .login-card { background: #fff; border-radius: 22px; padding: 40px 36px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.12); }
    .login-logo { display: block; width: 110px; height: 56px; object-fit: contain; margin: 0 auto 20px; }
    .login-title { text-align: center; font-size: 1.4rem; font-weight: 800; color: #1b3a1d; margin-bottom: 6px; }
    .login-sub { text-align: center; color: #888; font-size: .93rem; margin-bottom: 24px; }
    .login-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 10px; padding: 10px 14px; font-size: .9rem; margin-bottom: 16px; }
    .login-register, .login-back { color: #3a8c3f; font-weight: 600; text-decoration: none; }
    .login-register { display: block; text-align: center; }
    .login-back { display: inline-flex; margin-bottom: 24px; }
    .login-divider { text-align: center; color: #aaa; font-size: .85rem; margin: 20px 0 12px; }
  </style>
</head>
<body>
  <main class="login-page">
    <div class="login-card">
      <a class="login-back back-link" href="{{ url('/') }}">← Volver al inicio</a>
      <img class="login-logo" src="{{ asset('assets/logo.png') }}" alt="Shizen">
      <div class="login-title">Bienvenido de vuelta</div>
      <div class="login-sub">Accede a tu cuenta Shizen</div>
      @if ($errors->any())
        <p class="login-error">{{ $errors->first() }}</p>
      @endif
      <form method="post" action="{{ route('login') }}">
        @csrf
        @if (request('redirect'))
          <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif
        <div class="modal-form-group">
          <label for="email">Email</label>
          <input class="modal-input" id="email" name="email" type="email" placeholder="tu@email.com" autocomplete="email" required>
        </div>
        <div class="modal-form-group">
          <label for="password">Contraseña</label>
          <input class="modal-input" id="password" name="password" type="password" placeholder="Contraseña" autocomplete="current-password" required>
        </div>
        <button class="btn-modal-primary" type="submit" style="margin-top: 10px;">Ingresar</button>
      </form>
      <div class="login-divider">¿Nuevo en Shizen?</div>
      <a class="login-register" href="{{ url('/registro-usuario') }}">Crear cuenta gratis</a>
    </div>
  </main>
</body>
</html>

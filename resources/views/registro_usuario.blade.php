<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de usuario | Shizen</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/registro_base.css') }}">
</head>
<body>
<div class="view active" id="view-join-restaurant">
  <div class="join-page">
    <div class="join-left" style="background:linear-gradient(135deg,#1a3a2a,#2a7a50)">
      <div class="join-left-bg" style="background-image:url('https://images.unsplash.com/photo-1572715376701-98568319fd0b?w=800&fit=crop&auto=format')">
      </div>
      <div class="join-left-content">
        <img src="{{ asset('assets/logo.png') }}" alt="Shizen" class="join-left-logo" />
        <span class="join-left-badge">
          Para usuarios
        </span>
        <h2>
          Conoce miles de negocios veganos
        </h2>
        <p>
          Compra y haz parte de la red de personas conscientes más grande de Colombia.
        </p>
        <br>
        <div class="benefit-grid">
          <div class="benefit-card">
            <div class="b-icon">
            📖
            </div>
            <div class="b-title">
            Gran catálogo
            </div>
            <div class="b-desc">
              Accede a miles de platillos veganos al instante
            </div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">
              🚵
            </div>
            <div class="b-desc">
              Domicilios en toda la ciudad
            </div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">
              🏷️
            </div>
            <div class="b-title">
              Miles de descuentos
            </div>
            <div class="b-desc">
              Contribuye al cuidado del planeta y de tu bolsillo
            </div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">
              💚
            </div>
            <div class="b-title">
              Marca sostenible
            </div>
            <div class="b-desc">
              Productos veganos y ecológicos
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="join-right">
      <div class="join-form-wrap">
        <a class="join-back back-link" href="{{ url('/') }}">
          ← Volver al inicio
        </a>
        <div class="progress-steps">
          <div class="step-dot active" id="joinRest-dot-1">
            1
          </div>
          <div class="step-line" id="joinRest-line-1">
          </div>
          <div class="step-dot" id="joinRest-dot-2">
            2
          </div>
        </div>
        @if ($errors->any())
          <div class="form-server-error" role="alert">
            {{ $errors->first() }}
          </div>
        @endif
        <form method="post" action="{{ route('registro.usuario') }}">
          @csrf
          <h2>
            ¡Regístrate ahora y empieza a comprar!
          </h2>
          <p class="form-sub">
            Completa tus datos para comenzar.
          </p>
          <br>
          <div class="form-group">
            <label>
              Nombre
            </label>
            <input class="form-input" type="text" name="nombre" placeholder="Tu nombre" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .'\-]{2,}" title="Ingresa tu nombre." required />
          </div>
          <div class="form-group">
            <label>
              Apellido
            </label>
            <input class="form-input" type="text" name="apellido" placeholder="Tu apellido" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .'\-]{2,}" title="Ingresa tu apellido." required />
          </div>
          <div class="form-group">
            <label>
              Email
            </label>
            <input class="form-input" type="email" name="email" placeholder="tu@email.com" required />
          </div>
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input class="form-input" id="password" type="password" name="password" autocomplete="new-password" minlength="8" placeholder="Mínimo 8 caracteres" required />
          </div>
          <div class="form-group">
            <label for="password-confirmation">Confirmar contraseña</label>
            <input class="form-input" id="password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required />
          </div>
        <div id="joinRest-step2">
          <br>
          <br>
          <h2>
            Casi listo
          </h2>
          <br>
          <div class="terms-check">
            <input type="checkbox" id="termsRest" name="terminos" value="aceptado" required />
            <label for="termsRest">
              Acepto los términos y condiciones de Shizen y la política de privacidad.
            </label>
          </div>
          <button class="btn-primary-full" type="submit">
            Enviar solicitud 🚀
          </button>
        </div>
        </form>
        <div id="joinRest-success" class="success-state hidden">
          <div class="success-icon">
            🎉
          </div>
          <h3>
            Registro completado
          </h3>
          <p>
            Bienvenido a la familia Shizen
          </p>
          <a class="btn-primary-full" href="{{ url('/') }}">
            Volver al inicio
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>

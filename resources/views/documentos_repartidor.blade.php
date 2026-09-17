<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documentos del repartidor | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/registro_base.css') }}">
</head>
<body>
<main class="view active document-page">
  <div class="join-page">
    <div class="join-left" style="background:linear-gradient(135deg,#bf360c,#f57c00)">
      <div class="join-left-bg" style="background-image:url('https://images.unsplash.com/photo-1611068562065-994ba66501ba?w=800&fit=crop&auto=format')"></div>
      <div class="join-left-content">
        <img src="{{ asset('assets/logo-repartidor.png') }}" alt="Shizen" class="join-left-logo">
        <span class="join-left-badge">Para repartidores</span>
        <h2>Gana dinero recorriendo la ciudad</h2>
        <p>Sé repartidor Shizen: horarios flexibles, mejores tarifas y una comunidad que cuida el planeta.</p>
        <div class="benefit-grid">
          <div class="benefit-card"><div class="b-icon">💰</div><div class="b-title">Ganancias top</div><div class="b-desc">Las mejores tarifas del mercado</div></div>
          <div class="benefit-card"><div class="b-icon">🕒</div><div class="b-title">Horarios libres</div><div class="b-desc">Trabaja cuando quieras</div></div>
          <div class="benefit-card"><div class="b-icon">🛡</div><div class="b-title">Seguro incluido</div><div class="b-desc">Accidentes cubiertos en cada domicilio</div></div>
          <div class="benefit-card"><div class="b-icon">📲</div><div class="b-title">App intuitiva</div><div class="b-desc">Gestiona todo desde tu celular</div></div>
        </div>
      </div>
    </div>
    <div class="join-right">
    <div class="join-form-wrap document-form-wrap">
      <a class="join-back back-link" href="{{ route('registro.repartidor') }}">← Volver a tus datos</a>
      <div class="progress-steps">
        <div class="step-dot done">✓</div><div class="step-line done"></div><div class="step-dot active">2</div>
      </div>
      <form method="post" action="{{ route('registro.repartidor.documentos') }}" enctype="multipart/form-data">
        @csrf
        <h2>Sube tus documentos</h2>
        <p class="form-sub">Adjunta la documentación para completar tu solicitud.</p>
        <div class="form-group">
          <label for="cedula">Cédula</label>
          <input class="form-input" id="cedula" name="cedula" inputmode="numeric" pattern="[0-9]{6,12}" required>
        </div>
        <div class="form-group">
          <label for="foto">Foto de perfil</label>
          <input class="form-input" id="foto" name="foto_repartidor" type="file" accept="image/jpeg,image/png,image/webp" required>
        </div>
        <div class="form-group">
          <label for="documento">Documento de identidad (PDF)</label>
          <input class="form-input" id="documento" name="documento_cedula" type="file" accept="application/pdf" required>
        </div>
        <button class="btn-primary-full" type="submit">Finalizar registro</button>
      </form>
    </div>
    </div>
  </div>
</main>
</body>
</html>

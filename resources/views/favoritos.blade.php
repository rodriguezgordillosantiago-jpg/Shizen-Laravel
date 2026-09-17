<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Favoritos | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  <link rel="stylesheet" href="{{ asset('css/favorites.css') }}">
</head>
<body>
  <header>@include('navegacion')</header>
  <main class="favorites-page">
    <a class="menu-back back-link" href="{{ url('/') }}">← Volver al inicio</a>
    <h1>Mis favoritos</h1>
    <p class="favorites-subtitle">Los negocios que guardaste para volver cuando quieras.</p>
    <div class="favorites-grid">
      @forelse ($businesses as $business)
        <a class="favorite-card" href="{{ route('business.menu', $business->id) }}">
          <img src="{{ $business->logo_url }}" alt="Logo de {{ $business->nombre }}" loading="lazy" decoding="async">
          <div>
            <strong>{{ $business->nombre }}</strong>
            <span>★ {{ number_format($business->rating, 1) }}</span>
          </div>
        </a>
      @empty
        <p class="favorites-empty">Todavía no tienes negocios favoritos.</p>
      @endforelse
    </div>
  </main>
  <div id="overlays">@include('modales')</div>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $category->nombre }} | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  <link rel="stylesheet" href="{{ asset('css/business-menu.css?v=20260912-1') }}">
</head>
<body>
  <header id="navigation">@include('navegacion')</header>
  <main id="app-content">
    <section class="cat-page">
      <div class="cat-hero" id="catHero" style="background-image:url('{{ $category->cover_img }}')">
        <div class="cat-hero-overlay"></div>
        <a class="cat-hero-back back-link" href="{{ url('/') }}" aria-label="Volver">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7" /></svg>
        </a>
        <div class="cat-hero-info">
          <div class="cat-hero-icon-name">
            <span class="cat-hero-icon">{{ $category->icon ?: '🍔' }}</span>
            <span class="cat-hero-name">{{ $category->nombre }}</span>
          </div>
          <p class="cat-hero-desc">{{ $category->descripcion }}</p>
        </div>
      </div>
      <div class="cat-filter-bar">
        <span>Ordenar por:</span>
        <select class="sort-select"><option>Relevancia</option><option>Precio: menor</option><option>Precio: mayor</option></select>
      </div>
      <div class="cat-items-inner">
        <div class="items-grid" id="itemsGrid">
          @forelse ($dishes as $dish)
            <?php
              $hasPromo = !empty($dish->on_promo) && !empty($dish->precio_promocion);
              $precioReal = $hasPromo ? (float) $dish->precio_promocion : (float) $dish->precio;
            ?>
            <article class="dish-card">
              <div class="dish-img">
                <img class="dish-img-image" src="{{ $dish->imagen_url }}" alt="{{ $dish->plato_nombre }}" loading="lazy" decoding="async">
                <span class="dish-tag-badge">Vegano</span>
                @if ($hasPromo)
                  <span class="dish-promo-badge" style="position:absolute;top:10px;left:10px;background:#ea580c;color:#fff;font-weight:700;padding:4px 8px;border-radius:6px;font-size:11px;box-shadow:0 2px 6px rgba(234,88,12,.5)">🔥 PROMOCIÓN</span>
                @endif
                @if ($dish->negocio_id)
                  @if (session('auth_user'))
                    <form method="post" action="{{ route('business.favorite', $dish->negocio_id) }}" class="dish-favorite-form">
                      @csrf
                      <button class="favorite-button dish-favorite-button {{ $dish->is_favorite ? 'is-favorite' : '' }}" type="submit" aria-label="{{ $dish->is_favorite ? 'Quitar restaurante de favoritos' : 'Agregar restaurante a favoritos' }}">
                        {{ $dish->is_favorite ? '♥' : '♡' }}
                      </button>
                    </form>
                  @else
                    <a class="favorite-button dish-favorite-button" href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" aria-label="Inicia sesión para agregar el restaurante a favoritos" title="Inicia sesión para agregar el restaurante a favoritos">♡</a>
                  @endif
                @endif
              </div>
              <div class="dish-body">
                <div class="dish-name">{{ $dish->plato_nombre }}</div>
                <div class="dish-restaurant">🏪 {{ $dish->negocio_nombre }}</div>
                <p class="dish-description">{{ $dish->plato_desc }}</p>
                <div class="dish-meta"><span>🕐 20 min</span></div>
                <div class="dish-price-row">
                  <div class="dish-prices">
                    @if ($hasPromo)
                      <span class="dish-price" style="color:#ea580c">${{ number_format((float) $dish->precio_promocion, 0, ',', '.') }}</span>
                      <span style="font-size:12px;color:#9ca3af;text-decoration:line-through;margin-left:6px">${{ number_format((float) $dish->precio, 0, ',', '.') }}</span>
                    @else
                      <span class="dish-price">${{ number_format((float) $dish->precio, 0, ',', '.') }}</span>
                    @endif
                  </div>
                  <?php $cartDish = ['id' => (int) $dish->id, 'name' => $dish->plato_nombre, 'price' => $precioReal, 'restaurant' => $dish->negocio_nombre, 'businessId' => (int) ($dish->id_negocio ?? 0), 'image' => $dish->imagen_url]; ?>
                  <button class="btn-add-cart" type="button" onclick='addToCart(<?= e(json_encode($cartDish, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)'>Añadir al carrito</button>
                </div>
              </div>
            </article>
          @empty
            <p class="cat-empty">No hay platos registrados en esta categoría.</p>
          @endforelse
        </div>
      </div>
    </section>
  </main>
  <div id="overlays">@include('modales')</div>
  <script src="{{ asset('js/data.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

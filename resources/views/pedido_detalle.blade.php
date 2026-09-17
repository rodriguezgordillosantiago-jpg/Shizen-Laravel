<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pedido #{{ $order->id_pedido }} | Shizen</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
</head>
<body>
  <header>@include('navegacion')</header>
  <main class="orders-page">
    <a class="back-link" href="{{ route('orders') }}" aria-label="Volver a mis pedidos" title="Volver a mis pedidos">← Volver a mis pedidos</a>
    <section class="order-detail">
      <h1>Pedido #{{ $order->id_pedido }}</h1>
      <p>{{ $order->descripcion }}</p>
      <p>Estado: <strong>{{ $order->estado }}</strong></p>
      <p>Dirección: {{ $order->direccion_entrega }}</p>

      <div class="order-tracking" aria-label="Rastreo del pedido">
        <h2>Rastreo del pedido</h2>
        <div class="tracking-line">
          <div class="tracking-step is-complete"><span>✓</span><strong>Pedido realizado</strong><small>Confirmado</small></div>
          <div class="tracking-step is-complete"><span>✓</span><strong>En preparación</strong><small>El negocio está preparando tu pedido</small></div>
          <div class="tracking-step {{ $order->fecha_confirmacion ? 'is-complete' : 'is-current' }}"><span>{{ $order->fecha_confirmacion ? '✓' : '3' }}</span><strong>En camino</strong><small>{{ $order->fecha_confirmacion ? 'Entrega confirmada' : 'Rastreo estático por ahora' }}</small></div>
          <div class="tracking-step {{ $order->fecha_confirmacion ? 'is-complete' : '' }}"><span>{{ $order->fecha_confirmacion ? '✓' : '4' }}</span><strong>Entregado</strong><small>{{ $order->fecha_confirmacion ? 'Pedido recibido' : 'Pendiente de entrega' }}</small></div>
        </div>
      </div>

      @if(session('order_created'))
        <p class="order-success" role="alert">✓ Pedido registrado correctamente. Puedes consultar su estado y código de entrega aquí.</p>
      @elseif(session('order_success'))
        <p class="order-success" role="alert">✓ {{ session('order_success') }}</p>
      @endif

      @if($order->fecha_confirmacion)
        <p class="order-success">✅ Pedido entregado. ¡Gracias por elegir Shizen!</p>
      @else
        <div class="delivery-code-box" style="background:#f0fdf4;border:2px dashed #22c55e;border-radius:12px;padding:16px;text-align:center;margin:20px 0">
          <div style="font-size:14px;color:#15803d;font-weight:700">🔑 Tu Código de Entrega:</div>
          <div style="font-size:28px;font-weight:900;letter-spacing:4px;color:#166534;margin:6px 0">{{ $order->codigo_entrega ?: sprintf('%06d', ($order->id_pedido * 137461) % 900000 + 100000) }}</div>
          <div style="font-size:12px;color:#4b5563">Entrégaselo al repartidor cuando llegue con tu pedido para confirmar la entrega.</div>
        </div>
      @endif

      @if($order->fecha_confirmacion)
        @if(!$rated && $order->id_repartidor)
          <form class="rating-form" method="post" action="{{ route('orders.rate', $order->id_pedido) }}">
            @csrf
            <h2>Califica al repartidor</h2>
            <fieldset class="rating-stars" aria-label="Selecciona una calificación de 1 a 5 estrellas">
              <legend>Tu valoración</legend>
              @for($rating = 1; $rating <= 5; $rating++)
                <label class="rating-star">
                  <input type="radio" name="puntuacion" value="{{ $rating }}" aria-label="{{ $rating }} estrellas" required>
                  <span aria-hidden="true"></span>
                </label>
              @endfor
            </fieldset>
            <textarea class="modal-input" name="comentario" placeholder="Comentario opcional" rows="3"></textarea>
            <button class="btn-modal-primary" type="submit">Enviar calificación</button>
          </form>
        @elseif($rated)
          <p class="order-success">Ya calificaste este pedido.</p>
        @else
          <p class="order-success">La calificación estará disponible cuando se asigne el repartidor.</p>
        @endif
      @endif
    </section>
  </main>
  <div id="overlays">@include('modales')</div>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

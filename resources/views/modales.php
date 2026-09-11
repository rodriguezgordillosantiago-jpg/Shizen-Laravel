<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<div
  class="modal-overlay"
  id="registerModal"
  onclick="handleRegisterOverlayClick(event)"
>
  <div class="modal-card">
    <button
      class="modal-close"
      onclick="closeRegisterModal()"
      aria-label="Cerrar"
    >
      ×
    </button>
    <div class="modal-icon" id="regModalIcon">🔐</div>
    <div class="modal-title" id="regModalTitle">
      Crea tu cuenta gratis
    </div>
    <div class="modal-sub" id="regModalSub">
      Para comprar en Shizen necesitas una cuenta. ¡Es gratis y rápido!
    </div>
    <button class="btn-modal-primary">
      Crear cuenta gratis
    </button>
    <button
      class="btn-modal-secondary"
      onclick="openAccountLogin()"
    >
      Ya tengo cuenta
    </button>
    <div class="offer-warning hidden" id="offerWarning">
      ⚠ Oferta por tiempo limitado - no pierdas el
      descuento! Oferta por tiempo limitado - no pierdas el
      descuento!
    </div>
  </div>
</div>
<div class="modal-overlay" id="cartModal" onclick="handleCartOverlayClick(event)">
  <div class="modal-card cart-card">
    <button class="modal-close" type="button" onclick="closeCart()" aria-label="Cerrar">×</button>
    <div class="modal-title">Tu carrito</div>
    <div id="cartItems" class="cart-items"></div>
    <div class="cart-total-row">
      <span>Total</span>
      <strong id="cartTotal">$0</strong>
    </div>
    <button class="btn-modal-primary" type="button" id="checkoutButton" onclick="openCheckout()">
      Continuar compra
    </button>
  </div>
</div>
<div class="modal-overlay" id="checkoutModal" onclick="handleCheckoutOverlayClick(event)">
  <div class="modal-card checkout-card">
    <button class="modal-close" type="button" onclick="closeCheckout()" aria-label="Cerrar">×</button>
    <div class="modal-title">Datos de entrega</div>
    <p class="modal-sub">Completa tus datos para registrar el pedido.</p>
    <form method="post" action="php/registrar_pedido.php" onsubmit="prepareCheckout(event)">
      <input type="hidden" name="items" id="checkoutItems">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="checkout-fields">
        <input class="modal-input" name="nombre" placeholder="Nombre" required maxlength="60">
        <input class="modal-input" name="apellido" placeholder="Apellido" required maxlength="60">
        <input class="modal-input" name="numero_documento" placeholder="Número de documento" required maxlength="20" inputmode="numeric">

        <input class="modal-input checkout-wide" name="correo" type="email" placeholder="Correo electrónico" required maxlength="120">
        <input class="modal-input checkout-wide" name="direccion" placeholder="Dirección de entrega" required maxlength="255">
        <input class="modal-input checkout-wide" name="localidad" placeholder="Localidad (opcional)" maxlength="60">
        <select class="modal-input checkout-wide" name="metodo_pago" required>
          <option value="" disabled selected>Método de pago</option>
          <option value="Contraentrega">Contraentrega (efectivo)</option>
          <option value="Transferencia">Transferencia bancaria</option>
          <option value="Nequi">Nequi</option>
          <option value="Daviplata">Daviplata</option>
        </select>
      </div>
      <p id="checkoutError" class="login-error" hidden></p>
      <button class="btn-modal-primary" type="submit">Registrar pedido</button>
    </form>
  </div>
</div>

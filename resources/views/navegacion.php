<?php
$authUser = session('auth_user');
$logueado = is_array($authUser) && !empty($authUser['id_usuario']);
$csrfField = csrf_field();
?>
<script>
  window.shizenSyncCartUrl = <?= json_encode(url('/php/sync_carrito.php'), JSON_UNESCAPED_SLASHES) ?>;
</script>
<nav>
  <div class="nav-inner">
    <a class="nav-logo" href="<?= e(url('/')) ?>">
      <img src="<?= e(asset('assets/logo.png')) ?>" alt="Shizen" />
    </a>
    <form class="nav-search" method="get" action="<?= e(route('search')) ?>" role="search">
      <label class="sr-only" for="navSearch">Buscar en Shizen</label>
      <input id="navSearch" name="q" type="search" placeholder="¿Qué quieres comer?" value="<?= e(request('q', '')) ?>">
      <button type="submit" aria-label="Buscar">⌕</button>
    </form>
    <div class="nav-right">
      <?php if ($logueado): ?>
        <a class="nav-favorites-button" href="<?= e(route('favorites')) ?>" aria-label="Ver favoritos" title="Favoritos">&#9825;</a>
        <a class="nav-orders-button" href="<?= e(route('orders')) ?>" aria-label="Ver mis pedidos" title="Mis pedidos">&#128666;</a>
        <div class="nav-user-menu">
          <button class="btn-ingreso btn-ingreso--user" id="userMenuBtn" type="button" onclick="openProfileModal()">
            <span class="profile-avatar" aria-hidden="true">&#128100;</span> <?= e($authUser['nombre']) ?>
          </button>
          <div class="nav-dropdown" role="menu">
            <button class="nav-dropdown-item" type="button" onclick="openProfileModal()">Editar mis datos</button>
            <a class="nav-dropdown-item" href="<?= e(route('favorites')) ?>">Favoritos</a>
            <form method="post" action="<?= e(route('logout')) ?>">
              <?= $csrfField ?>
              <button class="nav-dropdown-item" type="submit">Cerrar sesión</button>
            </form>
          </div>
        </div>
      <?php else: ?>
        <a class="nav-favorites-button" href="<?= e(url('/login?redirect=/favoritos')) ?>" aria-label="Inicia sesión para ver favoritos" title="Favoritos">&#9825;</a>
        <a class="nav-orders-button" href="<?= e(url('/login?redirect=/pedidos')) ?>" aria-label="Inicia sesión para rastrear pedidos" title="Rastrear pedido">&#128666;</a>
        <a class="btn-ingreso" href="<?= e(url('/login')) ?>">Ingreso</a>
      <?php endif; ?>
      <button class="btn-cart" id="cartBtn" onclick="openCart()" aria-label="Abrir carrito" title="Carrito">
        &#128722; <span id="cartCount">0</span>
      </button>
      <button class="btn-hamburger" id="hamburgerBtn" onclick="toggleMobileMenu()" aria-label="Menú">&#9776;</button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <?php if ($logueado): ?>
      <button class="mobile-menu-btn" type="button" onclick="openProfileModal(); toggleMobileMenu()">&#128100; Editar mis datos</button>
      <a class="mobile-menu-btn" href="<?= e(route('favorites')) ?>">&#9825; Favoritos</a>
      <a class="mobile-menu-btn" href="<?= e(route('orders')) ?>">&#128666; Mis pedidos</a>
      <form method="post" action="<?= e(route('logout')) ?>">
        <?= $csrfField ?>
        <button class="mobile-menu-btn" type="submit">&#128274; Cerrar sesión</button>
      </form>
    <?php else: ?>
      <a class="mobile-menu-btn" href="<?= e(url('/login?redirect=/favoritos')) ?>">&#9825; Favoritos</a>
      <a class="mobile-menu-btn" href="<?= e(url('/login?redirect=/pedidos')) ?>">&#128666; Rastrear pedido</a>
      <a class="mobile-menu-btn" href="<?= e(url('/login')) ?>">&#128272; Iniciar sesión</a>
    <?php endif; ?>
    <a class="mobile-menu-btn" href="<?= e(url('/promociones')) ?>">&#127881; Promociones</a>
    <a class="mobile-menu-btn" href="<?= e(url('/registro-usuario')) ?>">&#128100; Para usuarios</a>
    <a class="mobile-menu-btn" href="<?= e(url('/registro-negocio')) ?>">&#127978; Para negocios</a>
    <a class="mobile-menu-btn" href="<?= e(url('/registro-repartidor')) ?>">&#128693; Para repartidores</a>
  </div>
  <div class="nav-underline"></div>
</nav>

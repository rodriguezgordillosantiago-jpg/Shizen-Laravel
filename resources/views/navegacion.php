<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$logueado      = !empty($_SESSION["id_usuario"]);
$nombreUsuario = htmlspecialchars($logueado ? ($_SESSION['usuario_nombre'] ?? 'Mi cuenta') : '');
$rolUsuario    = strtolower(trim($_SESSION["usuario_rol"] ?? ""));
?>
<nav>
  <div class="nav-inner">
    <a class="nav-logo" href="index.php">
      <img src="assets/logo.png" alt="Shizen" />
    </a>
    <div class="nav-colombia">Colombia</div>
    <div class="nav-right">
      <?php if ($logueado): ?>
        <div class="nav-user-menu">
          <button class="btn-ingreso btn-ingreso--user" id="userMenuBtn" aria-haspopup="true" aria-expanded="false"
                  onclick="this.closest('.nav-user-menu').classList.toggle('open'); this.setAttribute('aria-expanded', this.closest('.nav-user-menu').classList.contains('open'));">
            &#128100; <?= $nombreUsuario ?>
          </button>
          <div class="nav-dropdown" role="menu">
            <a class="nav-dropdown-item" href="auth/logout.php" role="menuitem">Cerrar sesion</a>
          </div>
        </div>
      <?php else: ?>
        <a class="btn-ingreso" href="php/login.php">Ingreso</a>
      <?php endif; ?>
      <button class="btn-cart" id="cartBtn" onclick="openCart()" aria-label="Abrir carrito" title="Carrito">
        &#128722; <span id="cartCount">0</span>
      </button>
      <button class="btn-hamburger" id="hamburgerBtn" onclick="toggleMobileMenu()" aria-label="Menu">&#9776;</button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <?php if ($logueado): ?>
      <a class="mobile-menu-btn" href="auth/logout.php">&#128274; Cerrar sesion</a>
    <?php else: ?>
      <a class="mobile-menu-btn" href="php/login.php">&#128272; Iniciar sesion</a>
    <?php endif; ?>
    <a class="mobile-menu-btn" href="php/promociones.php">&#127881; Promociones</a>
    <a class="mobile-menu-btn" href="php/registro_usuario.php">&#128100; Para usuarios</a>
    <a class="mobile-menu-btn" href="php/registro_negocio.php">&#127978; Para negocios</a>
    <a class="mobile-menu-btn" href="php/registro_repartidor.php">&#128691; Para repartidores</a>
  </div>
  <div class="nav-underline"></div>
</nav>

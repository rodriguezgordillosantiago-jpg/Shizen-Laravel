<nav>
  <div class="nav-inner">
    <a class="nav-logo" href="index.html">
      <img src="assets/logo.png" alt="Shizen" />
    </a>
    <div class="nav-right">
      <button
        class="btn-ingreso"
        id="ingresoBtn"
        onclick="openAccessModal()"
      >
        Ingreso
      </button>
      <button
        class="btn-cart"
        id="cartBtn"
        onclick="openCart()"
        aria-label="Abrir carrito"
        title="Carrito"
      >
        🛒 <span id="cartCount">0</span>
      </button>
      <button
        class="btn-hamburger"
        id="hamburgerBtn"
        onclick="toggleMobileMenu()"
        aria-label="Menú"
      >
        ☰
      </button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <button
      class="mobile-menu-btn"
      onclick="
        openAccessModal();
        toggleMobileMenu();
      "
    >
      &#128272; Iniciar sesión
    </button>
    <a class="mobile-menu-btn" href="{{ url('/promociones') }}">
      &#127881; Promociones
    </a>
    <a
      class="mobile-menu-btn"
      href="{{ url('/registro-usuario') }}"
    >
      &#128100; Para usuarios
    </a>
    <a
      class="mobile-menu-btn"
      href="{{ url('/registro-negocio') }}"
    >
      &#127978; Para negocios
    </a>
    <a
      class="mobile-menu-btn"
      href="{{ url('/registro-repartidor') }}"
    >
      &#128693; Para repartidores
    </a>
  </div>
  <div class="nav-underline"></div>
</nav>

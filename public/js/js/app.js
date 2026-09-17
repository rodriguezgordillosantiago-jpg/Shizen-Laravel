/* ── Countdown timers ──────────────────────────────────────────────── */
var countdowns = {};

function formatTime(secs) {
    var h = Math.floor(secs / 3600);
    var m = Math.floor((secs % 3600) / 60);
    var s = secs % 60;
    return [h, m, s]
        .map(function (n) {
            return String(n).padStart(2, "0");
        })
        .join(":");
}

function startCountdowns() {
    ALL_PROMOS.forEach(function (p) {
        countdowns[p.id] = p.endsIn;
    });
    setInterval(function () {
        ALL_PROMOS.forEach(function (p) {
            if (countdowns[p.id] > 0) {
                countdowns[p.id]--;
                var el = document.getElementById("timer-" + p.id);
                if (el) el.textContent = formatTime(countdowns[p.id]);
            }
        });
    }, 1000);
}

/* ── View management ───────────────────────────────────────────────── */
function showView(viewId) {
    document.querySelectorAll(".view").forEach(function (v) {
        v.classList.remove("active");
    });
    var target = document.getElementById("view-" + viewId);
    if (target) target.classList.add("active");
    window.scrollTo({ top: 0, behavior: "smooth" });

    var floatBtn = document.getElementById("floatBtn");
    if (!floatBtn) return;
    if (viewId === "home") {
        floatBtn.classList.remove("hidden");
    } else {
        floatBtn.classList.add("hidden");
    }
}

/* ── Category cards ────────────────────────────────────────────────── */
function renderCategoryCards() {
    var scroll = document.getElementById("categoryScroll");
    if (!scroll) return;
    scroll.innerHTML = "";
    CATEGORIES.forEach(function (cat) {
        var card = document.createElement("a");
        card.className = "cat-card";
        card.href = new URL(
            "php/categorias.php?categoria=" + cat.dbId,
            document.baseURI,
        ).href;
        card.style.background = cat.bg;
        card.innerHTML =
            '<div class="cat-emoji-wrap">' +
            cat.icon +
            "</div>" +
            '<div class="cat-label">' +
            cat.label +
            "</div>" +
            '<div class="cat-pill">Ver platos</div>';
        scroll.appendChild(card);
    });
}

function renderCategoryPage() {
    var itemsGrid = document.getElementById("itemsGrid");
    var catName = document.getElementById("catName");
    if (!itemsGrid || !catName) return;
    if (typeof CATEGORIES === "undefined" || !CATEGORIES.length) return;

    var params = new URLSearchParams(window.location.search);
    var catParam = params.get("categoria") || "1";
    var category =
        CATEGORIES.find(function (c) {
            return String(c.dbId) === catParam || c.id === catParam;
        }) || CATEGORIES[0];

    if (category) {
        var catHero = document.getElementById("catHero");
        var catIcon = document.getElementById("catIcon");
        var catDesc = document.getElementById("catDesc");
        if (catHero && category.coverImg && !catHero.style.backgroundImage) {
            catHero.style.backgroundImage = "url('" + category.coverImg + "')";
        }
        if (catIcon && !catIcon.textContent.trim())
            catIcon.textContent = category.icon;
        if (catName && !catName.textContent.trim())
            catName.textContent = category.label;
        if (catDesc && !catDesc.textContent.trim())
            catDesc.textContent = category.description;
    }
}

/* ── Promos ────────────────────────────────────────────────────────── */
function renderPromoFilters() {
    var wrap = document.getElementById("promoFilterTabs");
    if (!wrap) return;
    wrap.innerHTML = "";
    PROMO_CATS.forEach(function (cat) {
        var btn = document.createElement("button");
        btn.className = "filter-tab" + (cat === "Todos" ? " active" : "");
        btn.textContent = cat;
        btn.onclick = function () {
            filterPromos(cat, btn);
        };
        wrap.appendChild(btn);
    });
}

function filterPromos(cat, btn) {
    document.querySelectorAll(".filter-tab").forEach(function (b) {
        b.classList.remove("active");
    });
    btn.classList.add("active");
    document.querySelectorAll(".promo-card").forEach(function (card) {
        card.style.display =
            cat === "Todos" || card.dataset.cat === cat ? "" : "none";
    });
}

function renderPromos() {
    var grid = document.getElementById("promosGrid");
    if (!grid) return;
    grid.innerHTML = "";
    ALL_PROMOS.forEach(function (p) {
        var div = document.createElement("div");
        div.className = "promo-card";
        div.dataset.cat = p.cat;
        div.innerHTML =
            '<div class="promo-card-img" style="background-image:url(\'' +
            p.img +
            "')\">" +
            '<span class="promo-badge" style="background:' +
            p.badgeColor +
            '">' +
            p.badge +
            "</span>" +
            '<span class="promo-tag-top">' +
            p.tag +
            "</span>" +
            "</div>" +
            '<div class="promo-card-body">' +
            "<h3>" +
            p.emoji +
            " " +
            p.title +
            "</h3>" +
            '<div class="promo-restaurant">&#127978; ' +
            p.restaurant +
            "</div>" +
            '<p class="promo-desc">' +
            p.desc +
            "</p>" +
            '<div class="promo-timer">&#8987; <span id="timer-' +
            p.id +
            '">' +
            formatTime(p.endsIn) +
            "</span></div>" +
            '<div class="promo-prices">' +
            '<span class="promo-price-new">' +
            p.price +
            "</span>" +
            '<span class="promo-price-old">' +
            p.originalPrice +
            "</span>" +
            '<span class="promo-discount">-' +
            p.discount +
            "</span>" +
            "</div>" +
            '<a class="btn-view-dishes" href="php/categorias.php">Ver platos</a>' +
            "</div>";
        grid.appendChild(div);
    });
}

/* ── Login modal ───────────────────────────────────────────────────── */
var loginModalOpen = false;

function showLoginErrorFromQuery() {
    if (!window.loginError && window.location.hash !== "#error") return;
    var error = document.getElementById("login-error");
    if (error) error.hidden = false;
    openLoginModal();
}

if (window.shizenLayoutReady) {
    window.shizenLayoutReady.then(showLoginErrorFromQuery);
}

function toggleLoginModal() {
    if (loginModalOpen) closeLoginModal();
    else openLoginModal();
}

function openLoginModal() {
    document.getElementById("loginModal").classList.add("open");
    document.getElementById("ingresoBtn").classList.add("active");
    loginModalOpen = true;
}

function openAccessModal() {
    var registerModal = document.getElementById("registerModal");
    if (registerModal) registerModal.classList.remove("open");
    openLoginModal();
}

/* ── Carrito y checkout ────────────────────────────────────────────── */
var cart = JSON.parse(localStorage.getItem("shizenCart") || "[]");
var UI_TEXT = {
    emptyCart: "Tu carrito está vacío.",
    decrease: "Disminuir cantidad",
    increase: "Aumentar cantidad",
    offerTitle: "¡Aprovecha esta oferta!",
    offerDescription:
        "Crea tu cuenta gratis y aplica el descuento automáticamente en tu primer pedido.",
    accountTitle: "Crea tu cuenta gratis",
    accountDescription:
        "Para comprar en Shizen necesitas una cuenta. ¡Es gratis y rápido!",
};

function formatMoney(value) {
    return "$" + Number(value).toLocaleString("es-CO");
}

function saveCart() {
    localStorage.setItem("shizenCart", JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    var count = document.getElementById("cartCount");
    if (count)
        count.textContent = cart.reduce(function (sum, item) {
            return sum + item.quantity;
        }, 0);
}

function addToCart(product) {
    if (cart.length > 0) {
        var first = cart[0];
        var currentBus = first.businessId || first.restaurant;
        var newBus = product.businessId || product.restaurant;

        if (currentBus && newBus && String(currentBus) !== String(newBus)) {
            var currentName = first.restaurant || "Restaurante #" + currentBus;
            var newName = product.restaurant || "Restaurante #" + newBus;

            var confirmSwitch = confirm(
                'Tu carrito actualmente contiene productos de "' +
                    currentName +
                    '".\n\n' +
                    "Cada pedido solo puede contener platos de un mismo restaurante para evitar conflictos de entrega.\n\n" +
                    '¿Deseas vaciar tu carrito e iniciar un nuevo pedido con productos de "' +
                    newName +
                    '"?',
            );

            if (confirmSwitch) {
                cart = [];
            } else {
                return;
            }
        }
    }

    var existing = cart.find(function (item) {
        return item.id === product.id;
    });
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            restaurant: product.restaurant,
            businessId: product.businessId || product.id_negocio || 0,
            quantity: 1,
        });
    }
    saveCart();
    renderCart();
    openCart();
}

function cartTotal() {
    return cart.reduce(function (sum, item) {
        return sum + item.price * item.quantity;
    }, 0);
}

function renderCart() {
    var items = document.getElementById("cartItems");
    var total = document.getElementById("cartTotal");
    var checkout = document.getElementById("checkoutButton");
    if (!items || !total) return;
    items.innerHTML = cart.length
        ? cart
              .map(function (item) {
                  return (
                      '<div class="cart-item"><div><strong>' +
                      item.name +
                      "</strong><small>" +
                      item.restaurant +
                      "</small><span>" +
                      formatMoney(item.price) +
                      '</span></div><div class="cart-quantity"><button type="button" aria-label="' +
                      UI_TEXT.decrease +
                      '" title="' +
                      UI_TEXT.decrease +
                      '" onclick="changeCartQuantity(' +
                      item.id +
                      ',-1)">−</button><b>' +
                      item.quantity +
                      '</b><button type="button" aria-label="' +
                      UI_TEXT.increase +
                      '" title="' +
                      UI_TEXT.increase +
                      '" onclick="changeCartQuantity(' +
                      item.id +
                      ',1)">+</button></div></div>'
                  );
              })
              .join("")
        : '<p class="cart-empty">' + UI_TEXT.emptyCart + "</p>";
    total.textContent = formatMoney(cartTotal());
    if (checkout) checkout.disabled = cart.length === 0;
}

function changeCartQuantity(id, delta) {
    var item = cart.find(function (entry) {
        return entry.id === id;
    });
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0)
        cart = cart.filter(function (entry) {
            return entry.id !== id;
        });
    saveCart();
    renderCart();
}

function openCart() {
    renderCart();
    var modal = document.getElementById("cartModal");
    if (modal) modal.classList.add("open");
}

function closeCart() {
    var modal = document.getElementById("cartModal");
    if (modal) modal.classList.remove("open");
}

function handleCartOverlayClick(event) {
    if (event.target === document.getElementById("cartModal")) closeCart();
}

function openCheckout() {
    if (!cart.length) return;
    closeCart();
    document.getElementById("checkoutModal").classList.add("open");
}

function closeCheckout() {
    document.getElementById("checkoutModal").classList.remove("open");
}

function handleCheckoutOverlayClick(event) {
    if (event.target === document.getElementById("checkoutModal"))
        closeCheckout();
}

function prepareCheckout(event) {
    if (!cart.length) {
        event.preventDefault();
        return;
    }
    document.getElementById("checkoutItems").value = JSON.stringify(
        cart.map(function (item) {
            return { id: item.id, quantity: item.quantity };
        }),
    );
}

updateCartCount();

function closeLoginModal() {
    document.getElementById("loginModal").classList.remove("open");
    document.getElementById("ingresoBtn").classList.remove("active");
    loginModalOpen = false;
}

function handleLoginOverlayClick(e) {
    if (e.target === document.getElementById("loginModal")) closeLoginModal();
}

function togglePassword(button) {
    var input = button.parentElement.querySelector("input");
    if (!input) return;
    var showPassword = input.type === "password";
    input.type = showPassword ? "text" : "password";
    button.classList.toggle("is-visible", showPassword);
    button.setAttribute(
        "aria-label",
        showPassword ? "Ocultar contraseña" : "Mostrar contraseña",
    );
    button.setAttribute(
        "title",
        showPassword ? "Ocultar contraseña" : "Mostrar contraseña",
    );
}

function openAccountLogin() {
    openAccessModal();
}

function goToRegistration() {
    closeLoginModal();
    var registrationSection = document.getElementById("registro");
    if (!registrationSection) {
        window.location.assign("index.html#registro");
        return;
    }
    registrationSection.scrollIntoView({
        behavior: "smooth",
        block: "start",
    });
    registrationSection.focus({ preventScroll: true });
}

/* ── Register modal ────────────────────────────────────────────────── */
function openRegisterModal(isOffer) {
    closeLoginModal();
    var icon = document.getElementById("regModalIcon");
    var title = document.getElementById("regModalTitle");
    var sub = document.getElementById("regModalSub");
    var warning = document.getElementById("offerWarning");
    if (isOffer) {
        icon.textContent = "🏷️";
        title.textContent = UI_TEXT.offerTitle;
        sub.textContent = UI_TEXT.offerDescription;
        warning.classList.remove("hidden");
    } else {
        icon.textContent = "🌱";
        title.textContent = UI_TEXT.accountTitle;
        sub.textContent = UI_TEXT.accountDescription;
        warning.classList.add("hidden");
    }
    document.getElementById("registerModal").classList.add("open");
}

function closeRegisterModal() {
    document.getElementById("registerModal").classList.remove("open");
}

function handleRegisterOverlayClick(e) {
    if (e.target === document.getElementById("registerModal"))
        closeRegisterModal();
}

/* ── Join form steps ───────────────────────────────────────────────── */
function validateSection(sectionId) {
    var section = document.getElementById(sectionId);
    var fields = section.querySelectorAll("input, select, textarea");

    for (var i = 0; i < fields.length; i++) {
        var field = fields[i];
        if (
            field.type !== "checkbox" &&
            field.required &&
            !field.value.trim()
        ) {
            field.setCustomValidity("Este campo es obligatorio.");
        } else {
            field.setCustomValidity("");
        }
        if (!field.checkValidity()) {
            field.reportValidity();
            field.focus();
            return false;
        }
    }
    return true;
}

function nextStep(prefix) {
    if (!validateSection(prefix + "-step1")) return;
    document.getElementById(prefix + "-step1").classList.add("hidden");
    document.getElementById(prefix + "-step2").classList.remove("hidden");
    var dot1 = document.getElementById(prefix + "-dot-1");
    var dot2 = document.getElementById(prefix + "-dot-2");
    var line1 = document.getElementById(prefix + "-line-1");
    dot1.className = "step-dot done";
    dot1.textContent = "✓";
    line1.classList.add("done");
    dot2.classList.add("active");
}

function submitJoin(prefix) {
    if (!validateSection(prefix + "-step2")) return;
    document.getElementById(prefix + "-step2").classList.add("hidden");
    document.getElementById(prefix + "-success").classList.remove("hidden");
    var dot2 = document.getElementById(prefix + "-dot-2");
    dot2.className = "step-dot done";
    dot2.textContent = "✓";
}

function resetJoinForm(prefix) {
    document.getElementById(prefix + "-step1").classList.remove("hidden");
    document.getElementById(prefix + "-step2").classList.add("hidden");
    document.getElementById(prefix + "-success").classList.add("hidden");
    var dot1 = document.getElementById(prefix + "-dot-1");
    var dot2 = document.getElementById(prefix + "-dot-2");
    var line1 = document.getElementById(prefix + "-line-1");
    dot1.className = "step-dot active";
    dot1.textContent = "1";
    dot2.className = "step-dot";
    dot2.textContent = "2";
    line1.className = "step-line";
}

/* ── Mobile nav ────────────────────────────────────────────────────── */
function toggleMobileMenu() {
    var menu = document.getElementById("mobileMenu");
    menu.classList.toggle("open");
    document.getElementById("hamburgerBtn").textContent =
        menu.classList.contains("open") ? "✕" : "☰";
}

/* ── Init ──────────────────────────────────────────────────────────── */
function initializeApp() {
    renderCategoryCards();
    renderCategoryPage();
    renderPromoFilters();
    renderPromos();
    startCountdowns();

    if (window.location.hash === "#registro") {
        var registrationSection = document.getElementById("registro");
        if (registrationSection) {
            registrationSection.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
            registrationSection.focus({ preventScroll: true });
        }
    }
}

window.shizenLayoutReady.then(initializeApp);

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeLoginModal();
});

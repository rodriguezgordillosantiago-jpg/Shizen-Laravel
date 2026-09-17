<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="../" />
  <title>Promociones | Shizen</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/nav.css" />
  <link rel="stylesheet" href="css/pages.css" />
  <link rel="stylesheet" href="css/modals.css" />
</head>
<body>

  <!-- Componente de navegación reutilizable -->
  <header id="navigation">
    <?php include __DIR__ . '/navegacion.html'; ?>
  </header>

  <main id="app-content">
    <section class="promos-page">

      <div class="page-header">
        <a class="btn-back" href="index.php" aria-label="Volver">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>
        </a>
        <div>
          <h2>Promociones</h2>
          <p>Descuentos exclusivos en restaurantes veganos</p>
        </div>
      </div>

      <div class="promos-inner">
        <div class="promos-grid">

          <?php if (empty($promociones)): ?>
            <p class="promos-empty">No hay promociones activas en este momento.</p>
          <?php else: ?>
            <?php foreach ($promociones as $promo): ?>
              <div class="promo-card">
                <div class="promo-card-img" style="background-image: url('<?= htmlspecialchars($promo['imagen_url'] ?: 'assets/image-6.png') ?>')">
                  <span class="promo-badge">Oferta</span>
                  <span class="promo-tag-top">Destacado</span>
                </div>
                <div class="promo-card-body">
                  <h3>🌱 <?= htmlspecialchars($promo['promo_nombre']) ?></h3>
                  <div class="promo-restaurant">🏪 <?= htmlspecialchars($promo['negocio_nombre']) ?></div>
                  <p class="promo-desc"><?= htmlspecialchars($promo['promo_desc']) ?></p>
                  <a class="btn-view-dishes" href="php/categorias.php">Ver platos</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      </div>

    </section>
  </main>

  <!-- Componente de modales reutilizable -->
  <div id="overlays">
    <?php include __DIR__ . '/modales.php'; ?>
  </div>

  <script src="js/data.js"></script>
  <script>
    window.shizenLayoutReady = Promise.resolve();
  </script>
  <script src="js/app.js"></script>
</body>
</html>

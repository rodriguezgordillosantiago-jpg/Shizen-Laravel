<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Categorías | Shizen</title>
  <base href="../">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/nav.css" />
  <link rel="stylesheet" href="css/home.css" />
  <link rel="stylesheet" href="css/pages.css" />
  <link rel="stylesheet" href="css/modals.css" />
</head>
<body>
  <header id="navigation">
    <?php include __DIR__ . '/navegacion.html'; ?>
  </header>

  <main id="app-content">
    <section class="cat-page">
      <?php
        $defaultCovers = [
          1 => 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=1200&h=500&fit=crop&auto=format',
          2 => 'https://images.unsplash.com/photo-1473093226795-af9932fe5856?w=1200&h=500&fit=crop&auto=format',
          3 => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=1200&h=500&fit=crop&auto=format',
          4 => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=1200&h=500&fit=crop&auto=format',
          5 => 'https://images.unsplash.com/photo-1622597467836-f3285f2131b8?w=1200&h=500&fit=crop&auto=format',
          6 => 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=1200&h=500&fit=crop&auto=format',
          7 => 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=1200&h=500&fit=crop&auto=format',
        ];

        $catId = (int)($categoria['id'] ?? 1);
        $rawCover = trim($categoria['cover_img'] ?? '');
        $coverImg = '';

        if (!empty($rawCover)) {
          if (preg_match('#^https?://#i', $rawCover)) {
            $coverImg = $rawCover;
          } else {
            $clean = preg_replace('#^\.\.?/#', '', $rawCover);
            if (file_exists(__DIR__ . '/../' . $clean)) {
              $coverImg = $clean;
            } else {
              $catalogPath = preg_replace('#^shizen_movil/#i', '', $clean);
              if (file_exists(__DIR__ . '/../../public/images/catalogo/' . $catalogPath)) {
                $coverImg = '/images/catalogo/' . $catalogPath;
              }
            }
          }
        }

        if (empty($coverImg)) {
          $coverImg = $defaultCovers[$catId] ?? $defaultCovers[1];
        }

        $coverImg  = htmlspecialchars($coverImg);
        $catIcon   = !empty($categoria['icon'])         ? htmlspecialchars($categoria['icon'])         : '🍔';
        $catName   = !empty($categoria['nombre'])       ? htmlspecialchars($categoria['nombre'])       : 'Comidas Rápidas';
        $catDesc   = !empty($categoria['descripcion'])  ? htmlspecialchars($categoria['descripcion'])  : 'Burgers, tacos y wraps plant-based para cuando el tiempo apremia.';
      ?>
      <div class="cat-hero" id="catHero"
           style="background-image:url('<?= $coverImg ?>')">
        <div class="cat-hero-overlay"></div>
        <a class="cat-hero-back" href="index.php" aria-label="Volver">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>
        </a>
        <div class="cat-hero-info">
          <div class="cat-hero-icon-name">
            <span class="cat-hero-icon" id="catIcon"><?= $catIcon ?></span>
            <span class="cat-hero-name" id="catName"><?= $catName ?></span>
          </div>
          <p class="cat-hero-desc" id="catDesc"><?= $catDesc ?></p>
        </div>
      </div>

      <div class="cat-items-inner">
        <div class="items-grid" id="itemsGrid">

          <?php if (empty($platos)): ?>
            <p class="cat-empty" style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: #666; font-size: 1.05rem;">No hay platos registrados en esta categoría.</p>
          <?php else: ?>
            <?php foreach ($platos as $plato): ?>
              <?php
                $rawImg = trim($plato['imagen_url'] ?? '');
                $imgFallback = 'assets/image-6.png';
                $imgUrl = $imgFallback;

                if (!empty($rawImg)) {
                  if (preg_match('#^https?://#i', $rawImg)) {
                    $imgUrl = htmlspecialchars($rawImg);
                  } else {
                    $clean = preg_replace('#^\.\.?/#', '', $rawImg);
                    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

                    // 1. ¿Está dentro de shizenhome/?
                    if (file_exists(__DIR__ . '/../' . $clean)) {
                      $imgUrl = htmlspecialchars($clean);
                    }
                    // 2. Catálogo local migrado.
                    elseif (file_exists(__DIR__ . '/../../public/images/catalogo/' . preg_replace('#^shizen_movil/#i', '', $clean))) {
                      $imgUrl = htmlspecialchars('/images/catalogo/' . preg_replace('#^shizen_movil/#i', '', $clean));
                    }
                    // 3. ¿Está en la raíz de htdocs/?
                    elseif (file_exists(__DIR__ . '/../../' . $clean) || ($docRoot && file_exists($docRoot . '/' . $clean))) {
                      $imgUrl = htmlspecialchars('../' . $clean);
                    }
                    // 4. Respetar la ruta original
                    else {
                      $imgUrl = htmlspecialchars($rawImg);
                    }
                  }
                }

                $ratingNegocio = !empty($plato['negocio_calificacion']) ? number_format((float)$plato['negocio_calificacion'], 1) : '4.8';
              ?>
              <div class="dish-card">
                <div class="dish-img" style="background-image: url('<?= $imgUrl ?>')">
                  <span class="dish-tag-badge">Vegano</span>
                </div>
                <div class="dish-body">
                  <div class="dish-name"><?= htmlspecialchars($plato['plato_nombre']) ?></div>
                  <div class="dish-restaurant">🏪 <?= htmlspecialchars($plato['negocio_nombre']) ?></div>
                  <div class="dish-meta">
                    <span>🕐 20 min</span>
                  </div>
                  <div class="dish-price-row">
                    <div class="dish-prices">
                      <span class="dish-price">$<?= number_format((float)$plato['precio'], 0, ',', '.') ?></span>
                    </div>
                    <button
                      class="btn-add-cart"
                      type="button"
                      onclick='addToCart(<?= json_encode([
                        "id" => (int) $plato["id"],
                        "name" => $plato["plato_nombre"],
                        "price" => (float) $plato["precio"],
                        "restaurant" => $plato["negocio_nombre"]
                      ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                    >Añadir al carrito</button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      </div>
    </section>
  </main>

  <!-- Modales reutilizables -->
  <div id="overlays">
    <?php include __DIR__ . '/modales.php'; ?>
  </div>

  <script src="js/data.js?v=20260827-1"></script>
  <script>
    window.shizenLayoutReady = Promise.resolve();
  </script>
  <script src="js/app.js?v=20260827-1"></script>
</body>
</html>

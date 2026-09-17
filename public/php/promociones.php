<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../clases/Plato.php';

try {
    $promociones = Plato::obtenerPromocionesActivas();
} catch (Throwable $e) {
    error_log('Error cargando promociones: ' . $e->getMessage());
    $promociones = [];
}

// Llamamos a la vista que dibuja el diseño
include __DIR__ . '/../forms/promociones.php';
?>
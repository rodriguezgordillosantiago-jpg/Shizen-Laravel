<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../clases/Categoria.php';
require_once __DIR__ . '/../clases/Plato.php';

$rawCategoria = $_GET['categoria'] ?? '';
$categoria = null;
$platos = [];

try {
    $categoria = Categoria::buscar($rawCategoria);
    $categoriaId = (int)($categoria['id'] ?? 0);

    if ($categoriaId > 0) {
        $platos = Plato::obtenerPorCategoria($categoriaId);
    }
} catch (Throwable $e) {
    error_log('Error al cargar categoría en POO: ' . $e->getMessage());
    $categoria = null;
    $platos = [];
}

include __DIR__ . '/../forms/categorias.php';
?>


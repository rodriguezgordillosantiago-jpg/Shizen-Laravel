<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

/**
 * Plato
 * ---------------------------------------------------------
 * Modelo de Plato / Producto del menú y promociones.
 * ---------------------------------------------------------
 */
class Plato {
    /** Obtiene los platos asociados a una categoría */
    public static function obtenerPorCategoria(int $categoriaId): array {
        if ($categoriaId <= 0) return [];

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT
                m.id_menu_item AS id,
                m.nombre        AS plato_nombre,
                m.descripcion   AS plato_desc,
                m.precio,
                m.stock,
                m.imagen_url,
                COALESCE(n.nombre, \'Restaurante Shizen\') AS negocio_nombre
            FROM menu_items m
            LEFT JOIN negocios n ON m.id_negocio = n.id_negocio
            WHERE m.id_categoria = ?
            ORDER BY RAND()
        ');
        $stmt->execute([$categoriaId]);
        return $stmt->fetchAll();
    }

    /** Obtiene todas las promociones activas */
    public static function obtenerPromocionesActivas(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('
            SELECT 
                p.id_promocion AS id,
                p.nombre AS promo_nombre,
                p.descripcion AS promo_desc,
                p.imagen_url,
                COALESCE(n.nombre, \'Restaurante Shizen\') AS negocio_nombre
            FROM promociones p
            LEFT JOIN negocios n ON p.id_negocio = n.id_negocio
            WHERE p.activo = 1
        ');
        return $stmt->fetchAll();
    }
}

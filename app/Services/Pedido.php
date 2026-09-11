<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

/**
 * Pedido
 * ---------------------------------------------------------
 * Modelo de Pedido para registrar órdenes y detalles
 * de compra con control transaccional y validación de stock.
 * ---------------------------------------------------------
 */
class Pedido {
    /**
     * Procesa y registra un pedido completo en la base de datos
     */
    public static function registrar(
        array $datosCliente,
        array $items,
        ?int $idUsuario = null
    ): int {
        if (empty($items)) {
            throw new InvalidArgumentException('El carrito está vacío.');
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $productIds = array_values(array_unique(array_map(
                static fn($item): int => (int)($item['id'] ?? 0),
                $items
            )));
            $productIds = array_values(array_filter($productIds, static fn(int $id): bool => $id > 0));

            if (!$productIds) {
                throw new RuntimeException('Productos inválidos.');
            }

            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $stmt = $pdo->prepare("SELECT id, negocio_id, nombre, precio, stock FROM menu_items WHERE id IN ($placeholders) FOR UPDATE");
            $stmt->execute($productIds);
            $products = [];
            foreach ($stmt->fetchAll() as $product) {
                $products[(int)$product['id']] = $product;
            }

            $total = 0;
            $detalles = [];
            $negocioId = 1;
            $descripcionItems = [];

            foreach ($items as $item) {
                $id = (int)($item['id'] ?? 0);
                $quantity = (int)($item['quantity'] ?? 0);

                if (!isset($products[$id]) || $quantity < 1 || $quantity > 99) {
                    throw new RuntimeException('Un producto del carrito ya no está disponible.');
                }
                if ($products[$id]['stock'] !== null && $quantity > (int)$products[$id]['stock']) {
                    throw new RuntimeException('No hay stock suficiente para un producto.');
                }

                $negocioId = (int)($products[$id]['negocio_id'] ?? 1);
                $price = (int)$products[$id]['precio'];
                $subtotal = $price * $quantity;
                $total += $subtotal;
                $descripcionItems[] = "{$quantity}x {$products[$id]['nombre']}";
                $detalles[] = [
                    'id_menu_item' => $id,
                    'valor'        => $price,
                    'cantidad'     => $quantity,
                ];
            }

            // Si no se proporcionó idUsuario explícito, buscar por correo
            if (!$idUsuario && !empty($datosCliente['correo'])) {
                $stmtUser = $pdo->prepare('SELECT id_usuario FROM usuario WHERE email = ? LIMIT 1');
                $stmtUser->execute([trim($datosCliente['correo'])]);
                $foundUser = $stmtUser->fetch();
                if ($foundUser) {
                    $idUsuario = (int)$foundUser['id_usuario'];
                }
            }

            if (!$idUsuario) {
                // Asignar al usuario cliente de prueba por defecto si no está logueado
                $idUsuario = 18;
            }

            $direccionEntrega = trim(($datosCliente['direccion'] ?? '') . ' ' . ($datosCliente['localidad'] ?? ''));
            $descripcion = implode(', ', $descripcionItems);

            // 1. Insertar en tabla `pedido`
            $stmtOrder = $pdo->prepare('
                INSERT INTO pedido (
                    id_usuario, id_negocio, descripcion, direccion_entrega, estado, fecha_creacion
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ');

            $stmtOrder->execute([
                $idUsuario,
                $negocioId,
                $descripcion,
                $direccionEntrega,
                'Pendiente'
            ]);

            $pedidoId = (int)$pdo->lastInsertId();

            // 2. Insertar en tabla `detalle_pedido`
            $detailStmt = $pdo->prepare('
                INSERT INTO detalle_pedido (
                    id_pedido, id_menu_item, valor, fecha, cantidad
                ) VALUES (?, ?, ?, NOW(), ?)
            ');

            foreach ($detalles as $detalle) {
                $detailStmt->execute([
                    $pedidoId,
                    $detalle['id_menu_item'],
                    $detalle['valor'],
                    $detalle['cantidad']
                ]);
            }

            // 3. Insertar en tabla `compra`
            $tokenTransaccion = bin2hex(random_bytes(16));
            $stmtCompra = $pdo->prepare('
                INSERT INTO compra (
                    id_usuario, id_pedido, fecha_pago, total, metodo_pago, estado, token_transaccion
                ) VALUES (?, ?, NOW(), ?, ?, ?, ?)
            ');

            $metodoPago = !empty($datosCliente['metodo_pago']) ? trim($datosCliente['metodo_pago']) : 'Contraentrega';

            $stmtCompra->execute([
                $idUsuario,
                $pedidoId,
                $total,
                $metodoPago,
                'Pendiente',
                $tokenTransaccion
            ]);

            $pdo->commit();
            return $pedidoId;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Error en Pedido::registrar: ' . $e->getMessage());
            throw $e;
        }
    }
}


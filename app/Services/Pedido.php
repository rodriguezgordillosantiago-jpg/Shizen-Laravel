<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class Pedido
{
    public function forUser(int $userId): Collection
    {
        return DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->leftJoin('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->leftJoin('negocios as n', 'n.id_negocio', '=', 'p.id_negocio')
            ->where('p.id_usuario', $userId)
            ->select(
                'p.id_pedido',
                'p.descripcion',
                'p.direccion_entrega',
                'p.estado as pedido_estado',
                'p.fecha_creacion',
                'c.total',
                'c.estado as compra_estado',
                'e.estado as entrega_estado',
                'e.codigo_entrega',
                'e.fecha_confirmacion',
                'e.id_repartidor',
                'n.nombre as negocio_nombre'
            )
            ->orderByDesc('p.fecha_creacion')
            ->get();
    }

    public function create(int $userId, array $items, array $data): int
    {
        if (!$items) {
            throw new InvalidArgumentException('El carrito está vacío.');
        }

        $productIds = collect($items)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->values();
        $products = DB::table('menu_items')
            ->whereIn('id_menu_item', $productIds)
            ->select('id_menu_item as id', 'id_negocio', 'nombre', 'precio')
            ->get()
            ->keyBy('id');

        if ($products->count() !== $productIds->count()) {
            throw new RuntimeException('Uno de los platos ya no está disponible.');
        }

        $total = 0;
        $description = [];
        foreach ($items as $item) {
            $id = (int) ($item['id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity < 1 || $quantity > 99 || !$products->has($id)) {
                throw new RuntimeException('La cantidad de un plato no es válida.');
            }

            $product = $products->get($id);
            $total += (int) $product->precio * $quantity;
            $description[] = $quantity . 'x ' . $product->nombre;
        }

        $businessId = (int) ($products->first()->id_negocio ?? 1);
        $address = trim($data['direccion'] . ' ' . ($data['localidad'] ?? ''));

        return DB::transaction(function () use ($userId, $items, $products, $total, $description, $businessId, $address, $data): int {
            $orderId = ((int) DB::table('pedido')->lockForUpdate()->max('id_pedido')) + 1;
            $purchaseId = ((int) DB::table('compra')->lockForUpdate()->max('id_compra')) + 1;

            DB::table('pedido')->insert([
                'id_pedido' => $orderId,
                'id_usuario' => $userId,
                'id_negocio' => $businessId,
                'descripcion' => implode(', ', $description),
                'direccion_entrega' => $address,
                'estado' => 'Pendiente',
                'fecha_creacion' => now(),
            ]);

            foreach ($items as $item) {
                $product = $products[(int) $item['id']];
                DB::table('detalle_pedido')->insert([
                    'id_pedido' => $orderId,
                    'id_menu_item' => $product->id,
                    'valor' => (int) $product->precio,
                    'fecha' => now(),
                    'cantidad' => (int) $item['quantity'],
                ]);
            }

            DB::table('compra')->insert([
                'id_compra' => $purchaseId,
                'id_usuario' => $userId,
                'id_pedido' => $orderId,
                'fecha_pago' => now(),
                'total' => $total,
                'metodo_pago' => $data['metodo_pago'],
                'estado' => 'Pendiente',
                'token_transaccion' => Str::random(32),
            ]);

            DB::table('entrega')->insert([
                'id_compra' => $purchaseId,
                'estado' => 'Pendiente',
                'codigo_entrega' => (string) random_int(100000, 999999),
            ]);

            return $orderId;
        });
    }

    public function findForUser(int $userId, int $orderId): ?object
    {
        return DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->leftJoin('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->leftJoin('repartidor as r', 'r.id_repartidor', '=', 'e.id_repartidor')
            ->where('p.id_pedido', $orderId)
            ->where('p.id_usuario', $userId)
            ->select(
                'p.*',
                'c.id_compra',
                'c.total',
                'c.metodo_pago',
                'e.estado as entrega_estado',
                'e.codigo_entrega',
                'e.fecha_confirmacion',
                'e.id_repartidor',
                'r.nombre as repartidor_nombre',
                'r.apellido as repartidor_apellido'
            )
            ->first();
    }

    public function isRated(int $userId, int $orderId): bool
    {
        return DB::table('calificacion')
            ->where('id_usuario', $userId)
            ->where('id_pedido', $orderId)
            ->exists();
    }

    public function confirmDelivery(int $userId, int $orderId, string $code): bool
    {
        $delivery = DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->join('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->where('p.id_pedido', $orderId)
            ->where('p.id_usuario', $userId)
            ->where('e.codigo_entrega', $code)
            ->whereNull('e.fecha_confirmacion')
            ->select('p.id_pedido', 'e.id_entrega')
            ->first();

        if (!$delivery) {
            return false;
        }

        DB::transaction(function () use ($delivery): void {
            DB::table('entrega')
                ->where('id_entrega', $delivery->id_entrega)
                ->update([
                    'estado' => 'Entregado',
                    'fecha_confirmacion' => now(),
                ]);
            DB::table('pedido')
                ->where('id_pedido', $delivery->id_pedido)
                ->update(['estado' => 'Recibido']);
        });

        return true;
    }

    public function rate(int $userId, int $orderId, int $score, ?string $comment): bool
    {
        $order = DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->join('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->where('p.id_pedido', $orderId)
            ->where('p.id_usuario', $userId)
            ->whereNotNull('e.fecha_confirmacion')
            ->select('p.id_negocio', 'e.id_repartidor')
            ->first();
        if (!$order || !$order->id_repartidor) {
            return false;
        }

        DB::table('calificacion')->updateOrInsert(
            ['id_usuario' => $userId, 'id_negocio' => $order->id_negocio],
            [
                'id_repartidor' => $order->id_repartidor,
                'id_pedido' => $orderId,
                'puntuacion' => $score,
                'comentario' => $comment,
                'fecha' => now(),
            ]
        );

        return true;
    }
}

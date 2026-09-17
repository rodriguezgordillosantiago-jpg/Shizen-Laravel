<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LogicException;

class Repartidor extends Usuario
{
    public function findByUserId(int $userId): ?object
    {
        return DB::table('repartidor as r')
            ->join('usuario as u', 'u.id_usuario', '=', 'r.id_usuario')
            ->where('r.id_usuario', $userId)
            ->select('r.*', 'u.email', 'u.ciudad')
            ->first();
    }

    public function activeDeliveries(int $courierId): Collection
    {
        return $this->deliveries($courierId, 'active');
    }

    public function availableDeliveries(): Collection
    {
        return $this->deliveries(null, 'available');
    }

    public function history(int $courierId): Collection
    {
        return $this->deliveries($courierId, 'history');
    }

    public function rating(int $courierId): float
    {
        return (float) (DB::table('calificacion')
            ->where('id_repartidor', $courierId)
            ->avg('puntuacion') ?? 0);
    }

    public function acceptDelivery(int $courierId, int $deliveryId): bool
    {
        return DB::transaction(function () use ($courierId, $deliveryId): bool {
            $activeCount = DB::table('entrega')
                ->where('id_repartidor', $courierId)
                ->whereNull('fecha_confirmacion')
                ->lockForUpdate()
                ->count();

            if ($activeCount >= 4) {
                throw new LogicException('Ya tienes el máximo de cuatro pedidos activos.');
            }

            return DB::table('entrega')
                ->where('id_entrega', $deliveryId)
                ->whereNull('id_repartidor')
                ->where('estado', 'Pendiente')
                ->update([
                    'id_repartidor' => $courierId,
                    'fecha_asignacion' => now(),
                    'estado' => 'Asignado',
                ]) > 0;
        });
    }

    public function advanceDelivery(int $courierId, int $deliveryId): ?string
    {
        $delivery = DB::table('entrega')
            ->join('compra', 'compra.id_compra', '=', 'entrega.id_compra')
            ->where('entrega.id_entrega', $deliveryId)
            ->where('entrega.id_repartidor', $courierId)
            ->select('entrega.id_entrega', 'entrega.estado', 'entrega.fecha_entrega', 'compra.id_pedido')
            ->first();

        if (!$delivery) {
            return null;
        }

        if ($delivery->fecha_entrega) {
            return 'already_completed';
        }

        if ($delivery->estado !== 'En camino') {
            DB::transaction(function () use ($delivery): void {
                DB::table('entrega')
                    ->where('id_entrega', $delivery->id_entrega)
                    ->update(['estado' => 'En camino']);
                DB::table('pedido')
                    ->where('id_pedido', $delivery->id_pedido)
                    ->update(['estado' => 'En camino']);
            });

            return 'picked_up';
        }

        DB::transaction(function () use ($delivery): void {
            DB::table('entrega')
                ->where('id_entrega', $delivery->id_entrega)
                ->update([
                    'estado' => 'Entregado',
                    'fecha_entrega' => now(),
                ]);
            DB::table('pedido')
                ->where('id_pedido', $delivery->id_pedido)
                ->update(['estado' => 'Entregado']);
        });

        return 'delivered';
    }

    public function updateProfile(object $courier, array $data): void
    {
        $nombre = trim((string) $data['nombre']);
        $apellido = trim((string) $data['apellido']);

        DB::transaction(function () use ($courier, $data, $nombre, $apellido): void {
            DB::table('usuario')
                ->where('id_usuario', $courier->id_usuario)
                ->update([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                ]);
            DB::table('repartidor')
                ->where('id_repartidor', $courier->id_repartidor)
                ->update([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'vehiculo' => $data['vehiculo'],
                ]);
        });
    }

    private function deliveries(?int $courierId, string $type): Collection
    {
        $query = DB::table('entrega as e')
            ->join('compra as c', 'c.id_compra', '=', 'e.id_compra')
            ->join('pedido as p', 'p.id_pedido', '=', 'c.id_pedido')
            ->join('usuario as u', 'u.id_usuario', '=', 'p.id_usuario')
            ->join('negocios as n', 'n.id_negocio', '=', 'p.id_negocio')
            ->select(
                'e.id_entrega',
                'e.estado as entrega_estado',
                'e.fecha_asignacion',
                'e.fecha_entrega',
                'e.codigo_entrega',
                'p.id_pedido',
                'p.descripcion',
                'p.direccion_entrega',
                'p.estado as pedido_estado',
                'p.fecha_creacion',
                'c.total',
                'u.nombre as cliente_nombre',
                'u.apellido as cliente_apellido',
                'n.nombre as negocio_nombre'
            )
            ->orderByDesc('p.fecha_creacion');

        if ($type === 'available') {
            return $query
                ->whereNull('e.id_repartidor')
                ->where('e.estado', 'Pendiente')
                ->get();
        }

        $query->where('e.id_repartidor', $courierId);

        if ($type === 'history') {
            return $query->whereNotNull('e.fecha_entrega')->get();
        }

        return $query
            ->whereNull('e.fecha_confirmacion')
            ->whereNull('e.fecha_entrega')
            ->get();
    }
}

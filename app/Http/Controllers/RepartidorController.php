<?php

namespace App\Http\Controllers;

use App\Services\Repartidor;
use Illuminate\Http\Request;
use LogicException;

class RepartidorController extends Controller
{
    public function __construct(private readonly Repartidor $repartidores)
    {
    }

    public function index(Request $request, string $section = 'inicio')
    {
        $courier = $this->courier($request);
        $active = $this->repartidores->activeDeliveries($courier->id_repartidor);
        $available = $this->repartidores->availableDeliveries();
        $history = $this->repartidores->history($courier->id_repartidor);
        $stats = [
            'active' => $active->count(),
            'available' => $available->count(),
            'delivered' => $history->whereNotNull('fecha_entrega')->count(),
            'rating' => $this->repartidores->rating($courier->id_repartidor),
        ];

        return view('repartidor', [
            'courier' => $courier,
            'section' => in_array($section, ['inicio', 'activos', 'pedidos', 'historial', 'perfil'], true) ? $section : 'inicio',
            'active' => $active,
            'available' => $available,
            'history' => $history,
            'stats' => $stats,
            'online' => (bool) $request->session()->get('repartidor_online', true),
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $this->courier($request);
        $online = !((bool) $request->session()->get('repartidor_online', true));
        $request->session()->put('repartidor_online', $online);

        return back()->with('courier_success', $online ? 'Ahora estás disponible para recibir pedidos.' : 'Has pausado la recepción de pedidos.');
    }

    public function accept(Request $request, int $deliveryId)
    {
        $courier = $this->courier($request);
        try {
            $updated = $this->repartidores->acceptDelivery($courier->id_repartidor, $deliveryId);
        } catch (LogicException $exception) {
            abort(422, $exception->getMessage());
        }

        if (!$updated) {
            return back()->withErrors(['delivery' => 'Este pedido ya fue tomado por otro repartidor.']);
        }

        return back()->with('courier_success', 'Pedido aceptado. Revisa la dirección de recogida.');
    }

    public function advance(Request $request, int $deliveryId)
    {
        $courier = $this->courier($request);
        $result = $this->repartidores->advanceDelivery($courier->id_repartidor, $deliveryId);
        abort_unless($result, 404);

        if ($result === 'already_completed') {
            return back()->with('courier_success', 'La entrega ya fue marcada como realizada.');
        }

        if ($result === 'picked_up') {
            return back()->with('courier_success', 'Pedido recogido. Ahora dirígete al destino.');
        }

        return back()->with('courier_success', 'Entrega marcada. El cliente debe confirmar con su código.');
    }

    public function updateProfile(Request $request)
    {
        $courier = $this->courier($request);
        $data = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
            'apellido' => ['required', 'string', 'min:2', 'max:100'],
            'vehiculo' => ['required', 'in:Bicicleta,Moto,Patineta electrica,Carro'],
        ]);

        $this->repartidores->updateProfile($courier, $data);

        $authUser = $request->session()->get('auth_user');
        $request->session()->put('auth_user', array_merge($authUser, [
            'nombre' => trim($data['nombre']),
            'apellido' => trim($data['apellido']),
        ]));

        return back()->with('courier_success', 'Tu perfil fue actualizado.');
    }

    private function courier(Request $request): object
    {
        $authUser = $request->session()->get('auth_user');
        abort_unless(is_array($authUser) && !empty($authUser['id_usuario']), 403);

        $courier = $this->repartidores->findByUserId((int) $authUser['id_usuario']);
        abort_unless($courier, 403);

        return $courier;
    }
}

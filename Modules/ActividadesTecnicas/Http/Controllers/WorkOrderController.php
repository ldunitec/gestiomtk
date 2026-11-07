<?php

namespace Modules\ActividadesTecnicas\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse, Request};
use Modules\ActividadesTecnicas\Models\WorkOrder;
use Modules\ActividadesTecnicas\Services\WorkOrderService;

class WorkOrderController extends Controller
{
    public function __construct(private WorkOrderService $service) {}

    private function data(Request $request): array
    {
        $data = $request->validate(['title'=>'required|string|max:180','description'=>'nullable|string|max:10000',
            'priority'=>'required|in:low,normal,high,urgent','scheduled_at'=>'nullable|date_format:Y-m-d\TH:i',
            'agenda_tipo'=>'sometimes|in:Interno,Taller,Visita,Llamada',
            'agenda_responsable'=>['sometimes','nullable',\Illuminate\Validation\Rule::in(array_merge(array_keys(\Modules\Produccion\Http\Controllers\AgendaController::responsablesOrden()), ['todos']))]]);
        $data['scheduled_at'] = empty($data['scheduled_at']) ? null
            : CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data['scheduled_at'], config('app.timezone', 'America/Tegucigalpa'))->second(0)->utc();
        $data['_agenda'] = array_intersect_key($data, array_flip(['agenda_tipo','agenda_responsable']));
        unset($data['agenda_tipo'], $data['agenda_responsable']);
        return $data;
    }

    private function responseOrder(WorkOrder $order, string $message): RedirectResponse
    {
        $response = redirect()->route('actividades-tecnicas.ordenes.show', $order)->with('success', $message);
        if ($order->agenda_id) {
            $sync = app(\Modules\Produccion\Http\Controllers\AgendaController::class)->enviarATodoist($order->agenda);
            if (!($sync['success'] ?? false)) { $response->with('warning', 'La orden y la agenda se guardaron. Todoist quedó pendiente; puede reintentarlo desde la agenda.'); }
        }
        return $response;
    }

    public function fromAgenda(Request $request, \Modules\Produccion\Entities\Agenda $agenda)
    {
        $order = app(\Modules\ActividadesTecnicas\Services\AgendaIntegration::class)->convert($agenda, $request->user()->id);
        if ($request->expectsJson()) { return response()->json(['success'=>true, 'url'=>route('actividades-tecnicas.ordenes.show', $order)]); }
        return redirect()->route('actividades-tecnicas.ordenes.show', $order)->with('success', 'Actividad vinculada a la orden técnica.');
    }

    public function index(): View
    {
        $orders = WorkOrder::with(['client','location','assignments.technician'])->latest('id')->paginate(20);
        return view('actividades-tecnicas::orders.index', compact('orders'));
    }

    public function create(): View { return view('actividades-tecnicas::orders.create'); }

    public function store(Request $request): RedirectResponse
    {
        $order = $this->service->create($this->data($request), $request->user()->id);
        return $this->responseOrder($order, 'Orden de trabajo creada y programada en la agenda.');
    }

    public function show(WorkOrder $workOrder): View
    {
        $workOrder->load(['agenda','client','location','serviceType','assignments.technician','timeLogs','gpsLogs','evidences','materials','statusHistory.user']);
        return view('actividades-tecnicas::orders.show', ['order'=>$workOrder]);
    }

    public function edit(WorkOrder $workOrder): View
    {
        abort_if($workOrder->status === 'cancelled', 422, 'Una orden cancelada no se puede editar.');
        return view('actividades-tecnicas::orders.edit', ['order'=>$workOrder]);
    }

    public function update(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->service->update($workOrder, $this->data($request), $request->user()->id);
        return $this->responseOrder($workOrder->fresh(), 'Orden y agenda actualizadas correctamente.');
    }

    public function destroy(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $request->validate(['confirmar'=>'accepted']);
        $this->service->cancel($workOrder, $request->user()->id);
        return $this->responseOrder($workOrder->fresh(), 'Orden cancelada y retirada de la agenda activa. Se conserva su historial.');
    }
}


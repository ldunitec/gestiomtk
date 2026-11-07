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
            'priority'=>'required|in:low,normal,high,urgent','scheduled_at'=>'nullable|date_format:Y-m-d\TH:i']);
        $data['scheduled_at'] = empty($data['scheduled_at']) ? null
            : CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data['scheduled_at'], config('app.timezone', 'America/Tegucigalpa'))->second(0)->utc();
        return $data;
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
        return redirect()->route('actividades-tecnicas.ordenes.show', $order)->with('success', 'Orden de trabajo creada correctamente.');
    }

    public function show(WorkOrder $workOrder): View
    {
        $workOrder->load(['client','location','serviceType','assignments.technician','timeLogs','gpsLogs','evidences','materials','statusHistory.user']);
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
        return redirect()->route('actividades-tecnicas.ordenes.show', $workOrder)->with('success', 'Orden actualizada correctamente.');
    }

    public function destroy(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $request->validate(['confirmar'=>'accepted']);
        $this->service->cancel($workOrder, $request->user()->id);
        return redirect()->route('actividades-tecnicas.ordenes.show', $workOrder)->with('success', 'Orden cancelada. Se conserva su historial.');
    }
}

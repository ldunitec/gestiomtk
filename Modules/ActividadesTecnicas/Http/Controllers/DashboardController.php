<?php

namespace Modules\ActividadesTecnicas\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\ActividadesTecnicas\Models\WorkOrder;

class DashboardController extends Controller
{
    public function health(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['module'=>'ActividadesTecnicas','status'=>'ok']);
    }

    public function index(): View
    {
        $stats = [
            'total' => WorkOrder::count(),
            'assigned' => WorkOrder::where('status', 'assigned')->count(),
            'in_progress' => WorkOrder::where('status', 'in_progress')->count(),
            'completed' => WorkOrder::where('status', 'completed')->count(),
            'pending' => WorkOrder::whereIn('status', ['new', 'assigned', 'accepted'])->count(),
        ];

        $recentOrders = WorkOrder::with(['client', 'location', 'assignments.technician'])
            ->latest()
            ->limit(10)
            ->get();

        return view('actividades-tecnicas::dashboard', compact('stats', 'recentOrders'));
    }
}

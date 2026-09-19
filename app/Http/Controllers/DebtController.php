<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index()
{


// dd( ' index del bets');

    $debts = Debt::where('user_id', auth()->id())->get();
    return view('admin.debts.index', compact('debts'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    return view('admin.debts.create');

    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $request->validate([
        'type' => 'required',
        'name' => 'required',
        'original_amount' => 'required|numeric'
    ]);

    Debt::create([
        'user_id' => auth()->id(),
        'type' => $request->type,
        'name' => $request->name,
        'original_amount' => $request->original_amount,
        'current_balance' => $request->original_amount,
        'total_installments' => $request->total_installments,
        'due_date' => $request->due_date,
    ]);

    return redirect()->route('admin.debts.index');
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function pay(Request $request, Debt $debt)
{
    $request->validate([
        'amount' => 'required|numeric|min:0.01'
    ]);

    Payment::create([
        'debt_id' => $debt->id,
        'payment_date' => now(),
        'amount' => $request->amount,
        'comment' => $request->comment
    ]);

    $debt->current_balance -= $request->amount;
    $debt->paid_installments += 1;

    if ($debt->current_balance <= 0) {
        $debt->status = false;
        $debt->current_balance = 0;
    }

    $debt->save();
return response()->json([
    'success' => true
]);
}

public function dashboardFinanzas()
{
    $user = auth()->id();
    $hoy = Carbon::today();

$proximas = Debt::where('user_id', auth()->id())
    ->whereNotNull('due_date')
    ->where('due_date', '>=', $hoy)
    ->where('due_date', '<=', $hoy->copy()->addDays(5))
    ->get();

$vencidas = Debt::where('user_id', auth()->id())
    ->whereNotNull('due_date')
    ->where('due_date', '<', $hoy)
    ->where('status',1)
    ->get();

    $totalDeudas = Debt::where('user_id',$user)
        ->where('status',1)
        ->sum('current_balance');

    $tarjetas = Debt::where('user_id',$user)
        ->where('type','Tarjeta')
        ->sum('current_balance');

    $suscripciones = Debt::where('user_id',$user)
        ->where('type','Suscripcion')
        ->sum('current_balance');

    $prestamos = Debt::where('user_id',$user)
        ->where('type','Prestamo')
        ->sum('current_balance');

    return view('admin.debts.dashboard-finanzas', compact(

 
    'totalDeudas',
    'tarjetas',
    'suscripciones',
    'prestamos',
    'proximas',
    'vencidas'
));
}




}

<?php

namespace App\Console\Commands;

use App\Models\Debt;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
  

public function handle()
{
    $hoy = Carbon::today();

    $subs = Debt::where('is_recurring', true)
        ->whereDate('due_date', '<=', $hoy)
        ->get();

    foreach($subs as $sub){

        // Agregar saldo nuevamente
        $sub->current_balance += $sub->recurring_amount;

        // Calcular nueva fecha
        if($sub->frequency == 'Mensual'){
            $sub->due_date = Carbon::parse($sub->due_date)->addMonth();
        }

        if($sub->frequency == 'Anual'){
            $sub->due_date = Carbon::parse($sub->due_date)->addYear();
        }

        $sub->save();
    }
}
}

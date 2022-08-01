<?php

namespace App\Console\Commands;

use App\Http\Controllers\CycleCountController;
use App\Models\CycleCounting;
use App\Models\Inventory;
use Illuminate\Console\Command;

class DailyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto approve pending cycle countings that passed approved_before date ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cycleCountings = CycleCounting::where('approve_before', '<', now())->get();
        foreach ($cycleCountings as $cycleCounting) {
            $inventory = Inventory::findOrFail($cycleCounting->schedule->sku->inventory_id);
            $ira =
                number_format((1 - abs($cycleCounting->variance) / $inventory->qty_on_hand), 3, '.', "");
            $cycleCounting['inv_rec_accuracy'] = $ira;
            $cycleCounting['status'] = "COMPLETED";
            $cycleCounting->save();
            $inventory->qty_on_hand += $cycleCounting->variance;
            $inventory->save();
        }

        $this->info('Daily Update has been send successfully');
    }
}

<?php

namespace App\Listeners;

use App\Events\StockCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StockCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\StockCreated  $event
     * @return void
     */
    public function handle(StockCreated $event)
    {
        //
    }
}

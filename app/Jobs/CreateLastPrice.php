<?php

namespace App\Jobs;

use App\Models\Market;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CreateLastPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $market_id;
    private $market_name;
    private $last_price;
    private $arrow;

    public function __construct($market_id,$market_name,$last_price,$arrow=null)
    {
        //
        $this->market_id    = $market_id;
        $this->market_name  = $market_name;
        $this->last_price   = $last_price;
        $this->arrow        = $arrow;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $last_price = Market::where('id',$this->market_id)->value('last_price');
        if(is_null($last_price) || (($this->last_price - $last_price) >=0 )) {
            $this->arrow = '↑';
        }else{
            $this->arrow = '↓';
        }
        $time = date('Y-m-d H:i:s',time());
        Market::updateOrCreate(
            ['id'     => $this->market_id],
            [
                'last_price'    => $this->last_price,
                'arrow'         => $this->arrow,
                'updated_at'    => $time
            ]);
    }

}

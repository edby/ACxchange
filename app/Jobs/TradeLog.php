<?php

namespace App\Jobs;

use App\Models\XchangeDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TradeLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tradeData;

    /**
     * Create a new job instance.
     * @param $tradeData array
     * @return void
     */
    public function __construct($tradeData)
    {
        $this->tradeData = $tradeData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            XchangeDetail::create($this->tradeData);
        }catch (\Exception $exception){
            $exception->getMessage();
        }
    }
}

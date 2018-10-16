<?php

namespace App;

use App\Tool\TimeCalc;
use Illuminate\Support\Facades\Log;

class KLine extends Base
{
    protected $guarded = [];

    public $timestamps = false;

    /*
     * 时间节点
    */
    const NODE = [
        1 => [15, 'i', 'minutes'],
        2 => [30, 'i','minutes'],
        3 => [1, 'H', 'hour'],
        4 => [2, 'H', 'hours'],
        5 => [6, 'H', 'hours'],
        6 => [1, 'd', 'day'],
        7 => [2, 'd', 'days'],
        8 => [1, 'm', 'month'],
        9 => [2, 'm', 'months'],
        10 => [6, 'm', 'months'],
        11 => [1, 'W', 'week'],
        12 => [2, 'W', 'weeks'],
        13 => [1, 'Y', 'years'],
    ];

    /*
     * 时间标记
     */
    const TAG = [
        'Y',
        'm',
        'd',
        'H',
        'i',
    ];

    const BASICS = [
        14 => [1, 'i', 'minute'],
    ];

    const BENCHEnd= [
        1 => 900,
        2 => 1800,
        3 => 3600,
        4 => 7200,
        5 => 21600,
        6 => 86400,
        7 => 172800,
        8 => 604800,
        9 => 1209600,
        10 => 2419200,
        11 => 4838400,
        12 => 14515200,
    ];


    const BENCH = [
        1 => 60,
        2 => 300,
        3 => 900,
        4 => 1800,
        5 => 3600,
        6 => 7200,
        7 => 21600,
        8 => 43200,
        9 => 86400,
        10 => 604800,
    ];


    const BENCH_GUPIAO = [
        1 => 1800,
        2 => 9000,
        3 => 27000,
        4 => 54000,
        5 => 108000,
        6 => 216000,
        7 => 648000,
        8 => 1296000,
        9 => 2592000,
        10 => 18144000,
    ];

    /**
     * 获取币种信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currencySets()
    {
        return $this->belongsTo('App\CurrencySet','curr_id', 'curr_id');
    }

    /**
     * 一次生成多个kLine
     * @param $time
     * @param $price
     * @param $currId
     * @param $tradeCurr
     * @param $total
     */
    public function createKLines($time,$price,$currId,$tradeCurr,$total)
    {
        $timeCalc = new TimeCalc();
        $datumType = 1;
        while (13>$datumType){
            $datumTime = $timeCalc->modular($datumType,$time);
            $this->createKLine($time,$price,$currId,$tradeCurr,$total,$datumType,$datumTime);
            ++$datumType;
        }
    }

    /**
     * 指定生成一个KLine
     * @param $time
     * @param $price
     * @param $currId
     * @param $tradeCurr
     * @param $total
     * @param $datumType
     * @param $datumTime
     * @return mixed
     */
    public function createKLine($time,$price,$currId,$tradeCurr,$total,$datumType,$datumTime)
    {
        $datumData = $this->where([
            ['datum_time','=',strtotime($datumTime)],
            ['datum_type','=',$datumType],
            ['curr_id','=',$currId],
        ])->first(['close']);

        if (empty($datumData)){

            $datumPre = $this->where([
                ['datum_type','=',$datumType],
                ['curr_id','=',$currId],
            ])->orderByDesc('datum_time')->first(['close']);

            if (empty($datumPre)) $datumPre['close'] = CurrencySet::where('curr_id',$currId)->value('price_btc');;

            $resComp = bccomp($price,$datumPre['close'],8);

            $open = $datumPre['close'];
            if (strtotime($datumTime) === $time ) $open = $price;

            if ($resComp === 0){
                $low = $datumPre['close'];
                $high = $datumPre['close'];
            }elseif ($resComp === 1){
                $low = $datumPre['close'];
                $high = $price;
            }else{
                $low = $price;
                $high = $datumPre['close'];
            }

            $close = $price;
            $volume = $total;

            $lowHigh = bcadd($low,$high,8);
            $average = bcdiv($lowHigh,2,8);

            $data = [
                'open'=> $open,
                'low'=> $low,
                'high'=> $high,
                'close'=> $close,
                'average'=> $average,
                'volume'=> $volume,
                'datum_type'=> $datumType,
                'curr_id'=> $currId,
                'curr_abb'=> $tradeCurr,
                'late_time'=> $time,
                'datum_time'=> strtotime($datumTime),
                'add_time'=> time(),
            ];

            $mode = $this->create($data);
        }else{
            $resComp = bccomp($price,$datumData['low'],8);
            if ($resComp < 0) $data['low'] = $price;
            $data['low'] = $datumData['low'];

            $resComp = bccomp($price,$datumData['high'],8);
            if ($resComp > 0) $data['high'] = $price;
            $data['high'] = $datumData['high'];

            $lowHigh = bcadd($data['low'],$data['high'],8);
            $average = bcdiv($lowHigh,2,8);

            $volume = bcadd($datumData['volume'],$total,8);

            $data = [
                'close'=> $price,
                'average'=> $average,
                'volume'=> $volume,
                'datum_type'=> $datumType,
                'curr_id'=> $currId,
                'curr_abb'=> $tradeCurr,
                'late_time'=> $time,
                'datum_time'=> strtotime($datumTime),
                'add_time'=> time(),
            ];
            $mode = $this->where([
                ['datum_time','=',strtotime($datumTime)],
                ['datum_type','=',$datumType],
                ['curr_id','=',$currId],
            ])->update($data);
        }
        return $mode;
    }
}
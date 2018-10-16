<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/4/9
 * Time: 18:20
 */

namespace App\Library\Trade;

use App\CurrencySet;
use App\KLine;
use App\Tool\TimeCalc;

class Order
{
    public function kDataCopy($nowStamp,$type,$currId,$limit)
    {
        $timeCalc = new TimeCalc();

        $endTime = $nowStamp-KLine::BENCH[$type];

        $startDatum  = $timeCalc->modularCopy(14,$nowStamp);
        $endDatum  = $timeCalc->modularCopy(14,$endTime);

        $kData = KLine::where([
            ['curr_id','=',$currId],
            ['datum_type','=',14],
            ['datum_time','>=',$endDatum]
        ])->orderBy('id','desc')
            ->get(['open','low','volume','high','close','average','datum_time','late_time']);

        //当订单细节表没有数据时候 返回
        if ($kData->isEmpty()){

            $remote = KLine::where([
                ['curr_id','=',$currId],
                ['datum_type','=',14],
                ['datum_time','<',$endDatum]
            ])->orderBy('id','desc')
                ->first(['close']);
            if (empty($remote))  $price = CurrencySet::where([
                ['curr_id','=',$currId],
                ['switch','=',10],
            ])->value('price_btc');
        }

    }

    /**
     * 返回K线图数据
     * @param int $nowStamp 开始时间戳
     * @param int $endStamp  结束时间戳
     * @param int $currId  货币类型ID
     * @param int $datumType 请求K线图类型 15 30分钟
     * @param int $limit 请求条数
     * @return array
     */
    public function kData($nowStamp,$endStamp,$currId,$datumType,$limit)
    {
        $limitAnchor = $limit;
        $gapTime = KLine::NODE[$datumType][0];
        $tag = KLine::NODE[$datumType][1];
        $unit = KLine::NODE[$datumType][2];
        $data = [];

        $kData = KLine::where([
            ['curr_id','=',$currId],
            ['datum_type','=',$datumType],
        ])->orderBy('id','desc')
            ->get(['open','low','volume','high','close','average','datum_time','late_time']);

        $timeCalc = new TimeCalc();

        //当订单细节表没有数据时候 返回
        if ($kData->isEmpty()){
            $data = $this->orderReverse($datumType,$currId,$nowStamp,$endStamp,$tag,$gapTime,$unit,$limit,$limitAnchor);
            return $data;
        }

        $datum = $timeCalc->modular($datumType, $nowStamp);

        $endDatum = $timeCalc->modular($datumType,$endStamp);

        $counter = 0;

        while (strtolower($datum) >= strtolower($endDatum) ){

            if ($limit == 0) break;
            //当kline 表里面没有数据的时候 自己生成数据
            if (empty($kData[$counter]) || strtolower($kData[$counter]['datum_time']) < strtolower($datum)){

                $price = $kData[$counter]['close'];

                $data[] = $this->assArray($price,$price,$price,$price,$price,0,$datumType,$currId,$datum,$datum);
            }else {
                //当数据库里面有数据 则使用数据库数据
                $data[] = $kData[$counter];
                ++$counter;
            }
            $datum = $timeCalc->timeReverse($tag,$gapTime,$unit,$datum);
            --$limit;
        }
        return $data;
    }

    /**
     * 一个订单都没有得时候
     * @param int $currId
     * @param string $datumType
     * @param int $time
     * @param int $endTime
     * @param string $tag
     * @param int $gapTime
     * @param string $unit
     * @param int $limit
     * @param int $limitAnchor
     * @return array
     */
    public function orderReverse($datumType,$currId,$time,$endTime,$tag,$gapTime,$unit,$limit,$limitAnchor)
    {
        $data = [];
        $timeCalc = new TimeCalc();
        $price = CurrencySet::where('curr_id',$currId)->value('price_btc');
        $datum  = $timeCalc->modular($datumType,$time);
        $endDatum  = $timeCalc->modular($datumType,$endTime);
        while ($limit > 0){
            if (strtotime($datum) <= strtotime($endDatum)) break;
            if ($limit != $limitAnchor) $time = $datum;
            $data[] = $this->assArray($price,$price,$price,$price,$price,0,$datumType,$currId,$time,$datum);
            $datum = $timeCalc->timeReverse($tag,$gapTime,$unit,$datum);
            --$limit;
        }
        return $data;
    }

    /**
     * 生成 K线 柱
     * @param double $open      开始金额
     * @param double $low      最低金额
     * @param double $high     最高金额
     * @param double $close    收盘金额
     * @param double $average  平均金额
     * @param double $volume   成交金额
     * @param int    $datumType   基准类型
     * @param int    $currId       货币类型Id
     * @param int    $lateTime     最后时间
     * @param int    $datumTime    基准时间
     * @return array
     */
    public function assArray($open,$low,$high,$close,$average,$volume,$datumType,$currId,$lateTime,$datumTime)
    {
        $currAbb = CurrencySet::where('curr_id',$currId)->value('curr_abb');
        return $data = [
            'open'=> $open,
            'low'=> $low,
            'high'=> $high,
            'close'=> $close,
            'average'=> $average,
            'volume'=> $volume,
            'datum_type'=> $datumType,
            'curr_id'=> $currId,
            'curr_abb'=> $currAbb,
            'late_time'=> $lateTime,
            'datum_time'=> $datumTime,
            'add_time'=> time(),
        ];
    }

}
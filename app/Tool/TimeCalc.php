<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/4/11
 * Time: 12:00
 */

namespace App\Tool;

use App\KLine;

/**
 * 时间计算
 * Class TimeCalc
 * @package App\Tool
 */
class TimeCalc
{
    /**
     * 算出 时间戳 所属时间范围
     * @param string $type   NODE 的键名
     * @param string $timeStamp  被计算的时间戳
     * @return false|string
     */
    public function modular($type, $timeStamp)
    {
        $tag = KLine::NODE[$type][1];
        $anchor = date($tag,$timeStamp);
        $gapTime = KLine::NODE[$type][0];

        if ('m'=== $tag){
            $modular = ceil($anchor/$gapTime);
            $resetTime =  $gapTime*$modular+1;
        }else{
            $modular = $anchor%$gapTime;
            $resetTime =  $anchor-$modular;
        }
//        dump('type =>'.$type.' #anchor =>'.$anchor.' #gapTime =>'.$gapTime.' #modular =>'.$modular.' #resetTime =>'.$resetTime);
        $unit = KLine::NODE[$type][2];
        if ('W' === $tag){
            $date = $this->specialWeek($timeStamp,$gapTime);
        }elseif ('Y'=== $tag){
            $date = $this->specialYear($anchor);
        }elseif('m'=== $tag){
            $date = $this->specialMonth($resetTime,$timeStamp);
        }else{
            $resetTime = str_pad($resetTime,2,"0",STR_PAD_LEFT);
            $date = $this->dateAssembly($tag,$timeStamp,$resetTime);
            $date = date('Y-m-d H:i:s',strtotime("+$gapTime $unit",strtotime($date)));
        }
        return $date;
    }

    /**
     * 月份的特殊处理
     * @param int $resetTime
     * @param int $timeStamp  当前时间戳
     * @return false|string
     */
    public function specialMonth($resetTime,$timeStamp)
    {
        if ($resetTime > 12){
            return date('Y-m-d H:i:s',strtotime("-1 day",strtotime((date('Y',$timeStamp)+1).'-01-01')));
        }else{
            $month = str_pad($resetTime,2,"0",STR_PAD_LEFT);
            return date('Y-m-d H:i:s',strtotime("-1 day",strtotime(date('Y',$timeStamp).'-'.$month.'-01')));
        }
    }

    /**
     * 年特殊处理
     * @param int $anchor 当年年份 2018
     * @return false|string
     */
    public function specialYear($anchor)
    {
        $year = $anchor+1;
        return date('Y-m-d H:i:s',strtotime("-1 day",strtotime($year.'-01-01')));
    }

    /**
     * 周特殊处理
     * @param int  $timeStamp 时间戳
     * @param int $gapTime
     * @return false|string
     */
    public function specialWeek($timeStamp,$gapTime)
    {
        $anchor = date('W',$timeStamp);
        dump($anchor);
        $week = date('w',$timeStamp);
        $modular = $anchor%$gapTime;
        if ($week > 0) $week = 7-$week;
        $gap = ($week+$modular*7)*86400;
        return date('Y-m-d H:i:s',strtotime(date('Y-m-d',$timeStamp))+$gap);
    }


    /**
     * 时间得逆运算  更具给出时间 单位前一段时间
     * @param string  $tag      代表时间得标记 i H m W
     * @param int     $gapTime  时间间隔  15分钟  2小时
     * @param string  $unit     单位 英文 hours minutes
     * @param string  $datum    基准时间 运算出来得
     * @return false|string
     */
    public function timeReverse($tag,$gapTime,$unit,$datum)
    {
        if ('W' === $tag){
            $datum = date('Y-m-d H:i:s',strtotime("-$gapTime $unit",strtotime($datum)));
        }elseif ('Y'=== $tag){
            $datum = date('Y-m-d H:i:s',strtotime("-$gapTime $unit",strtotime($datum)));
        }elseif('m'=== $tag){
            $anchor = date($tag,strtotime($datum));
            $month = $anchor-$gapTime+1;
            $datum = date('Y-m-d H:i:s',strtotime("-1 day",strtotime(date('Y',strtotime($datum)).'-'.$month.'-01')));
        }else{
            $datum = date('Y-m-d H:i:s',strtotime("-$gapTime $unit",strtotime($datum)));
        }
        return $datum;
    }


    /**
     * 副本计算
     * @param $type
     * @param $timeStamp
     * @return false|string
     */
    public function modularCopy($type,$timeStamp)
    {
        $tag = KLine::BASICS[$type][1];
        $anchor = date($tag,$timeStamp);
        $gapTime = KLine::BASICS[$type][0];

        $modular = $anchor%$gapTime;
        $resetTime =  $anchor-$modular;
//        dump('type =>'.$type.' #anchor =>'.$anchor.' #gapTime =>'.$gapTime.' #modular =>'.$modular.' #resetTime =>'.$resetTime);
        $unit = KLine::BASICS[$type][2];
        $resetTime = str_pad($resetTime,2,"0",STR_PAD_LEFT);
        $date = $this->dateAssembly($tag,$timeStamp,$resetTime);
        $date = date('Y-m-d H:i:s',strtotime("+$gapTime $unit",strtotime($date)));
        return $date;
    }



    /**
     * 天 时 分 计算
     * @param string $tag
     * @param int $timeStamp
     * @param int $resetTime
     * @return string
     */
    private function dateAssembly($tag,$timeStamp,$resetTime)
    {
        $tagSet = KLine::TAG;
        $date = '';
        foreach ($tagSet as $key => $value){
            if ($tag === $value){
                $date .= $resetTime;
                $tmpKey = 4-$key;
                if ( $tmpKey > 0) $date .= str_repeat('00',$tmpKey);
                break;
            }else{
                $date .= date($value,$timeStamp);
            }
        }
        return $date;
    }
}
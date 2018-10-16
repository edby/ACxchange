<?php

namespace App\Http\Controllers\FrontEnd;

use App\Models\Blockchain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
/** 区块连操作记录
 * Class BlockchainController
 * @package App\Http\Controllers
 */
class BlockchainController extends Controller
{
    //
    public function move($client,$user_id,$target_id,$amount,$order_id)
    {
        $move_status = $client->move($user_id,$target_id,$amount);
        if($move_status) {
            //id , coin , user_id , target_id ,amount status ,created_at ,updated_at ,order_id
        }
    }


    public static function insertBuyOption($market_name,$user_id,$target_id,$amount,$order_id,$type=2)
    {
        $currency = explode('_',$market_name);
        $coin = $currency[1];
        $blockchain_opt = new Blockchain();

        $blockchain_opt->order_id = $order_id;
        $blockchain_opt->currency = $coin;
        $blockchain_opt->user_id = $user_id;
        $blockchain_opt->target_id = $target_id;
        $blockchain_opt->amount = $amount;
        $blockchain_opt->status = 0;
        $blockchain_opt->type = $type;
        $blockchain_opt->save();

        //TODO 是否需要记录涉及的区块链操作次数
    }

    public static function insertBuyExchangeOption($market_name,$user_id,$target_id,$amount,$order_id,$type=2)
    {
        $currency = explode('_',$market_name);
        $coin = $currency[0];
        $blockchain_opt = new Blockchain();

        $blockchain_opt->order_id   = $order_id;
        $blockchain_opt->currency   = $coin;
        $blockchain_opt->user_id    = $user_id;
        $blockchain_opt->target_id  = $target_id;
        $blockchain_opt->amount     = $amount; //---金额转多了 TODO
        $blockchain_opt->status     = 0;
        $blockchain_opt->type       = $type;
        $blockchain_opt->save();
    }

    public static function insertSellOption($market_name,$user_id,$target_id,$amount,$order_id,$type=1)
    {
        $currency = explode('_',$market_name);
        $coin = $currency[1];
        $blockchain_opt = new Blockchain();

        $blockchain_opt->order_id = $order_id;
        $blockchain_opt->currency = $coin;
        $blockchain_opt->user_id = $user_id;
        $blockchain_opt->target_id = $target_id;
        $blockchain_opt->amount = $amount;
        $blockchain_opt->status = 0;
        $blockchain_opt->type = $type;
        $blockchain_opt->save();
    }

    public static function insertSellOptionTrans($market_name,$user_id,$target_id,$amount,$order_id,$type=1)
    {
        $currency = explode('_',$market_name);
        $coin = $currency[0];
        $blockchain_opt = new Blockchain();

        $blockchain_opt->order_id = $order_id;
        $blockchain_opt->currency = $coin;
        $blockchain_opt->user_id = $user_id;
        $blockchain_opt->target_id = $target_id;
        $blockchain_opt->amount = $amount;
        $blockchain_opt->status = 0;
        $blockchain_opt->type = $type;
        $blockchain_opt->save();
    }

    public static function insertSellFeeOption($market_name,$user_id,$target_id,$amount,$order_id,$type=1)
    {
        $currency = explode('_',$market_name);
        $coin = $currency[1];
        $blockchain_opt = new Blockchain();

        $blockchain_opt->order_id = $order_id;
        $blockchain_opt->currency = $coin;
        $blockchain_opt->user_id = $user_id;
        $blockchain_opt->target_id = $target_id;
        $blockchain_opt->amount = $amount;
        $blockchain_opt->status = 0;
        $blockchain_opt->type = $type;
        $blockchain_opt->fee_or_trade = 0;
        $blockchain_opt->save();
    }

    public static function insertSellFirstOption($market_name,$user_id,$target_id,$amount,$order_id,$type=1)
    {
        $currency = explode('_',$market_name);
        $coin = $currency[0];
        $blockchain_opt = new Blockchain();

        $blockchain_opt->order_id = $order_id;
        $blockchain_opt->currency = $coin;
        $blockchain_opt->user_id = $user_id;
        $blockchain_opt->target_id = $target_id;
        $blockchain_opt->amount = $amount;
        $blockchain_opt->status = 0;
        $blockchain_opt->type = $type;
        $blockchain_opt->save();
    }


    public static function InsertCancelOrder($order_id,$currency,$user_id,$target_id,$amount,$status,$type,$fee_or_trade)
    {
        $blockchain_opt = new Blockchain();

        $blockchain_opt->order_id = $order_id;
        $blockchain_opt->currency = $currency;
        $blockchain_opt->user_id = $user_id;
        $blockchain_opt->target_id = $target_id;
        $blockchain_opt->amount = $amount;
        $blockchain_opt->status = 0;
        $blockchain_opt->type = $type;
        $blockchain_opt->fee_or_trade = 3;  //3   交易撤回的区块链操作  //4取消订单退还手续费
        $blockchain_opt->save();

        return $blockchain_opt;
    }

    //后台 MOVE 操作 直接转账----------------------------------------
    public static function slaveMove($currency,$target_id,$amount,$order_id)
    {
        $blockchain_opt_target = new Blockchain();
        $blockchain_opt_target->order_id = $order_id;
        $blockchain_opt_target->currency = $currency;
        $blockchain_opt_target->user_id = 2;
        $blockchain_opt_target->target_id = $target_id;
        $blockchain_opt_target->amount = $amount;
        $blockchain_opt_target->status = 1;
        $blockchain_opt_target->type = 9;
        $blockchain_opt_target->save();
        $client = self::getStaticClient($currency);
        $client->_get_balance(2);
        $client->_get_balance($target_id);
    }

    //TODO 合并以上操作...

    public static function doBlockchainOpt($order_id)
    {

    }
}

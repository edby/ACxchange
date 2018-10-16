<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/10/010
 * Time: 14:28
 */

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\User;
use App\UserCurr;
use App\WithdrawHistory;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestYaoController extends Controller
{






    //--对比所有人地址对比
    public function duibi_all_user_address(Request $request){
        header("Content-Type:text/html; charset=UTF-8");
        $currency_list = ['BTC','RPZ','XVG','BCH'];
        if(empty($user_id)){
            $user_es = User::all();
            $total = count($user_es);
            foreach ($currency_list as $currency){
                $client= self::getStaticClient($currency);
                $dex =0;
                foreach ($user_es as $user){
                    $dex ++;
                    echo ("当前用户".$user->id." 当前货币:".$currency." 当前 位置:".$dex."/".$total."</br>");
                    try{
                        $this->duibi_a_user_address($client,$user->id,$currency);
                    }catch (\Exception $e){
                        dump($e->getMessage());
                    }

                }
            }
        }
    }


    private function duibi_a_user_address($client,$user_id,$currency){
        try{
            $address_es = $client->getAddressList($client->prefix().$user_id);
            //dump($address_es);
        }catch (\Exception $e){
            dump($e->getMessage());
        }


        if(empty($address_es)){
            dump($user_id." 地址为空,错误: 当前币:".$client->prefix());
            return false;
        }else{
            $address = UserCurr::where([['user_id','=',$user_id],['curr_abb','=',$currency]])->value('address');
              if(!in_array($address,$address_es)){
                  dump($user_id." 当前地址不属于他，地址错误:".$address." 当前币种: ".$currency);
                  dump($address_es);
                  //$now = date("Y-m-d H:i:s",time());
                  //UserCurr::where([['user_id','=',$user_id],['curr_abb','=',$currency]])->update(
                  //    ['address'=>$address_es[0],
                  //    'updated_at'=>$now,
                  //    'back_address'=>$address,
                  //    ]);
                  //echo "更新成功</br>";
                  return false;
              }
        }
        return true;
       }

       //-测试地址是否相等

       public function test_duibi_a_user_address(Request $request){
        $client = self::getStaticClient("BCH");
        $currency="BCH";
        $user_id=128;
        try{
            $this->duibi_a_user_address($client,$user_id,$currency);
        }catch (\Exception $e){
            dump($e->getMessage());
        }

        dump("wanc");
       }



    public  function get_user_trans_info(Request $request){
        try{

            $user_id=$request->user_id;
            $currency=$request->currency;
            $client = self::getStaticClient($currency);
            dump($client->prefix().$user_id);
            $address_es = $client->getAddressList($client->prefix().$client->prefix().$user_id);
            dump($address_es);
            $address_es = $client->getAddressList($client->prefix().$user_id);
            dump($address_es);
            $address_es = $client->getAddressList($client->prefix().$client->prefix().$client->prefix().$user_id);
            dump($address_es);
            //dump($client->listAccounts());
        }catch (\Exception $e){
            dump($e->getMessage());
        }

    }

    //--对比用户提现信息
    public function duibi_user_tixian(Request $request)
    {
        $user_id=$request->user_id;
        $this->shifou_wanquan_xieru_tixian($user_id);
    }

    //-判断用户提现信息是不是完全匹配
    private function shifou_wanquan_xieru_tixian($user_id){
        if(is_null($user_id)){
            echo "用户不能为空 </br>";
            return false;
        }else{
            if($user_id == 800822) return true;
            $currency_list = Currency::where([['is_show','=',1]])->get();
            if( $currency_list && count($currency_list)>0){
                foreach ($currency_list as $currency_huobi){
                    $currency=$currency_huobi->currency;
                    $client=self::getStaticClient($currency);
                    $trans_history = $client->getTransactionList($user_id,5000);
                    if ($trans_history) {
                        foreach ($trans_history as $key => $value) {
                            if(!self::panduan_already_send_data($currency,$user_id,$value)){
                                return false;
                            }
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }


    private function panduan_already_send_data($currency,$user_id,$value){
        if (($value['category'] == 'send')) {
            $amount=bcsub(0,$value['amount'],8);
            if($currency == 'VIT'){
                $result=WithdrawHistory::where([
                    ['txid','=', $value['txid']],
                    ['user_id','=',$user_id],
                    ['currency','=',$currency],
                    ['amount','<=',bcadd($amount,0.000001,6)],
                    ['amount','>=',bcsub($amount,0.000001,6)]
                ])->whereIn('status',[1,4])->first();
            }else{
                $result=WithdrawHistory::where([
                    ['txid','=', $value['txid']],
                    ['user_id','=',$user_id],
                    ['currency','=',$currency],
                    ['amount','=',$amount]
                ])->whereIn('status',[1,4])->first();
            }
            if($result && count($result)>0){
                // $result->update(['cunzai'=>'shide']);
            }else{
                dump($currency,$user_id,$value);
                return false;
                //--漏掉了数据
            }
        }
        return true;
    }




}
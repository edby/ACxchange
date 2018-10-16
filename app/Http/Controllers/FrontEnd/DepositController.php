<?php

namespace App\Http\Controllers\FrontEnd;

use App\Models\DepositHistory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class DepositController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth',['except'=>'']);
    }

    private static function get_mark_name($currency)
    {
        switch ($currency){
            case  1:
                return "btc";
            case  2:
                return "bch";
            case  3:
                return "ltc";
            case  4:
                return "rpz";
            case  5:
                return "eth";
            default:
                return "btc";
        }
    }

    /** 用户充值信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDepositHistory(Request $request)
    {
        $user_id = Auth::id();
        if(Auth::id()==="") return response()->json(['status'=>0,'message' => 'id is null','data'=>""]);
        $deposits = DepositHistory::where('user_id',$user_id)
            ->leftJoin('currency','deposit_history.currency_id','=','currency.id')
            ->select('currency.currency','deposit_history.created_at','address','amount','confirmations','deposit_history.status','txid')
            ->orderBy('deposit_history.created_at','desc')
            ->get()->toArray();


        //dump($deposits);
        foreach ($deposits as $key=>$value){
            //--时间
          //  dump($value);
            $data_data['data'][$key]['add_time']=strtotime($value['created_at']);
            //--货币类型
            $data_data['data'][$key]['currency']=$value['currency'];
            //-地址
            $data_data['data'][$key]['address']=$value['address'];
            //-txid
            $data_data['data'][$key]['txid']=$value['txid'];
            //-amunt
            $data_data['data'][$key]['amount']=number_format($value['amount'],8,".","");
            //--状态
            $data_data['data'][$key]['status']=$value['status'];
            if ($value['status'] == 1){
                $data_data['data'][$key]['statusCp']= __('ac.successfully');
            }else{
                $data_data['data'][$key]['statusCp'] = __('ac.OnGoing');
            }
            //-confirmations 确认数
            $data_data['data'][$key]['confirmations']=$value['confirmations'];
            $data_data['data'][$key]['img'] = asset('images/'.strtolower($value['currency']).'.png');
        }

        $data_data['current_page']=1;
        $data_data['last_page']=1;
        $data_data['total']=1;
        $data_data['lastPage']=1;
       // $data_data['currAbb']=$request->curr_abb;
        return response()->json(['status'=>1,'message' => 'successful','data'=>$data_data]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Handlers\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getAllClicent($currency)
    {
        $client = new Client(getenv($currency.'_HOST'),getenv($currency.'_PORT'),getenv($currency.'_USER'),getenv($currency.'_PASS'),$currency);
        return $client;
    }


    protected function getClient($currency)
    {
        $currency = strtoupper($currency);
        $client = null;
        // $currency = Currency::select('currency')->get();
        $client = new Client(getenv($currency.'_HOST'),getenv($currency.'_PORT'),getenv($currency.'_USER'),getenv($currency.'_PASS'),$currency);
        return $client;
    }

    public static function getStaticClient($currency)
    {
        $client = null;
        // $currency = Currency::select('currency')->get();
        $client = new Client(getenv($currency.'_HOST'),getenv($currency.'_PORT'),getenv($currency.'_USER'),getenv($currency.'_PASS'),$currency);
        return $client;
    }

    protected function success($code = 0, $message = '', $data = null)
    {
        $result = array(
            'code' => $code,
            'message' => $message ? $message : 'success',
            'data' => $data ? $data : null
        );
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }


    protected function failure($code, $message)
    {
        $result = array(
            'code' => $code,
            'message' => $message ? $message : 'failure',
            'data' => null,
        );
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }


    //--增加json处理
    protected function ajax_jason($data = [], $request=null,$status = 200, array $headers = [], $options = 0){
           if($request!=null){
               $this->save_url_request($request,$data);
           }
           return response()->json($data, $status, $headers, $options);
    }

    //--保存浏览器请求
    protected function save_url_request($request,$result=null){
        try{
            $url_duan = substr(url()->current(),strpos(url()->current(),"//")+2);
            $url = substr($url_duan,strpos($url_duan,"/"));
            $is_save=DB::table("save_url_request")->where([['url','=',$url]])->value('is_save');
            if($is_save==1){
                $info=[];
                $info['created_at']=date("Y-m-d H:i:s",time());
                $info['url']=url()->current();
                $info['user_id']=Auth::id();
                $info['param']=json_encode($request->all());
                if($result!=null)
                $info['result']=json_encode($result);
                DB::table("withdraw_request")->insert($info);
            }
        }catch (\Exception $e){
            if(Auth::id()==128){
                dump($e->getMessage());
            }
        }
    }

}

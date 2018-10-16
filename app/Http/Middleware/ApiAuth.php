<?php

namespace App\Http\Middleware;
use Exception;
use App\Models\Apikey;
use Closure;
use Illuminate\Http\Request;

class ApiAuth
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $res = $request->all();
       // dump($res);
       //if(1==1) return;
        if (empty($res['key']) || empty($res['signature']) || empty($res['nonce']) || (strlen($res['key']) != 50)) {
            print_r(json_encode(['code'=>10403,'message'=>'Auth faild 1']));
            exit;
        }
        /*
        $time = time();
        if ($time - $res['nonce'] > 300) {
            print_r(json_encode(['code'=>10402,'message'=>'Auth expire']));
            exit;
        }
        */
        $url ='https://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        $post_key       = $res['key'];
        $secret = Apikey::where('key',$post_key)->value('secret');
        if (is_null($secret)) {
            print_r(json_encode(['code'=>10403,'message'=>'Auth faild 2']));
            exit;
        }
        if($res['signature'] == base64_encode(hash_hmac('sha512', $url, $secret, true))) {
            return $next($request);
        }else {
            print_r(json_encode(['code'=>10403,'message'=>'Auth faild 3']));
            exit;
        }
    }
}

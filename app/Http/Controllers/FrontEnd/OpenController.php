<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/4/24
 * Time: 20:25
 */

namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OpenController extends Controller
{

    //--请求图片
    public function open(Request $request){

        $url=explode("url=",$request->getRequestUri());
        //dump($url);
        if(count($url)>1){
            $url=$url[1];
            header("Content-type:image/jpeg");
            $content = file_get_contents($url);
            header("content-length:".strlen($content));
            header("connection:keep-alive");
            echo $content;
            return;
        }
       // $url=$request->get("url");
       // $url="https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3097348540,2117933418&fm=27&gp=0.jpg";
        echo  "uri is null";

    }

}
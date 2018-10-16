<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/29
 * Time: 9:46
 */

namespace App\Tool;


class TmpCurl
{
    public function postAPI($api,$params=[],$headers = []){
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $api );
        // 以返回的形式接收信息
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        // 设置为POST方式
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params ) );
        // 不验证https证书
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // 发送数据
        $response = curl_exec( $ch );
        curl_close( $ch );
        return $response;
    }
}
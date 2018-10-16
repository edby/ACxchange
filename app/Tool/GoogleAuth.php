<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/24
 * Time: 16:28
 */

namespace App\Tool;


class GoogleAuth
{
    private $GoogleAuth;
    
    public function __construct()
    {
        $this->GoogleAuth = new \PHPGangsta_GoogleAuthenticator();
    }

    /**
     * 生成加密串与验证code 二维码链接
     * @param string $applicationName
     * @param int $secretLen
     * @return array
     * @throws \Exception
     */
    public function secretUrl($applicationName = 'AllanceCapitals',$secretLen = 16)
    {
        $secret = $this->GoogleAuth->createSecret($secretLen);
        $qrCodeUrl = $this->GoogleAuth->getQRCodeGoogleUrl($applicationName, $secret);
        $code = $this->GoogleAuth->getCode($secret);
        return ['secret' =>$secret,'qrCodeUrl'=>$qrCodeUrl,'code'=>$code];
    }

    /**
     * 根据加密串生成验证Code
     * @param $secret
     * @return string
     */
    public function getCode($secret)
    {
        return $this->GoogleAuth->getCode($secret);
    }

    /**
     * @param $secret
     * @param $code
     * @param int $time
     * @return boolean
     */
    public function verifyCode($secret,$code,$time = 2)
    {
        return $this->GoogleAuth->verifyCode($secret, $code, $time);
    }

}
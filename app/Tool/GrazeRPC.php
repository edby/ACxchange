<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/31
 * Time: 9:50
 */

namespace App\Tool;


use Graze\GuzzleHttp\JsonRpc\Client;

class GrazeRPC
{
    public static $url = [
        'btc' => "http://bitcoinrpc:B!tco!n@110.173.59.106:8332",
//        'zec' => "http://zcashuser:zcash000@110.173.59.106:8888",
        'bch' => "http://bitcoincashrpc:B!tco!ncash@137.59.22.122:8334",
        'ltc' => "http://litecoinrpc:l!teco!n@103.71.252.10:8331",
        'rpz' => "http://rrpcuser00:Nathan000@148.66.60.170:21435",
        //'xvg'=>"http://V3RgeU53R:V3RgeCo!n@148.66.60.170:20102",
        'xvg'=>"http://V3RgeU53R:V3RgeCo!n@148.66.60.170:20102",
    ];

    //确认数
    public static $confirmNum = [
        'btc' =>1,
        'zec' =>1,
        'bch' =>1,
        'ltc' =>1,
        'rpz' =>1,
        'xvg' =>1
    ];

    private $confirm;

    private static $instance;

    public $request = [];

    public $notification = [];

    public $send;

    public $sendAll;

    private $client;

    private $id = 1;

    public function __construct($type, array $config = [])
    {
        $this->client = Client::factory(self::$url[$type], $config);
        $this->confirm = self::$confirmNum[$type];
    }

    public static function getInstance($type,array $config = [])
    {
        //if (!self::$instance)
        return self::$instance = new  self($type, $config);
    }

    public function request($method,array $params = null)
    {
        $this->request = $this->client->request($this->id,$method, $params);
        return $this;
    }

    public function notification($method, array $params = null)
    {
        $this->notification = $this->client->notification($method, $params);
        return $this;
    }

    public function send($type)
    {
        $this->send = $this->client->send($this->$type[0]);
        return $this;
    }

    public function sendAll()
    {
        $array = array_merge($this->request,$this->notification);
        $this->sendAll = $this->client->sendAll($array);
        return $this;
    }

    public function get($type)
    {
        return $this->$type->getRpcResult();
    }

    /**
     * 发送请求
     * @param $method
     * @param array|null $params
     * @return \Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface|mixed|null
     */
    public function sendRequest($method,array $params = null)
    {
        $this->request = $this->client->request($this->id,$method, $params);
        return $this->client->send($this->request);
    }

    /**
     * 获取确认数
     * @return mixed
     */
    public function getConfirm()
    {
        return $this->confirm;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/5/8
 * Time: 11:00
 */

namespace App\Http\Controllers\FrontEnd;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{

    public function lLen()
    {
        $res = Redis::command('llen', ['test']);
        dump($res);
    }

        public function lPush(Request $request)
    {
        $res = Redis::command('lpush', ['test',$request->test]);
        dump($res);
    }

    public function lPop()
    {
        $res = Redis::command('brpop', ['test',1]);
        dump($res);
    }

    public function flushAll()
    {
        $res = Redis::command('flushall');
        dump($res);
    }

    public function lTrim()
    {
        $res = Redis::command('ltrim',['test',1,0]);
        dump($res);
    }
}
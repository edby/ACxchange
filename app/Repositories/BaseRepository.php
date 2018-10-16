<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/2/5
 * Time: 11:47
 */

namespace App\Repositories;


use App\UserCurr;
use Illuminate\Support\Facades\Auth;

class BaseRepository
{
    /**
     * 市场汇率
     * @return string
     */
    public function market()
    {
        return 'market';
    }

    /**
     * 交易买卖
     * @return string
     */
    public function trade()
    {
        return 'trade';
    }

    /**
     * 用户账户情况
     * @return string
     */
    public function Balance()
    {
        return 'balance';
    }

    /**
     * 获取用户已开通币的名称
     * @return \Illuminate\Http\JsonResponse
     */
    public function currList()
    {
        return  UserCurr::where('user_id',Auth::id())->get(['curr_abb']);
    }
}
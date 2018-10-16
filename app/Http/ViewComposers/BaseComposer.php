<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/2/5
 * Time: 11:32
 */

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\BaseRepository;


class BaseComposer
{
    /**
     * 实现基础仓库
     *
     * @var BaseRepository
     */
    protected $baseData;

    /**
     * 创建一个新的配置文件合成器
     *
     * @param  BaseRepository  $baseData
     * @return void
     */
    public function __construct(BaseRepository $baseData)
    {
        // 依赖关系由服务容器自动解析...
        $this->baseData = $baseData;
    }

    /**
     * 将数据绑定到视图。
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $data = [
            'market'=> $this->baseData->market(),
            'trade'=> $this->baseData->trade(),
            'Balance'=> $this->baseData->Balance(),
            'currList'=> $this->baseData->currList(),
        ];
        $view->with($data);
    }

}
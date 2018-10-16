<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/2/5
 * Time: 11:20
 */

namespace App\Providers;


use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * 在容器中注册绑定
     *
     * @return void
     */
    public function boot()
    {
        // 使用基于类的合成器...
        $baseDataView = ['front.order','front.help','front.trade','front.wallet'];
        View::composer(
            $baseDataView, 'App\Http\ViewComposers\BaseComposer'
        );
    }
}
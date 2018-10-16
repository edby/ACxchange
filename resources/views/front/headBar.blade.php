<!--PC导航-->
<div id="head">
    <!--登录后的首页头部-->
    <div class="head-row login">
        <div>
            <a href="{{url('/')}}"><img src="{{ asset('images/logo.png') }}"></a>
        </div>
        {{--@if(route('wallet') === url()->current())
            <div style="color: red">@lang('ac.SystemMaintenance')</div>
        @endif--}}
        <div class="login-right">
            <label>@lang('ac.Welcome'), <a href="{{url('user/index')}}">{{Auth::user()->name }}</a></label>
            <span>
            <label>@lang('ac.languageChoice')</label><i class="iconfont icon-xiala"></i>
            <div class="lang">
                <ul>
                   <li onclick="setLocale('cn')"><a>简体中文</a><img src="{{ asset('images/cn.png') }}"></li>
                   <li onclick="setLocale('tw')"><a>繁体中文</a><img src="{{ asset('images/hk.png') }}"></li>
                   <li onclick="setLocale('en')"><a>English</a><img src="{{ asset('images/flag_en.jpg') }}"></li>
                   <em></em>
              </ul>
            </div>
        </span>
            <strong class="trade0"><i class="iconfont icon-caidan"></i></strong>

        </div>
    </div>
    <!--登录后结束-->
</div>
<!--移动端头部-->
<div id="hearter-phone">
    <div class="leftbtn">
        <a><img width="22" height="17" src="{{ asset('images/left-btn.png') }}"></a>
    </div>
    <div class="rightlogo">
        <a href="{{url('/')}}"><img src="{{ asset('images/logo.png') }}"></a>
    </div>
    <!--登录后显示菜单-->
    <div class="login-phon">
        <div class="listphon">
            <ul>
                <li class="userphon">
                    <a href="{{url('user/index')}}"><label>@lang('ac.Welcome'), <span>{{Auth::user()->name }}</span></label></a>
                </li>
                <li>
                    <a href="{{ url('/trade/index') }}">@lang('ac.Trading')</a>
                </li>
                <li>
                    <a href="{{ url('order/index') }}">@lang('ac.Orders')</a>
                </li>
                <li>
                    <a href="{{ url('wallet/index') }}">@lang('ac.Wallet')</a>
                </li>
                <li>
                    <a href="{{ url('user/index') }}">@lang('ac.Account')</a>
                </li>
                <li>
                    <a href="{{ url('help/index') }}">@lang('ac.Help')</a>
                </li>
                <li>
                    <a href="{{ url('index/logOut') }}">@lang('ac.Logout')</a>
                </li>
            </ul>
            <div class="phon-bottom">
                <div class="hengxian" onclick="setLocale('cn')">
                    <a><img src="{{ asset('images/cn.png') }}"></a>
                </div>
                <div class="hengxian" onclick="setLocale('en')">
                    <a><img src="{{ asset('images/flag_en.jpg') }}"></a>
                </div>
                <div onclick="setLocale('tw')">
                    <a><img src="{{ asset('images/hk.png') }}"></a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    //語言設置
    function setLocale(lang) {
        console.log(lang);
        $.ajax({
            url:'/login/setLocale'
            ,type:"post"
            ,data:{lang:lang}
            ,success:function (data) {
                console.log(data);
                window.location.reload();
                return false;
            }
            ,error:function (msg){
                console.log(msg);
            }
        });
    }
</script>
<!--移动端头部结束-->
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ACxchange') }}</title>
    <link rel="shortcut icon" type="text/css" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/ac/jquery.fullPage.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/reset.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/iconfont/iconfont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/index.css') }}">
    <script src="{{ asset('js/ac/jquery.js') }}"></script>
    <script src="{{ asset('js/ac/echarts.min.js') }}"></script>
    <script src="{{ asset('js/ac/wow.min.js') }}"></script>
    <script src="{{ asset('js/ac/jquery.fullPage.js') }}"></script>
    <script src="{{ asset('js/ac/index.js') }}"></script>
    <script src="{{ asset('layer/layer.js') }}"></script>
    <script>
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    </script>
    <style>
        /*img-view*/
        .img-view {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        /*遮罩层样式*/
        .img-view .img-layer{
            position: fixed;
            z-index: 999;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.7);
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        /*不限制图片大小，实现居中*/
        .img-view .img img{
            max-width: 100%;
            max-height: 80%;
            display: block;
            position: fixed;
            z-index: 9999;
            top:0;
            bottom:0;
            left: 0;
            right: 0;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="img-view" onclick="bigimg(this)" style="">
    <div class="img-layer"></div>
    <div class="img">
        @if(!session('setLocale')) {{session(['setLocale'=>'en'])}} @endif
        <img src="{{asset('images/notice_0917_1_'.session('setLocale').'.jpg')}}" alt="">
    </div>
</div>
<!--PC导航-->
<div id="head">
    @guest
    <!--未登录的首页头部-->
    <div class="head-row unlisted">
        <div>
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}">
            </a>
        </div>
        <div>
            <a href="{{ route('loginCurrent',['current'=>'current']) }}" class="btns">@lang('ac.signUp')</a>
            <a href="{{ url('login') }}" class="btns">@lang('ac.signIn')</a>
            <span>
                @if(\Illuminate\Support\Facades\App::getLocale() === 'en')
                    <label>En</label><i class="iconfont icon-xiala"></i>
                @elseif(\Illuminate\Support\Facades\App::getLocale() === 'cn')
                    <label>简体中文</label><i class="iconfont icon-xiala"></i>
                @else
                    <label>繁体中文</label><i class="iconfont icon-xiala"></i>
                @endif
                <div class="lang">
                    <ul>
                       <li onclick="setLocale('cn')"><a>简体中文</a><img src="{{ asset('images/cn.png') }}"></li>
                       <li onclick="setLocale('tw')"><a>繁体中文</a><img src="{{ asset('images/hk.png') }}"></li>
                       <li onclick="setLocale('en')"><a>English</a><img src="{{ asset('images/flag_en.jpg') }}"></li>
                       <em></em>
                  </ul>
                </div>
            </span>
        </div>
    </div>
    <!--未登录结束-->
    @else
    <!--登录后的首页头部-->
    <div class="head-row login">
        <div><a href="{{ url('/') }}"><img src="{{ asset('images/logo.png') }}"></a></div>
        <div class="login-right">
            <label>@lang('ac.Welcome'), <a href="{{url('/user/index')}}">{{ Auth::user()->name }}</a></label>
            <span>
               @if(\Illuminate\Support\Facades\App::getLocale() === 'en')
                    <label>En</label><i class="iconfont icon-xiala"></i>
                @elseif(\Illuminate\Support\Facades\App::getLocale() === 'cn')
                    <label>简体中文</label><i class="iconfont icon-xiala"></i>
                @else
                    <label>繁体中文</label><i class="iconfont icon-xiala"></i>
                @endif
                <div class="lang">
                    <ul>
                       <li onclick="setLocale('cn')"><a>简体中文</a><img src="{{ asset('images/cn.png') }}"></li>
                       <li onclick="setLocale('tw')"><a>繁体中文</a><img src="{{ asset('images/hk.png') }}"></li>
                       <li onclick="setLocale('en')"><a>English</a><img src="{{ asset('images/flag_en.jpg') }}"></li>
                       <em></em>
                  </ul>
                </div>
            </span>
            <strong class="index0"><i class="iconfont icon-caidan"></i></strong>
        </div>
    </div>
    @endguest
    <!--登录后结束-->
</div>
<!--移动端头部-->
<div id="hearter-phone">
    <div class="leftbtn">
        <a><img width="22" height="17" src="{{ asset('images/left-btn.png') }}"></a>
    </div>
    <div class="rightlogo">
        <a href="{{ url('/') }}"><img src="{{ asset('images/logo.png') }}"></a>
    </div>
    <!--未登录-->
    @guest
    <div class="unlisted-phon">
        <div class="listphon">
            <ul class="phon-list">
                <li><a href="{{ route('loginCurrent',['current'=>'current'])}}" class="btns">@lang('ac.signUp')</a></li>
                <li><a href="{{ url('login') }}" class="btns">@lang('ac.signIn')</a></li>
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
    @else
    <!--登录后显示菜单-->
    <div class="login-phon">
        <div class="listphon">
            <ul>
                <li class="userphon">
                    <a href="{{url('/user/index')}}"><label>@lang('ac.Welcome'), <span>{{ Auth::user()->name }}</span></label></a>
                </li>
                <li><a href="{{ url('/trade/index') }}">@lang('ac.Trading')</a></li>
                <li><a href="{{ url('order/index') }}">@lang('ac.Orders')</a></li>
                <li><a href="{{ url('wallet/index') }}">@lang('ac.Wallet')</a></li>
                <li><a href="{{ url('user/index') }}">@lang('ac.Account')</a></li>
                <li><a href="{{ url('help/index') }}">@lang('ac.Help')</a></li>
                <li><a href="javascript:logOut()" class="logout">@lang('ac.Logout')</a></li>
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
    @endguest
</div>

<!--移动端头部结束-->
<div id="fullPage">
    <div class="section">
        <div class="slide bg bg1">
            <p class="wow zoomIn">@lang('ac.grabOpportunity')<br>@lang('ac.CryptoWave')</p>
            @guest
                <div class="getNow wow bounceInUp" class="" data-wow-delay="400ms">
                    <a href="{{ route('loginCurrent',['current'=>'current']) }}" >@lang('ac.SignUpNow')</a>
                </div>
            @else
                <div class="getNow wow bounceInUp" class="" data-wow-delay="400ms">
                    <a href="{{url('/trade/index')}}">@lang('ac.GetStarted')</a>
                </div>
            @endguest
            <div class="footer">
                <ul class="footerItem">
                    @empty(!$market)
                        @foreach($market as $eky => $value)
                    <li>
                        <table class="lineDate">
                            <thead>
                            <tr>
                                <th colspan="3">{{$value['curr']}}({{$value['curr_abb']}})</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{$value['last_price']}}</td>
                                <td>{{$value['curr_abb']}}</td>
                                <td class="{{$value['flag']}}"><i id="{{$value['curr_abb']}}">{{$value['percent_change_24h']}}</i><span><img class="{{$value['curr_abb']}}" src="{{asset('images').'/'.$value['flag'].'.png' }}"></span></td>
                            </tr>
                            {{--<tr>--}}
                                {{--<td>0.1001</td>--}}
                                {{--<td>ETH</td>--}}
                                {{--<td class="down"><i>7.102%</i><span><img src="{{ asset('images/down.png') }}"></span></td>--}}
                            {{--</tr>--}}
                            </tbody>
                        </table>
                    </li>
                        @endforeach
                    @endempty
                </ul>
            </div>
        </div>
         {{--<div class="slide bg bg2">--}}
             {{--<div class="echar" style="width: 100%; height: 100%; position: relative;">--}}
                 {{--<!--<video src="images/echerts.mp4" height="100%" width="100%" autoplay="autoplay"></video>-->--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="slide bg bg3">--}}
            {{--<div class="bgScale"></div>--}}
            {{--<div class="container">--}}
                {{--<span class="wow zoomIn centerText">How to get started</span>--}}
                {{--<ul class="wrapper-therd">--}}
                    {{--<li class="wow bounceInLeft" data-wow-delay="600ms">--}}
                        {{--<div class="topImg">--}}
                            {{--<img src="{{ asset('images/therd-ico1.png') }}">--}}
                        {{--</div>--}}
                        {{--<h5>Sign up</h5>--}}
                        {{--<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. </p>--}}
                        {{--<a href="#">--}}
                            {{--<img src="{{ asset('images/therd-right.png') }}">--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    {{--<li style="margin-top: 218px" class="wow bounceInUp" data-wow-delay="900ms">--}}
                        {{--<div class="topImg">--}}
                            {{--<img src="{{ asset('images/therd-ico2.png') }}">--}}
                        {{--</div>--}}
                        {{--<h5>Authentication</h5>--}}
                        {{--<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. </p>--}}
                        {{--<a href="#">--}}
                            {{--<img src="{{ asset('images/therd-right.png') }}">--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    {{--<li class="wow bounceInRight" data-wow-delay="600ms">--}}
                        {{--<div class="topImg">--}}
                            {{--<img src="{{ asset('images/therd-ico3.png') }}">--}}
                        {{--</div>--}}
                        {{--<h5>Sign up</h5>--}}
                        {{--<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. </p>--}}
                        {{--<a href="#">--}}
                            {{--<img src="{{ asset('images/therd-right.png') }}">--}}
                        {{--</a>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="slide bg bg4">--}}
            {{--<div class="bg4-row">--}}
                {{--<div class="bg4-left">--}}
                    {{--<div class="cryp  wow bounce" data-wow-delay=".4s">--}}
                        {{--alliancecaptitals platform--}}
                        {{--features--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="bg4-right">--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<!--定位-->--}}
            {{--<div class="positioning">--}}
                {{--<div class="box">--}}
                    {{--<div class="box-row box1">--}}
                        {{--<div class="small-row">--}}
                            {{--<div class="wen">--}}
                                {{--<span class="title0">Rapid trading</span>--}}
                                {{--<p>--}}
                                    {{--Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo.--}}
                                {{--</p>--}}
                            {{--</div>--}}
                            {{--<div class="rap-tu">--}}
                                 {{--<span class="img">--}}
                                 {{--<img src="{{ asset('images/icon_fast.png') }}">--}}
                                     {{--<div class="xian">--}}
                                     {{--</div>--}}
                                {{--</span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="small-row rightSmall">--}}
                            {{--<div class="tru-tu">--}}
                                 {{--<span class="img">--}}
                                     {{--<img src="{{ asset('images/icon_trust.png') }}">--}}
                                     {{--<div class="xian2"></div>--}}
                                 {{--</span>--}}
                            {{--</div>--}}
                            {{--<div class="wen">--}}
                                {{--<span class="title0">Trusted platform</span>--}}
                                {{--<p>--}}
                                    {{--Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo.--}}
                                {{--</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="box-row box2">--}}
                        {{--<div class="small-row">--}}
                            {{--<div class="wen">--}}
                                {{--<span class="title0">Security</span>--}}
                                {{--<p>--}}
                                    {{--Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo.--}}
                                {{--</p>--}}
                            {{--</div>--}}
                            {{--<div class="sec-tu">--}}
                                 {{--<span class="img">--}}
                                     {{--<img src="{{ asset('images/icon_security.png') }}">--}}
                                     {{--<div class="xian3"></div>--}}
                                 {{--</span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="small-row rightSmall">--}}
                            {{--<div class="pri-tu">--}}
                                 {{--<span class="img">--}}
                                     {{--<img src="{{ asset('images/icon_privacy.png') }}">--}}
                                     {{--<div class="xian4"></div>--}}
                                 {{--</span>--}}
                            {{--</div>--}}
                            {{--<div class="wen">--}}
                                {{--<span class="title0">Privacy insurance</span>--}}
                                {{--<p>--}}
                                    {{--Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo.--}}
                                {{--</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="box-row box3">--}}
                        {{--<div class="small-row">--}}
                            {{--<div class="wen">--}}
                                {{--<span class="title0">Easy to use</span>--}}
                                {{--<p>--}}
                                    {{--Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo.--}}
                                {{--</p>--}}
                            {{--</div>--}}
                            {{--<div class="easy-tu">--}}
                                 {{--<span class="img">--}}
                                     {{--<img src="{{ asset('images/icon_easy.png') }}">--}}
                                     {{--<div class="xian5"></div>--}}
                                 {{--</span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="small-row rightSmall">--}}
                            {{--<div class="tran-tu">--}}
                                 {{--<span class="img">--}}
                                     {{--<img src="{{ asset('images/icon_transparent.png') }}">--}}
                                     {{--<div class="xian6"></div>--}}
                                 {{--</span>--}}
                            {{--</div>--}}
                            {{--<div class="wen">--}}
                                {{--<span class="title0">Transparent fee</span>--}}
                                {{--<p>--}}
                                    {{--Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo.--}}
                                {{--</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="middlebtn">--}}
                      {{--<span>--}}
                          {{--<img class="wow flipInX" data-wow-delay=".4s"  src="{{ asset('images/logo_feature.png') }}">--}}
                      {{--</span>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<!--定位结束-->--}}
        {{--</div>--}}
        {{--<div class="slide bg bg5">--}}
            {{--<div class="middleText">--}}
                {{--<h4 class="title wow bounceInLeft">About AllianceCapitals</h4>--}}
                {{--<p class="wow bounceInLeft" data-wow-delay="200ms">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p>--}}
                {{--<p class="wow bounceInLeft" data-wow-delay="300ms">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p>--}}
                {{--@guest--}}
                {{--<div class="startTrad wow bounceInLeft" data-wow-delay="400ms">--}}
                    {{--<a href="{{url('/login')}}" class="start">Start Login</a>--}}
                {{--</div>--}}
                {{--@else--}}
                    {{--<div class="startTrad wow bounceInLeft" data-wow-delay="400ms">--}}
                        {{--<a href="{{url('/trade/index')}}" class="start">Start Trading</a>--}}
                    {{--</div>--}}
                    {{--@endguest--}}
            {{--</div>--}}
            {{--<div class="footer">--}}
                {{--<ul>--}}
                    {{--<li><a href="javascript:0">API Documentation</a></li>--}}
                    {{--<li class="fline"><a href="javascript:0">Terms and Conditions</a></li>--}}
                    {{--<li><a href="javascript:0">F.A.Q</a></li>--}}
                    {{--<li class="fline"><a href="javascript:0">Privacy Policy</a></li>--}}
                    {{--<li><a href="javascript:0">Contact AllianceCapitals</a></li>--}}
                {{--</ul>--}}
                {{--<div class="flogo">--}}
                    {{--<a href="{{ url('/') }}">--}}
                        {{--<img src="{{ asset('images/logo.png') }}" alt="">--}}
                    {{--</a>--}}
                {{--</div>--}}
                {{--<div class="cropy">--}}
                    {{--<span>© Copyright 2018 AllianceCapitals</span>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

    </div>
</div>
<!--菜单弹框-->
<div id="menu">
    <div class="colu-menu">
        <div class="btnn"><strong><i class="iconfont icon-caidan"></i></strong></div>
        <ul>
            <li><a href="{{ url('/trade/index') }}">@lang('ac.Trading')</a></li>
            <li><a href="{{ url('order/index') }}">@lang('ac.Orders')</a></li>
            <li><a href="{{ url('wallet/index') }}">@lang('ac.Wallet')</a></li>
            <li><a href="{{ url('user/index') }}">@lang('ac.Account')</a></li>
            <li><a href="{{ url('help/index') }}">@lang('ac.Help')</a></li>
        </ul>
        <div class="logout"><a href="javascript:logOut()"><span>@lang('ac.Logout')</span></a></div>
    </div>
</div>
<input type="hidden" name="env" value="{{$env}}">
<!--菜单弹框结束-->
</body>
</html>
<script>
    function logOut(){
        layer.open({
            title:'@lang('ac.prompt')'
            ,content:'@lang('ac.AreLogOut')'
            ,btn: ['@lang('ac.Confirm')', '@lang('ac.Cancel')']
            ,yes: function(index, layero){
                $.ajax({
                    url:'/index/logOut'
                    ,type:"GET"
                    ,success:function (msg) {
                        window.location.href = '/login';
                    }
                    ,error:function (error) {
                        console.log('error');
                        window.location.href = '/login';
                    }
                });
                layer.close(index);
            }
            ,btn2: function(index, layero){
                layer.close();
            }
        });
    }
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
            }
            ,error:function (msg){
                console.log(msg);
            }
        });
    }

    $(function(){
        var env = $("input[name='env']").val();
        //打开页面就初始化动画
        wow = new WOW({  animateClass: 'animated'});
        wow.init();
        var flag = true;

        $("#fullPage").fullpage({
            verticalCentered:false,
            slidesNavigation:false,     //是否显示导航===============1==============
            slidesNavPosition: 'bottom',   //导航位置
            loopHorizontal:false,  //禁止循环
           // keyboardScrolling: false,//是否禁止键盘事件==============2================
//            allowScrolling:false,//删除鼠标事件===========3=============
            afterRender: function() {
//                $.fn.fullpage.stop();//阻止事件

                $('.fp-slidesNav ul li').click(function () {
                    //切换实现动画效果
                    wow = new WOW({  animateClass: 'animated'});
                    wow.init();
                    AnimatHide();//默认移除动画
                    removeEchart();//移除echart图
                    //第二屏
                    if($(this).index()==1){
                        return addEchart(env);//添加echart图
                    }
                    //第四屏
                    if($(this).index()==3){
                        return Animat(1000);//执行动画散开
                    }
                })
            },
//            afterSlideLoad: function(anchorLink,index,slideIndex,direction){
//                flag = true;
//                if(slideIndex == 0){
//                    flag = false
//                    if(!flag){
//                        return
//                    }
//                    $.fn.fullpage.setAllowScrolling(false);
//                    console.log(22222)
//                    return
//                }
//                if(slideIndex == 4){
//                    flag = false
//                    if(!flag){
//                        return
//                    }
//                }
//            }
        });
        /*
        var scrollFunc = function (e) {
            e = e || window.event;
            if (e.wheelDelta) {  //判断浏览器IE，谷歌滑轮事件
                if (e.wheelDelta > 0) { //当滑轮向上滚动时
                    console.log("滑轮向上滚动");
                    $.fn.fullpage.moveSlideLeft();
                    wow = new WOW({  animateClass: 'animated'});
                    if(flag){

                        wow.init();
                    }
                    AnimatHide();//默认移除动画
                    Animat(1000);//执行动画散开
                    removeEchart();
                    addEchart(env);//添加echart图
                }
                if (e.wheelDelta < 0) { //当滑轮向下滚动时
                    console.log("滑轮向下滚动");
                    $.fn.fullpage.moveSlideRight();
                    wow = new WOW({  animateClass: 'animated'});
                    if(flag){

                        wow.init();
                    }
                    AnimatHide();//默认移除动画
                    Animat(1000);//执行动画散开
                    removeEchart();
                    addEchart(env);//添加echart图
                    // $.fn.fullpage.moveSlideRight();
                }
            } else if (e.detail) {  //Firefox滑轮事件
                if (e.detail> 0) { //当滑轮向上滚动时
                    // console.log("滑轮向上滚动");
                    $.fn.fullpage.moveSlideLeft();
                    wow = new WOW({  animateClass: 'animated'});
//                    wow.init();
                    AnimatHide();//默认移除动画
                    Animat(1000);//执行动画散开
                    removeEchart();
                    addEchart(env);//添加echart图
                    if(flag){

                        wow.init();
                    }
                }
                if (e.detail< 0) { //当滑轮向下滚动时
                    // console.log("滑轮向下滚动");
                    $.fn.fullpage.moveSlideRight();
                    wow = new WOW({  animateClass: 'animated'});
//                    wow.init();
                    AnimatHide();//默认移除动画
                    Animat(1000);//执行动画散开
                    removeEchart();
                    addEchart(env);//添加echart图
                    // $.fn.fullpage.moveSlideRight();
                    if(flag){

                        wow.init();
                    }
                }
            }
        }
        document.onkeydown=function(event){
            var e = event || window.event || arguments.callee.caller.arguments[0];
            if(e && e.keyCode==37){
                wow = new WOW({  animateClass: 'animated'});
//                wow.init();

                AnimatHide();//默认移除动画
                Animat(1000);//执行动画散开
                removeEchart();
                addEchart(env);//添加echart图
                if(flag){

                    wow.init();
                }
            }
            if(e && e.keyCode==39){
                wow = new WOW({  animateClass: 'animated'});
//                wow.init();
                AnimatHide();//默认移除动画
                Animat(1000);//执行动画散开
                removeEchart();
                addEchart(env);//添加echart图
                if(flag){

                    wow.init();
                }
            }
        };
        window.onmousewheel = document.onmousewheel = scrollFunc;
         */
    });
    function bigimg(selef) {
        $(selef).css('display', 'none')
    }
</script>
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="Generator" content="EditPlus">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Cryptocoiners') }}</title>
    <link rel="shortcut icon" type="text/css" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/reset.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/iconfont/iconfont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/index.css') }}">
    <script>
        var imgUrl = '{{asset('images')}}';
    </script>
    <script src="{{ asset('js/ac/jquery.js') }}"></script>
    <script src="{{ asset('js/ac/wow.min.js') }}"></script>
    <script src="{{ asset('js/ac/index.js') }}"></script>
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
    <style>
        .bug #head .head-row.login{ padding:0 70px; }
        .bug .bug504{color: #6f6f6f; width: 100%; display: flex; justify-content: center;}
        .bug .bug504 .box5{ text-align: center; width: 408px; background: url(".././../images/504.png") no-repeat center top;}
        .bug .bug504 .box5 h3{ text-align: left; padding-top: 180px; font-size: 46px; }
        .bug .bug504 .box5 p{ margin-top: 250px; font-size: 42px; font-weight: bolder; }
        .bug .bug504 .box5 a{width: 220px; height: 60px; line-height: 60px; display:inline-block; color: #fff; background:#2862c7; margin-top: 70px;  }

        @media (max-width: 430px) {
            .bug .bug504 .box5{ width: 96%; background-size: 90%;}
            .bug .bug504 .box5 h3{ padding-top: 120px; font-size: 36px;}
            .bug .bug504 .box5 p{font-size: 30px;margin-top: 72%;  }
        }
    </style>
</head>
<body class="tradebody bug">
<!--PC导航-->
<div id="head">
    <!--登录后的首页头部-->
    <div class="head-row login">
        <div><a href="{{url('/')}}"><img src="{{ asset('images/logo.png') }}"></a></div>
        <div class="login-right">
            <label>Welcome， <a>Zack</a></label>
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
                    <a><label>Welcome， <span>Zack</span></label></a>
                </li>
            </ul>
            <div class="phon-bottom">
                <div class="hengxian">
                    <a><img src="{{ asset('images/cn.png') }}"></a>
                </div>
                <div class="hengxian">
                    <a><img src="{{ asset('images/flag_en.jpg') }}"></a>
                </div>
                <div>
                    <a><img src="{{ asset('images/hk.png') }}"></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--移动端头部结束-->
<div class="bug504">
    <div class="box5">
        <div>
            <h3>Sorry,</h3>
            <p>Gateway Time-Out</p>
            <a href="{{url('/')}}">Go Back Home</a>
        </div>
    </div>
</div>
</body>
</html>

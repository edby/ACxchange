<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="Generator" content="EditPlus">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Cryptocoiners') }}</title>

    <link rel="shortcut icon" type="text/css" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/reset.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/iconfont/iconfont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/index.css') }}">
    <script>
        var imgUrl = '{{asset('images')}}';
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    </script>
    <script src="{{ asset('js/ac/jquery.js') }}"></script>
    <script src="{{ asset('js/ac/wow.min.js') }}"></script>
    <script src="{{ asset('js/ac/index.js') }}"></script>
    <script>
        $(function(){
            reJsonData();
            function reJsonData() {
                $.ajax({
                    url:'/index/getJson/data'
                    ,type:'GET'
                    ,success:function (res) {
                        data = res.data;
                        $('.login div.login-right>label').html(data.Welcome);
                        $('.bug .bug404 .box4 p').html(data.ChoiceHave);
                        $('.bug .bug404 .box4 a').html(data.GoBackHome);
                        $('#head .head-row span label, #header .head-row span label').html(data.languageChoice);
                        if (data.userName){
                            $('.login div.login-right>label a').html(data.userName);
                        }
                    }
                    ,error:function (msg) {
                        console.log(msg);
                    }
                });
            }
        });
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
        .bug .bug404{color: #6f6f6f; width: 100%;padding-top:128px;  display: flex;justify-content: center;}
        .bug .bug404 .box4{ width: 380px;  text-align: center;}
        .bug .bug404 .box4 h3{ height: 114px; line-height: 114px; font-size:54px; white-space: nowrap; }
        .bug .bug404 .box4 .png{ padding: 28px 0;border-bottom: 1px solid #232a46; border-top: 1px solid #232a46; }
        .bug .bug404 .box4 p{ line-height: 80px; font-size: 22px;  }
        .bug .bug404 .box4 a{ width: 220px; height: 62px; line-height: 62px; display:inline-block; color: #fff; background:#2862c7;  }
        @media ( max-width: 500px) {
            .bug .bug404 .box4{ width:92%;}
            .bug .bug404{ padding-top: 60px;}
            .bug .bug404 .box4 h3{font-size: 40px;}
            .bug .bug404 .box4 p{ font-size: 16px;}
            .bug .bug404 .box4 .png img{ width: 100%;}
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
            <label>Welcome， <a></a></label>
            <span>
                <label></label><i class="iconfont icon-xiala"></i>
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
<div class="bug404">
    <div class="box4">
        <div>
            <h3>OH NO! IT'S A</h3>
            <div class="png"><img src="{{ asset('images/404.png') }}"></div>
            <p>The only choice you now have is to</p>
            <a href="{{url('/')}}">Go Back Home</a>
        </div>
    </div>

</div>

</body>
</html>

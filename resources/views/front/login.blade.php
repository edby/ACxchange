<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Cryptocoiners') }}</title>
    <link rel="shortcut icon" type="text/css" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/jquery.fullPage.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/reset.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/iconfont/iconfont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/index.css') }}">
    <script src="{{ asset('js/ac/jquery.js') }}"></script>
    <script src="{{ asset('js/ac/bootstrap.min.js') }}"></script>
    <script src="{{ asset('layer/layer.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/ac/echarts.min.js') }}"></script>
    <script src="{{ asset('js/ac/wow.min.js') }}"></script>
    <script src="{{ asset('js/ac/jquery.fullPage.js') }}"></script>
    <script src="{{ asset('js/ac/index.js') }}"></script>
    <script src="{{ asset('js/ac/json.js') }}"></script>
    <style>
        /*浏览器默认input*/
        /* .main-login .login-right .form-group input{
            box-shadow: 40px 40px 40px 40px # inset;
            color: #dde1e8!important;
        } */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #1d1d25 inset !important;//关于解决输入框背景颜色
        -webkit-text-fill-color: #fff !important;//关于接输入框文字颜色
        }
        input{
            text-fill-color:#fff;
            -webkit-text-fill-color:#fff;
        }
        .ver_unput{
            text-fill-color:#000;
            -webkit-text-fill-color:#000;
        }
        /*.layui-layer {*/
        /*background:#2c35571f !important;*/
        /*}*/
        .layui-layer-loading0 {
            background: url(layer/theme/default/loading-0.gif) no-repeat!important;
        }
    </style>
</head>
<body>
<div id="header" style="">
    <div class="head-row unlisted">
        <div>
            <a href="{{url('/')}}">
                <img src="{{ asset('images/logo.png') }}">
            </a>
        </div>
        <div>
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
</div>
<div class="login">
    <div class="main-login">
        <ul>
            <li class="login-left">
                <div class="login-top @if(empty($current)) current @endif  ">
                    <h3 class="title">@lang('ac.signIn')</h3>
                    <p>@lang('ac.alreadyYourClient')</p>
                    <p>@lang('ac.startTradingWithUsNow')</p>
                </div>
                <div class="login-bottom @if(!empty($current)) {{$current}} @endif ">
                    <h3 class="title">@lang('ac.signUp')</h3>
                    <p>@lang('ac.notAClientYet')</p>
                    <p>@lang('ac.embark')</p>
                </div>
                <div class="btn-or">
                    or
                </div>
            </li>
            <li class="login-right">
                <div class="signIn" @if(!empty($current)) style="display:none" @else style="display:block"  @endif>
                    <h3 class="title">@lang('ac.signIn')</h3>
                    <form onsubmit="return signIn();" >
                        <div class="form-group account">
                            <input type="text" name="loginEmail" class="Account" placeholder="@lang('ac.enterEmailLogin')" >
                            {{--<span class="userError"></span>--}}
                        </div>
                        <div class="form-group password">
                            <input type="password" name="loginPrd"  class="Password" placeholder="@lang('ac.enterPassword')">
                            {{--<span class="passError"></span>--}}
                        </div>
                        <div class="form-group signBottom">
                            <div class="leftcheck">
                                <input type="checkbox" name="loginCheck">@lang('ac.signedOnComputer')
                            </div>
                            <div class="rightForget">
                                <a href="javascript:void (0)" class="forgotPass">@lang('ac.forgotPassword')</a>
                            </div>
                        </div>
                        <div class="form-group submit">
                            <input type="submit" name="signBtn" class="signBtn btnNotActiv" value="@lang('ac.signIn')">
                        </div>
                    </form>
                </div>
                <div class="signUp"   @if(empty($current)) style="display:none" @else style="display:block"  @endif >
                    <h3 class="title">@lang('ac.signUp')</h3>
                    <form id="signIn" onsubmit="return signUp();">
                        <div class="form-group account">
                            <input type="text" name="name" class="userName" placeholder="@lang('ac.enterName')" onblur="userNameReg()">
                            <span class="userError"></span>
                        </div>
                        <div class="form-group password">
                            <input type="email" name="email" class="email" placeholder="@lang('ac.enterEmail')" onblur="emailReg()">
                            <span class="emailRight"></span>
                        </div>
                        <div class="form-group password">
                            <input type="email" name="emailAgain" class="emailAgain" placeholder="@lang('ac.enterEmailAgain')" onblur="emailAgainReg()">
                            <span class="emailReRight"></span>
                        </div>
                        <div class="form-group password">
                            <input type="password" name="password" class="upPassReg" placeholder="@lang('ac.enterPassword')" onblur="upPassReg()">
                            <span class="upPass"></span>
                        </div>
                        <div class="form-group password">
                            <input type="password" name="passAgain" class="upPassAgain" placeholder="@lang('ac.enterPasswordAgain')" onblur="upPassAgain()">
                            <span class="passReRight"></span>
                        </div>
                        <div class="form-group password">
                            <input type="text" name="captcha" placeholder="@lang('ac.enterVerificationCode')">
                            <span class="verifiCode"><img id ='code' onclick='this.src=this.src+"?"+Math.random()' src="/captcha/inverse"></span>
                        </div>
                        <div class="form-group signBottom">
                            <div class="leftcheck" id="layerDemo">
                                <input type="checkbox" name="check" value="">@lang('ac.IhaveRead')<a href="#" class="treams">@lang('ac.TreamsAndConditions') </a> @lang('ac.and') <a href="javascript:0" class="privacy">@lang('ac.PrivacyPolicy')</a>.
                            </div>
                            <!-- <div class="rightForget">
                                <a href="#">Forgot password?</a>
                            </div> -->
                        </div>
                        <div class="form-group submit" >
                            <input type="submit" class="signUpBtn btnNotActiv" value="@lang('ac.signUp')">
                        </div>
                    </form>
                </div>
                <ul class="forgetPassStep">
                    <li class="steps">
                        <div class="forgotTop">
                            <h3 class="title">@lang('ac.ForgotPassword')</h3>
                            <div>
                                <span class="leftStep">1</span><span>/@lang('ac.stepThree')</span>
                            </div>
                        </div>
                        <div class="stepItem step1">
                            <p>@lang('ac.YourEmailAddress')</p>
                            <div class="form-group password">
                                <input type="email" name="restEmail" placeholder="@lang('ac.enterEmail')">
                                <!-- <span class="emailRight">Great.Please move on.</span> -->
                            </div>
                            <div class="form-group submit" >
                                <input type="button" class="nextTo2" value="Next">
                            </div>
                        </div>
                        <div class="stepItem step2">
                            <p>@lang('ac.codeSentTo')<a href="javascript:void (0);" class="Ebox"></a>@lang('ac.mailbox')</p>
                            <div class="form-group password">
                                <input type="text" name="restCode" placeholder="@lang('ac.enterVerificationCode')">
                                <!-- <span class="emailRight">Great.Please move on.</span> -->
                            </div>
                            <div class="form-group submit" >
                                <input type="button" class="nextTo3" value="Next">
                            </div>
                        </div>
                        <div class="stepItem step3">
                            <div class="form-group password">
                                <input type="password" name="spPass" placeholder="@lang('ac.enterPassword')">
                                <!-- <span class="emailRight">Great.Please move on.</span> -->
                            </div>
                            <div class="form-group password">
                                <input type="password" name="spPassAgain" placeholder="@lang('ac.enterPasswordAgain')">
                                <!-- <span class="emailRight">Great.Please move on.</span> -->
                            </div>
                            <div class="form-group submit" >
                                <input type="button" class="nextToLogin" value="Next">
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>

<div class="modal fade" id="authy_modal" tabindex="-1" role="dialog" style="padding-right: 17px;">
    <div class="modal-dialog" role="document" style="margin-top: 341.5px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">@lang('ac.FA')</h4>
            </div>
            <div class="modal-body">
                <p id="2fa_verify_title">@lang('ac.openGoogle')</p >
                <div class="authy_code">
                    {{--<input type="text" class="" id="newtext" autofocus="autofocus" >--}}
                </div>
            </div>
            <div class="modal-footer">
                <button id="send_authy" type="button" class="btn btn-nov" onclick="two_pwd()">OK</button>
            </div>
        </div>
    </div>
</div>

{{--successfully成功提示--}}
<div class="successbox">
    <div class="icon0">
        <div class="yuan0"><i class="iconfont icon-dui"></i></div>
    </div>
    <div class="success_tips">@lang('ac.LimitSuccessfully')</div>
</div>

{{--error成功提示--}}
<div class="errorbox">
    <div class="icon0">
        <div class="yuan0"><i class="iconfont icon-open-warn"></i></div>
    </div>
    <div class="error_tips">@lang('ac.totalMustBe')</div>
</div>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //登陸時候確認郵箱
    var register = "{{$register}}";
    if (register) {
        setTimeout(function () {
            successfully(register);
        },600);
    }


    //登陆验证
    function signIn() {
        var email = $(" input[ name='loginEmail' ] ").val();
        var password = $(" input[ name='loginPrd' ] ").val();
        var check =  $("input[name='loginCheck' ]").prop('checked');
        var data = {email:email,password:password,check:check};
        var default_2fa_type=1;


        //正则表达验证账号格式
        var accountReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        //如果账号为空
        if(email ==''){
            error('@lang('ac.AccountEmpty')' );
            return false;

        }else if(!accountReg.test(email)){
            error( '@lang('ac.Formatting')' );
            return false;

        }else if(password==''){
            error( '@lang('ac.inputPassword')');
            return false;

        }else if(accountReg.test(email)){

            $.ajax({
                url:'loginAction'
                ,type:'POST'
                ,data:data
                ,success:function (data) {
                    if (data.status === 1){
                        window.location.href="/wallet/index";
                    }else if (data.status == 0){
                        error(data.message);
                    }else if (data.status === 2){
                        $("#2fa_verify_title").html(data.title);
                        var authy = '';
                        $(".authy_code").empty();
                        authy += " <input type='text'  id='newtext' autofocus='autofocus'>";
                        authy += "<input type='hidden' id='type' value='"+data.type+"'>";
                        authy += "<input type='hidden' id='check' value='"+data.check+"'>";
                        $('.authy_code').append(authy);
                        $("#authy_modal").modal('show');
                    }
                }
                ,error:function (error) {
                    console.log(error);
                    var mg = JSON.parse(error.responseText);
                    for (var k in mg.errors){
                        var tip = mg.errors[k][0];
                    }
                    if (k===undefined){
                        layer.msg('@lang('ac.systemError')', {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                        return false;
                    }
                    k = k.toLowerCase();
                    k = k.replace(/\b\w+\b/g, function(word){
                        return word.substring(0,1).toUpperCase()+word.substring(1);
                    });
                    layer.msg(k+' : '+tip, {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                    return false;
                }
            });
            return false;
        }
    }
    //注册验证
    function signUp() {
        $("#code").attr('src','/captcha/inverse?'+Math.random());
        var name = $(" input[ name='name' ] ").val();
        var email = $(" input[ name='email' ] ").val();
        var email_confirmation = $(" input[ name='emailAgain' ] ").val();
        var password = $(" input[ name='password' ] ").val();
        var password_confirmation = $(" input[ name='passAgain' ] ").val();
        var captcha = $(" input[ name='captcha' ] ").val();
        var check =  $("input[name='check' ]").prop('checked');
        var data = {name:name,email:email,email_confirmation:email_confirmation,password:password,password_confirmation:password_confirmation,captcha:captcha,check:check};
        var indexcc = layer.load();
        $.ajax({
            url:'/registerAction'
            ,type:"post"
            ,data:data
            ,success:function (data) {
                layer.close(indexcc)
                console.log(data);
                if (1 == data.status){
                    successfully(data.message);
                    setTimeout(function () {
                        // successfully(data.message);
                        window.location.href="/login";
                    },1000);
                }else {
                    error(data.message);
                }
            }
            ,error:function (msg){
                layer.close(indexcc)
                var mg = JSON.parse(msg.responseText);
                for (var k in mg.errors){
                    var tip = mg.errors[k][0];
                }

                if (k===undefined){
                    layer.msg('@lang('ac.systemError')', {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                    return false;
                }

                k = k.toLowerCase();
                k = k.replace(/\b\w+\b/g, function(word){
                    return word.substring(0,1).toUpperCase()+word.substring(1);
                });
                layer.msg(k+' : '+tip, {
                    offset: 'auto',
                    anim: 6,
                    area:['420px']
                });
                return false;
            }
        });
        return false;
    }
    $(".signUp .privacy").on("click",function(){
        layer.open({
            type: 1,
            area: ['70%','70%'],
            title: "@lang('ac.contentO')",
            shadeClose: false,
            shade:[.6,'#121111'],
            scrollbar: true,
            anim:0,
            content:"@lang('ac.contentOne')"
        });
    });
    $(".signUp .treams").on("click",function(){
        layer.open({
            type: 1,
            area: ['70%','70%'],
            title: "@lang('ac.contentT')",
            shadeClose: false,
            shade:[.6,'#121111'],
            scrollbar: true,
            anim:0,
            content:"@lang('ac.contentTwo')"
        });
    })
    function accountReg(){
        //正则表达验证账号格式
        var accountReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        //如果账号为空
        if($(".Account").val() ==''){
            $(".userError").text('@lang('ac.AccountEmpty')').show().css('color','#dc3030');
        }else if(!accountReg.test($('.Account').val())){
            $(".userError").text('@lang('ac.Formatting')').show().css('color','#dc3030');
        }else if(accountReg.test($('.Account').val())){
            $(".userError").text("@lang('ac.seeYouAgain')").show().css('color','#5fcc29');
        }
    }
    function passwordReg(){
        if($(".Password").val() == ''){
            $('.passError').text("@lang('ac.inputPassword')").show().css('color','#dc3030');
        }else{
            $('.passError').text("@lang('ac.seeYouAgain')").show().css('color','#5fcc29');
        }
    }
    function userNameReg(){
        // var userReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        var userReg = /^\S{2,18}$/;
        if(!userReg.test($(".userName").val())){
            $(".userError").text('@lang('ac.invalidName')').show().css('color','#dc3030');
        }else if(userReg.test($(".userName").val())){
            $(".userError").text('').show().css('color','#5fcc29');
        }
    }
    function emailReg(){
        var userReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        if(!userReg.test($(".email").val())){
            $(".emailRight").text('@lang('ac.invalidEmail')').show().css('color','#dc3030');
        } else if(userReg.test($(".email").val())){
            $(".emailRight").text('').show().css('color','#5fcc29');
        }
    }
    function emailAgainReg(){
        var userReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        if(!userReg.test($(".emailAgain").val())){
            $(".emailReRight").text('@lang('ac.invalidEmail')').show().css('color','#dc3030');
        }else if($(".emailAgain").val() !== $(".email").val()){
            $(".emailReRight").text('@lang('ac.emailConfirmation')').show().css('color','#dc3030');
        }else if($(".emailAgain").val() == $(".email").val() ){
            $(".emailReRight").text('').show().css('color','#5fcc29');
        }
    }
    function upPassReg(){
        var upPassReg = /^(\S{6,18})$/;
        if(!upPassReg.test($('.upPassReg').val())){
            $('.upPass').text("@lang('ac.invalidPassword')").show().css('color','#dc3030');
        }else if( upPassReg.test($('.upPassReg').val()) && $('.upPassReg').val().length >= 6 && $('.upPassReg').val().length <= 18 ){
            $('.upPass').text("").show().css('color','#5fcc29');
        }
    }
    function upPassAgain(){
        var upPassReg = /^(\S{6,18})$/;
        if(!upPassReg.test($('.passReRight').val())){
            $('.passReRight').text("@lang('ac.invalidPassword')").show().css('color','#dc3030');
        }else if($('.upPassAgain').val() !== $('.upPassReg').val()){
            $('.passReRight').text("@lang('ac.passwordConfirmation')").show().css('color','#dc3030');
        }else if($('.upPassAgain').val() == $('.upPassReg').val()){
            $('.passReRight').text('').show().css('color','#5fcc29');
        }
    }
//    function keyFun(input,num,parent){
//        //当输入一个框自动跳到另一个框
//        $(input).find('input').each(function (r, a) {
//            $(a).on("focus", function (e) {
//                // $(e.target).val("")
//            })
//            $(a).on("keydown", function () {
//                // return !1
//            })
//            $(a).on("keyup", function (a) {
//                if (a.keyCode >= 96 && a.keyCode <= 105 || a.keyCode >= 48 && a.keyCode <= 57) {
//                    if (num != (r)) {
//                        $(this).val(a.key);
//                        $(input).find("input")[r + 1].focus();
//                    } else {
//                        $(this).val(a.key);
//                        $(this).blur();
//                        if(parent) {
//                            $(parent).focus()
//                        }
//                    }
//                }
//
//                if (8 !== a.keyCode) {
//                    return !1;
//                } else {
//                    if (0 !== r) {
//                        $(input).find("input")[r-1].focus();
//                    }
//                }
//            })
//
//        })
//    }

    function two_pwd(){
        //点击ok返回输入的内容
//        var length= $('.authy_code').children('input').length;//几个输入框
//        console.log(length);
//        var val = '';
//        for(var i=0;i<=length-1;i++){
//            if (i<length-2){
//                val =val+$("#verify_"+i+"").val();
//            }
//        }
        var  val = $("#newtext").val();
        var email = $(" input[ name='loginEmail' ] ").val();
        var type = $("#type").val();
        var check = $("#check").val();
        var data = {email:email,type:type,check:check,code:val};
        $.ajax({
            url:'/user/auth'
            ,type:"post"
            ,data:data
            ,success:function (data) {
                if (data.status === 1){
                    window.location.href = '/';
                    return false;
                }else {
                    error(data.message);
                }
            }
            ,error:function (msg){
                var mg = JSON.parse(msg.responseText);
                for (var k in mg.errors){
                    var tip = mg.errors[k][0];
                }

                if (k===undefined){
                    layer.msg('@lang('ac.systemError')', {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                    return false;
                }

                //首字母大写
                k = k.toLowerCase();
                k = k.replace(/\b\w+\b/g, function(word){
                    return word.substring(0,1).toUpperCase()+word.substring(1);
                });
                layer.msg(k+' : '+tip, {
                    offset: 'auto',
                    anim: 6,
                    area:['420px']
                });
                return false;
            }
        });
    }

    //enter回车调用two_pwd()；
    $('.authy_code').on('keydown','#newtext',function(e){

        // 兼容FF和IE和Opera
        var theEvent = e || window.event;
        var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
        if (event.keyCode == "13") {
            two_pwd();
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
            }
            ,error:function (msg){
                console.log(msg);
            }
        });
    }

   //按鈕變成可點擊
    $(function(){


//        var Checked=$('.main-login .login-right .leftcheck input').attr();
        //登录不为空
        $('.main-login .login-right .signIn .form-group input').blur(function(){

            var Email=$('.main-login .login-right .form-group input.Account').val();
            var Password=$('.main-login .login-right .signIn .form-group input.Password').val();

            if(Email!=='' && Password !==''){
                $('.main-login .login-right .signIn .signBtn').removeClass('btnNotActiv');
            }
            else{
                $('.main-login .login-right .signIn .signBtn').addClass('btnNotActiv');

            }
        });

        //注册不为空
        $('.main-login .login-right .signUp .form-group input').blur(function(){

            var userName=$('.main-login .login-right .form-group input.userName').val();
            var email=$('.main-login .login-right .signIn .form-group input.email').val();
            var emailAgain=$('.main-login .login-right .signIn .form-group input.emailAgain').val();
            var upPassReg=$('.main-login .login-right .signIn .form-group input.upPassReg').val();
            var upPassAgain=$('.main-login .login-right .signIn .form-group input.upPassAgain').val();
            var code=$('.main-login .login-right .signIn .password input').val();
            alert(code);


//            if(Email!=='' && Password !==''){
//                $('.main-login .login-right .signIn .signBtn').removeClass('btnNotActiv');
//            }
//            else{
//                $('.main-login .login-right .signIn .signBtn').addClass('btnNotActiv');
//
//            }
        });


    });
</script>
</body>
</html>


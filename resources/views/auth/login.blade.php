<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cryptocoiners') }}</title>

    <link rel="shortcut icon" type="text/css" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/jquery.fullPage.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/reset.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/iconfont/iconfont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/animate.css') }}">
    <script src="{{ asset('js/ac/jquery.js') }}"></script>
    <script src="{{ asset('layer/layer.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/index.css') }}">
    <script src="{{ asset('js/ac/echarts.min.js') }}"></script>
    <script src="{{ asset('js/ac/wow.min.js') }}"></script>
    <script src="{{ asset('js/ac/jquery.fullPage.js') }}"></script>
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
</head>
<body>
<div id="header" style="">
    <!--未登录的首页头部-->

    <div class="head-row unlisted">
        <div>
            <a href="index.html">
                <img src="{{ asset('images/logo.png') }}">
            </a>
        </div>
        <div>
            <!--  <a class="btns">Sign up</a>
             <a class="btns">Sign in</a> -->
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
    <!--未登录结束-->
    <!--登录后的首页头部-->
    <!--  <div class="head-row login">
         <div><img src="images/logo.png"></div>
         <div class="login-right">
             <label>Welcome， <a>Zack</a></label>
             <span>
                 <label>En</label><i class="iconfont icon-xiala"></i>
                 <div class="lang">
                     <ul>
                        <li><a>简体中文</a><img src="images/cn.png"></li>
                        <li><a>繁体中文</a><img src="images/hk.png"></li>
                        <li><a>English</a><img src="images/flag_en.jpg"></li>
                        <em></em>
                   </ul>
                 </div>
             </span>
             <strong><i class="iconfont icon-caidan"></i></strong>

         </div>
     </div> -->
    <!--登录后结束-->
</div>
<div class="login">
    <div class="main-login">
        <ul>
            <li class="login-left">
                <div class="login-top  current">
                    <h3 class="title">SIGN IN</h3>
                    <p>Already our client?</p>
                    <p>Don't hesitate to enjoy big digita currency revolution. Please sign in to start trading.</p>
                </div>
                <div class="login-bottom ">
                    <h3 class="title">SIGN UP</h3>
                    <p>Not a client yet?</p>
                    <p>You might miss the big digital currency revolution. Please sign up to seize it.</p>
                </div>
                <p class="btn-or">
                    or
                </p>
            </li>
            <li class="login-right">
                <div class="signIn">
                    <h3 class="title">SIGN IN</h3>
                    <form>
                        <div class="form-group account">
                            <input type="text" name="account" class="Account" placeholder="Please enter your account" onblur="accountReg()">
                            <span class="userError"></span>
                        </div>
                        <div class="form-group password">
                            <input type="password" name="" class="Password" placeholder="Please enter password" onblur="passwordReg()">
                            <span class="passError"></span>
                        </div>
                        <div class="form-group signBottom">
                            <div class="leftcheck">
                                <input type="checkbox" name="" value="rembered">  Keep me signed in on this computer.
                            </div>
                            <div class="rightForget">
                                <a href="#" class="forgotPass">Forgot password?</a>
                            </div>
                        </div>
                        <div class="form-group submit">
                            <input type="submit" name="signBtn" class="signBtn" value="Sign in">
                        </div>
                    </form>
                </div>
                <div class="signUp" style="display:none">
                    <h3 class="title">SIGN UP</h3>
                    <form>
                        <div class="form-group account">
                            <input type="text" name="userName" class="userName" placeholder="Please enter your name" onblur="userNameReg()">
                            <span class="userError"></span>
                        </div>
                        <div class="form-group password">
                            <input type="email" name="email" class="email" placeholder="Please enter your Email" onblur="emailReg()">
                            <span class="emailRight"></span>
                        </div>
                        <div class="form-group password">
                            <input type="email" name="emailAgain" class="emailAgain" placeholder="Please enter your Email again" onblur="emailAgainReg()">
                            <span class="emailReRight"></span>
                        </div>
                        <div class="form-group password">
                            <input type="password" name="upPassReg" class="upPassReg" placeholder="Please enter your password" onblur="upPassReg()">
                            <span class="upPass"></span>
                        </div>
                        <div class="form-group password">
                            <input type="password" name="upPassAgain" class="upPassAgain" placeholder="Please enter password again" onblur="upPassAgain()">
                            <span class="passReRight"></span>
                        </div>
                        <div class="form-group password">
                            <input type="text" name="" placeholder="Please enter verification code">
                            <span class="verifiCode"><img src="{{ asset('images/verifiCode.png') }}"></span>
                        </div>
                        <div class="form-group signBottom">
                            <div class="leftcheck" id="layerDemo">
                                <input type="checkbox" name="" value="rembered">&nbsp;I have read <a href="#" class="treams"> Treams and Conditions</a> and agreed to <a href="javascript:0" class="privacy">Privacy Policy</a>.
                            </div>
                            <!-- <div class="rightForget">
                                <a href="#">Forgot password?</a>
                            </div> -->
                        </div>
                        <div class="form-group submit" >
                            <input type="submit" class="signUpBtn" value="Sign Up">
                        </div>
                    </form>
                </div>
                <ul class="forgetPassStep">
                    <li class="steps">
                        <div class="forgotTop">
                            <h3 class="title">RESET PASSWORD</h3>
                            <div>
                                <span class="leftStep">1</span><span>/3step</span>
                            </div>
                        </div>
                        <div class="stepItem step1">
                            <p>Please enter your Email address. Verification code will be sent to the Email box.</p>
                            <div class="form-group password">
                                <input type="email" name="" placeholder="Please enter your Email">
                                <!-- <span class="emailRight">Great.Please move on.</span> -->
                            </div>
                            <div class="form-group submit" >
                                <input type="button" class="nextTo2" value="Next">
                            </div>
                        </div>
                        <div class="stepItem step2">
                            <p>The verification code has been sent to <a href="#" class="Ebox">annie_zhu_123456@gmail</a>.com.Please get it from your mailbox and fill in the following input box.Thank you!</p>
                            <div class="form-group password">
                                <input type="text" name="" placeholder="Please enter verification code">
                                <!-- <span class="emailRight">Great.Please move on.</span> -->
                            </div>
                            <div class="form-group submit" >
                                <input type="button" class="nextTo3" value="Next">
                            </div>
                        </div>
                        <div class="stepItem step3">
                            <div class="form-group password">
                                <input type="text" name="" placeholder="Please enter your password">
                                <!-- <span class="emailRight">Great.Please move on.</span> -->
                            </div>
                            <div class="form-group password">
                                <input type="text" name="" placeholder="Please enter your password again">
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
<script type="text/javascript">
    $(".signUp .privacy").on("click",function(){
        layer.open({
            type: 1,
            area: ['40%','50%'],
            title: 'Privacy policy',
            shadeClose: false,
            shade:[.6,'#121111'],
            scrollbar: false,
            anim:0,
            content: '\<\div style="padding:20px"><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p>\<\/div>'
        });
    })
    $(".signUp .treams").on("click",function(){
        layer.open({
            type: 1,
            area: ['40%','50%'],
            title: 'Treams and Conditions',
            shadeClose: false,
            shade:[.6,'#121111'],
            scrollbar: false,
            anim:0,
            content: '\<\div style="padding:20px"><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p>\<\/div>'
        });
    })
    function accountReg(){
        //正则表达验证账号格式
        var accountReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        //如果账号为空
        if($(".Account").val() ==''){
            $(".userError").text('Account cannot be empty!').show().css('color','#dc3030');
        }else if(!accountReg.test($('.Account').val())){
            $(".userError").text('Formatting error！').show().css('color','#dc3030');
        }else if(accountReg.test($('[name=account]').val())){
            $(".userError").text("Great to see you again!").show().css('color','#5fcc29');
        }
    }
    function passwordReg(){
        if($('.password').val() == ''){
            $('.passError').text("Please input a password!").show().css('color','#dc3030');
        }else if($('.password').val() === '123456'){
            $('.passError').text("Great to see you again!").show().css('color','#5fcc29');
        }else{
            $('.passError').text("Sorry! Try again please.").show().css('color','#dc3030');
        }
    }
    function userNameReg(){
        var userReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        if($(".userName").val() == ''){
            $(".userError").text('enter one user name').show().css('color','#dc3030');
        }else if(!userReg.test($(".userName").val())){
            $(".userError").text('Incorrect username format！').show().css('color','#dc3030');
        }else if(userReg.test($(".userName").val())){
            $(".userError").text('Nice name!！').show().css('color','#5fcc29');
        }
    }
    function emailReg(){
        var userReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        if($(".email").val() == ''){
            $(".emailRight").text('Please enter the mailbox').show().css('color','#dc3030');
        }else if(!userReg.test($(".email").val())){
            $(".emailRight").text('Incorrect mailbox format！').show().css('color','#dc3030');
        }else if(userReg.test($(".email").val())){
            $(".emailRight").text('Great.Please move on！').show().css('color','#5fcc29');
        }
    }
    function emailAgainReg(){
        var userReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        if($(".emailAgain").val() == ''){
            $(".emailReRight").text('Please enter mailbox again!').show().css('color','#dc3030');
        }else if($(".emailAgain").val() !== $(".email").val()){
            $(".emailReRight").text('Two input inconsistencies！').show().css('color','#dc3030');
        }else if($(".emailAgain").val() == $(".email").val() ){
            $(".emailReRight").text('Great.Please move on！').show().css('color','#5fcc29');
        }
    }
    function upPassReg(){
        var upPassReg = /^[a-zA-Z0-9_]*$/;
        if($('.upPassReg').val() == ''){
            $('.upPass').text("Please input a password!").show().css('color','#dc3030');
        }else if( !upPassReg.test($('.upPassReg').val()) || $('.upPassReg').val().length <= 6 || $('.upPassReg').val().length >= 16){
            $('.upPass').text(" 6-16 letters numbers underlines").show().css('color','#dc3030');
        }else if(  upPassReg.test($('.upPassReg').val()) && $('.upPassReg').val().length >= 6 && $('.upPassReg').val().length <= 16 ){
            $('.upPass').text("High level security.").show().css('color','#5fcc29');
        }
    }
    function upPassAgain(){
        if($('.upPassAgain').val() == ''){
            $('.passReRight').text("Please enter password again!").show().css('color','#dc3030');
        }else if($('.upPassAgain').val() !== $('.upPassReg').val()){
            $('.passReRight').text("Two input inconsistencies!").show().css('color','#dc3030');
        }else if($('.upPassAgain').val() == $('.upPassReg').val()){
            $('.passReRight').text("Wll done").show().css('color','#5fcc29');
        }
    }
</script>
</body>
</html>


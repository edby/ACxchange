<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Withdrawal confirmation</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{ asset('dashboard/bootstrap/css/bootstrap.min.css') }}?id={{str_random(20)}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <!-- <link href="./../dist/css/font-awesome.min.css" rel="stylesheet" type="text/css" /> -->
    <link href="{{ asset('dashboard/dist/fonts/iconfonts/iconfont.css') }}?id={{str_random(20)}}" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{ asset('dashboard/dist/css/style.css') }}?id={{str_random(20)}}" rel="stylesheet" type="text/css" />
</head>
<body class="sidebar-mini error2">
    <div>
        <header class="main-header">
            <!-- Logo -->
            <a href="" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">AC</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><img src="{{ asset('dashboard/dist/img/login-logo.png') }}?id={{str_random(20)}}"></span>
            </a>
        </header>
       <!-- 内容 -->
       <div class="bodyerror">
           <div class="boxerror">
               <img src="{{ asset('dashboard/dist/img/error.png') }}?id={{str_random(20)}}">
               <div class="cn_error">
                   @if(!isset($error)) 由于过高的提现请求，请耐心等待24小时，您将会受到提现成功通知
                   @else {{$error['zh']}}
                   @endif
               </div>
               <p class="en_error">
                   @if(!isset($error))
                       Due to hight withdrawals requests,withdrawals processing
                       time might take up to 24 hours.Please be patient.You will be
                       notified once withdrawal is done.
                   @else {{$error['en']}}
                   @endif
               </p>
               <button><a href="{{env('APP_URL').'/wallet/index'}}">Go Back Home</a></button>
           </div>
       </div>
    </div>
    <!-- ./wrapper -->
    <!-- jQuery 2.1.4 -->
    <script src="{{ asset('dashboard/plugins/jQuery/jQuery-2.1.4.min.js') }}?id={{str_random(20)}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset('dashboard/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <!-- FastClick -->
    <script src="{{ asset('dashboard/plugins/fastclick/fastclick.min.js') }}?id={{str_random(20)}}"></script>
    <!-- App -->
    <script src="{{ asset('dashboard/dist/js/app.min.js') }}?id={{str_random(20)}}" type="text/javascript"></script>
    <script src="{{ asset('dashboard/dist/js/index.js') }}?id={{str_random(20)}}" type="text/javascript"></script>
</body>

</html>
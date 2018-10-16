<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="Generator" content="EditPlus">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ACxchange') }}</title>
    <link rel="shortcut icon" type="text/css" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/jquery.fullPage.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/reset.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/iconfont/iconfont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/animate.css') }}">
    <script src="{{ asset('js/ac/jquery.js') }}"></script>
    <script src="{{ asset('layer/layer.js') }}"></script>
    @yield('style')
    @if(url()->current() === action('FrontEnd\TradeController@index'))
        <link rel="stylesheet" href="{{ asset('css/ac/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/ac/jquery.mCustomScrollbar.min.css') }}">
        <script src="{{ asset('js/ac/amcharts.js') }}"></script>
        <script src="{{ asset('js/ac/serial.js') }}"></script>
        <script src="{{ asset('js/ac/amstock.js') }}"></script>
        <script src="{{ asset('js/ac/marketchar.js') }}?id={{str_random(20)}}"></script>
    @endif
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/index.css') }}?{{str_random(16)}}">
    <script>
        var imgUrl = '{{asset('images')}}';
    </script>
    <script src="{{ asset('js/ac/echarts.min.js') }}"></script>
    <script src="{{ asset('js/ac/wow.min.js') }}"></script>
    <script src="{{ asset('js/ac/jquery.fullPage.js') }}"></script>
    <script src="{{ asset('js/ac/index.js') }}?{{str_random(16)}}"></script>
    <script src="{{ asset('js/ac/toastr.min.js') }}"></script>
    <script>
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    </script>
    @if(url()->current() === action('FrontEnd\UserController@index'))
        <script src="{{ asset('js/ac/birthday.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('css/ac/bootstrap.min.css')}}">
        <script src="{{ asset('js/ac/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/ac/user.js')}}?id={{str_random(20)}}"></script>
    @elseif (url()->current() === action('FrontEnd\WalletController@index'))

    @else
    @endif
</head>
<body class="tradebody">
{{--头部导航栏--}}
@component('front.headBar')@endcomponent
@yield('content')
{{--底部与右侧--}}
@component('front.footBar',['current'=>url()->current()])@endcomponent
@if(url()->current() === action('FrontEnd\UserController@index'))
    @component('front.extra',['pin'=>$user->pin])@endcomponent
@endif
</body>
</html>
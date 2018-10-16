@extends('layouts.email')
@section('user')
 {{ $name  or 'User'}}
@endsection
@section('content')
 <p>@lang('email.Withdraw_confirm') <a style="word-wrap : break-word;word-break:break-all;" href="{{getenv('APP_URL')}}/withdrawEmailVerify/{{$token or 'xx'}}" style="display:inline-block;max-width:560px!important;word-break:break-all" target="_blank">{{getenv('APP_URL')}}/withdrawEmailVerify/{{$token or 'xxx'}}</a></p>
 <br>
@endsection
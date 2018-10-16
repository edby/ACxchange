@extends('layouts.email')
@section('content')
    We regret to inform you that your withdrawal of {{$amount}} on {{$date_en}}, has been rejected. Kindly contact us at contact@novtraderex.com if you have any enquiries. We apologise for the inconvenience caused. <br>
    <br>
    我们很遗憾地通知您，您在 {{$date_cn}} 提现的 {{$amount}}，已被拒绝。如果您有任何疑问，请通过 contact@novatraderex.com 与我们联系。对由此造成的不便，我们深表歉意。<br>
    <br>
@endsection
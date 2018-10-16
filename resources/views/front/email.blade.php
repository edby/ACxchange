{{--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">--}}
{{--<html lang="{{ app()->getLocale() }}">--}}
{{--<head>--}}
{{--<meta http-equiv="Content-Type" content="Multipart/Alternative;charset=UTF-8">--}}
{{--<meta name="csrf-token" content="{{ csrf_token() }}">--}}
{{--<title>{{ config('app.name', 'Cryptocoiners') }}</title>--}}
{{--</head>--}}
{{--<body>--}}
{{--<style type="text/css">--}}
{{--@font-face {--}}
{{--font-family: 'Open Sans'; font-style: normal; font-weight: 300; src: local('Open Sans Light'), local('OpenSans-Light'), url('https://fonts.gstatic.com/s/opensans/v14/DXI1ORHCpsQm3Vp6mXoaTYnF5uFdDttMLvmWuJdhhgs.ttf') format('truetype');--}}
{{--}--}}
{{--@font-face {--}}
{{--font-family: 'Open Sans'; font-style: normal; font-weight: 400; src: local('Open Sans Regular'), local('OpenSans-Regular'), url('https://fonts.gstatic.com/s/opensans/v14/cJZKeOuBrn4kERxqtaUH3aCWcynf_cDxXwCLxiixG1c.ttf') format('truetype');--}}
{{--}--}}
{{--@font-face {--}}
{{--font-family: 'Open Sans'; font-style: normal; font-weight: 600; src: local('Open Sans SemiBold'), local('OpenSans-SemiBold'), url('https://fonts.gstatic.com/s/opensans/v14/MTP_ySUJH_bn48VBG8sNSonF5uFdDttMLvmWuJdhhgs.ttf') format('truetype');--}}
{{--}--}}
{{--</style>--}}
{{--<style type="text/css">--}}
{{--.qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {display: none !important;}--}}
{{--</style>--}}
{{--<style>.qmbox #mailContentContainer{height:auto!important;}</style>--}}
{{--<div style="position:relative;font-size:14px;height:auto;padding:15px 15px 10px 15px;z-index:1;zoom:1;line-height:1.7;background: #F8F9FD" bgcolor="#F8F9FD">--}}
{{--<div class="embox" style="margin-top:15px;margin-bottom:15px;">--}}
{{--<table border="0" cellpadding="0" cellspacing="0" width="100%" style="-webkit-text-size-adjust: none; border-collapse: collapse">--}}
{{--<tbody style="-webkit-text-size-adjust: none">--}}
{{--<tr style="-webkit-text-size-adjust: none">--}}
{{--<td style="-webkit-text-size-adjust: none;">--}}
{{--<div style="-webkit-text-size-adjust: none; margin: 0 auto; max-width: 612px;">--}}
{{--<table border="0" cellpadding="0" cellspacing="0" width="100%" style="-webkit-text-size-adjust: none">--}}
{{--<tbody style="-webkit-text-size-adjust: none">--}}
{{--<tr style="-webkit-text-size-adjust: none">--}}
{{--<td style="-webkit-text-size-adjust: none;">--}}
{{--{{asset('/email/email_head.png')}}--}}
{{--<div style="background-image: url('{{asset('email/email_head.png')}}'); background-repeat: no-repeat;height:72px;">--}}
{{--</div>--}}

{{--</td>--}}
{{--<tr style="-webkit-text-size-adjust: none">--}}
{{--<td style="-webkit-text-size-adjust: none;">--}}
{{--{{ asset('/email/email_box.png')}}--}}
{{--{{$email}} === {{$code}}--}}
{{--<div style='background-image: url("{{asset('email/email_box.png')}}"); background-repeat:repeat-y;-webkit-text-size-adjust: none; color: #525C65; line-height: 22px; margin-top:0;margin-bottom:0;margin-left:0;margin-right: 0;padding-left:75px;padding-right:75px; text-align: justify;padding-top:20px;'>--}}
{{--<p style="margin-top:0;margin-bottom:20px;margin-left:0;margin-right: 0;padding-right:60px;">--}}
{{--<span style="margin-top:5px;margin-bottom:5px;font-weight: bold;font-size:16px;font-family: 'Open+Sans', 'Open Sans', Helvetica, Arial, sans-serif;">@lang('ac.DearCustomer') {{$email}}</span>--}}
{{--</p>--}}
{{--<div style="-webkit-text-size-adjust: none; -webkit-text-stroke-width: 0px; color: rgb(82, 92, 101); display: inline !important; float: none; font-family: 'Open+Sans', 'Open Sans', Helvetica, Arial, sans-serif; font-size: 14px;padding:0;">{{$text}}&nbsp;&nbsp;<span style="color: #1c2529"> {!! $code !!}</span>&nbsp;&nbsp; .</div>--}}
{{--<table border="0" cellpadding="0" cellspacing="0" width="100%" style="-webkit-text-size-adjust: none;">--}}
{{--<tbody style="-webkit-text-size-adjust: none;">--}}
{{--<tr style="-webkit-text-size-adjust: none;">--}}
{{--<td style="-webkit-text-size-adjust: none;">--}}
{{--<p style="margin-top:20px;margin-bottom:0;margin-left:0;margin-right: 0; text-align: left;font-weight: 600;font-family: 'Open+Sans', 'Open Sans', Helvetica, Arial, sans-serif;font-size:14px;padding-right:60px;">Best Regards</p>--}}
{{--</td>--}}
{{--</tr>--}}
{{--<tr style="-webkit-text-size-adjust: none;">--}}
{{--<td style="-webkit-text-size-adjust: none;">--}}
{{--<p style="margin-top:5px;margin-bottom:5px;margin-left:0;margin-right: 0;font-weight: 600; text-align: left;font-family: 'Open+Sans', 'Open Sans', Helvetica, Arial, sans-serif;font-size:14px;padding-right:60px;">Alliance Capitals Exchange Team</p>--}}
{{--</tr>--}}
{{--</tbody>--}}
{{--</table>--}}
{{--</div>--}}
{{--</td>--}}
{{--</tr>--}}
{{--<tr  style="-webkit-text-size-adjust: none">--}}
{{--{{asset('/email/email_footer.png')}}--}}
{{--<td style="-webkit-text-size-adjust: none;">--}}
{{--<div style='background-image: url("{{asset('email/email_footer.png')}}"); background-repeat: no-repeat;height:112px;'></div>--}}
{{--</td>--}}
{{--</tr>--}}
{{--</tr>--}}
{{--</tbody>--}}
{{--</table>--}}
{{--</div>--}}
{{--</td>--}}
{{--</tr>--}}
{{--</tbody>--}}
{{--</table>--}}
{{--</div>--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="{{ app()->getLocale() }}">
<head>
    <head>
        <meta http-equiv="Content-Type" content="Multipart/Alternative;charset=UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ACXchange') }}</title>
    </head>
<body>
<style type="text/css">
    @font-face {
        font-family: 'Open Sans'; font-style: normal; font-weight: 300; src: local('Open Sans Light'), local('OpenSans-Light'), url('https://fonts.gstatic.com/s/opensans/v14/DXI1ORHCpsQm3Vp6mXoaTYnF5uFdDttMLvmWuJdhhgs.ttf') format('truetype');
    }
    @font-face {
        font-family: 'Open Sans'; font-style: normal; font-weight: 400; src: local('Open Sans Regular'), local('OpenSans-Regular'), url('https://fonts.gstatic.com/s/opensans/v14/cJZKeOuBrn4kERxqtaUH3aCWcynf_cDxXwCLxiixG1c.ttf') format('truetype');
    }
    @font-face {
        font-family: 'Open Sans'; font-style: normal; font-weight: 600; src: local('Open Sans SemiBold'), local('OpenSans-SemiBold'), url('https://fonts.gstatic.com/s/opensans/v14/MTP_ySUJH_bn48VBG8sNSonF5uFdDttMLvmWuJdhhgs.ttf') format('truetype');
    }
</style>
<style type="text/css">
    .qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {display: none !important;}
</style>
<style>.qmbox #mailContentContainer{height:auto!important;}</style>
<div style="position:relative;font-size:14px;height:auto;padding:15px 15px 10px 15px;z-index:1;zoom:1;line-height:1.7;background: #F8F9FD" bgcolor="#F8F9FD">
    <div class="embox" style="margin-top:15px;margin-bottom:15px;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="-webkit-text-size-adjust: none; border-collapse: collapse">
            <tbody style="-webkit-text-size-adjust: none">
            <tr style="-webkit-text-size-adjust: none">
                <td style="-webkit-text-size-adjust: none;">
                    <div style="-webkit-text-size-adjust: none; margin: 0 auto; max-width: 612px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="-webkit-text-size-adjust: none">
                            <tbody style="-webkit-text-size-adjust: none">
                            <tr style="-webkit-text-size-adjust: none">
                                <td style="-webkit-text-size-adjust: none;">
                                    <div style="background-image: url({{asset('email/acxchangeHeader.jpg')}});background-repeat: no-repeat;height:86px;">
                                    </div>

                                </td>
                            <tr style="-webkit-text-size-adjust: none">
                                <td style="-webkit-text-size-adjust: none;">
                                    <div style="background-color:#fff;background-repeat:repeat-y;-webkit-text-size-adjust: none; color: #525C65; line-height: 22px; margin-top:0;margin-bottom:0;margin-left:0;margin-right: 0;padding-left:45px;padding-right:45px; text-align: justify;padding-top:50px;padding-bottom:50px;">
                                        <p style="margin-top:0;margin-bottom:20px;margin-left:0;margin-right: 0;padding-right:60px;">
                                            <span style="margin-top:5px;margin-bottom:5px;font-weight: bold;font-size:16px;font-family: 'Open+Sans', 'Open Sans', Helvetica, Arial, sans-serif;">@lang('ac.DearCustomer') {{$email}}</span>
                                        </p>
                                        <div style="-webkit-text-size-adjust: none; -webkit-text-stroke-width: 0px; color: rgb(82, 92, 101); display: inline !important; float: none; font-family: 'Open+Sans', 'Open Sans', Helvetica, Arial, sans-serif; font-size: 14px;padding:0;">{{$text}}&nbsp;&nbsp;<span style="color: #1c2529"> {!! $code !!}</span>&nbsp;&nbsp; .</div>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="-webkit-text-size-adjust: none;">
                                            <tbody style="-webkit-text-size-adjust: none;">
                                            <tr style="-webkit-text-size-adjust: none;">
                                                <td style="-webkit-text-size-adjust: none;">
                                                    <p style="margin-top:20px;margin-bottom:0;margin-left:0;margin-right: 0; text-align: left;font-weight: 600;font-family: 'Open+Sans', 'Open Sans', Helvetica, Arial, sans-serif;font-size:14px;padding-right:60px;">Best Regards</p>
                                                </td>
                                            </tr>
                                            <tr style="-webkit-text-size-adjust: none;">
                                                <td style="-webkit-text-size-adjust: none;">
                                                    <p style="margin-top:5px;margin-bottom:5px;margin-left:0;margin-right: 0;font-weight: 600; text-align: left;font-family: 'Open+Sans', 'Open Sans', Helvetica, Arial, sans-serif;font-size:14px;padding-right:60px;">ACxchange Team</p>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr  style="-webkit-text-size-adjust: none">
                                <td style="-webkit-text-size-adjust: none;">
                                    <div style="background-image: url({{asset('email/acxchangeFooter.jpg')}});background-repeat: no-repeat;height:18px;">
                                        <a href="http://acxchange.io" style="display: inline-block;width: 100%;height:100%;"></a>
                                    </div>
                                </td>
                            </tr>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
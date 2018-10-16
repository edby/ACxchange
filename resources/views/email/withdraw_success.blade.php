<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="Multipart/Alternative;charset=UTF-8">
    <title>email-success</title>
</head>

<body>
<style type="text/css">
    .qmbox style,
    .qmbox script,
    .qmbox head,
    .qmbox link,
    .qmbox meta {
        display: none !important;
    }
</style>
<style>
    .qmbox #mailContentContainer {
        height: auto !important;
    }
</style>
<div style="position:relative;font-size:14px;height:auto;padding:15px 15px 10px 15px;z-index:1;zoom:1;line-height:1.7;background: #F8F9FD"
     bgcolor="#F8F9FD">
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
                                </td>
                            </tr>
                            <tr style="-webkit-text-size-adjust: none">
                                <td style="-webkit-text-size-adjust: none;">
                                    <div style="background-image: url({{asset('email/acx-email-bg.png')}});height:520px;-webkit-text-size-adjust: none; color: #525C65; line-height: 22px; margin-top:0;margin-bottom:0;margin-left:0;margin-right: 0;padding-left:75px;padding-right:75px; text-align: justify;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="-webkit-text-size-adjust: none;">
                                            <tbody style="-webkit-text-size-adjust: none;">
                                            <tr style="-webkit-text-size-adjust: none;">
                                                <td style="-webkit-text-size-adjust: none;">
                                                    <div style="background-image: url({{asset('email/acx-success.png')}});background-repeat:no-repeat;background-position:center center;height:231px;line-height: 22px; margin-top:0;margin-bottom:0;margin-left:0;margin-right: 0;margin-bottom: 20px;margin-top:125px;"></div>
                                                </td>
                                            </tr>
                                            <tr style="-webkit-text-size-adjust: none;">
                                                <td style="-webkit-text-size-adjust: none;">
                                                    <p style="margin-top:20px;margin-bottom:0;margin-left:0;margin-right: 0; text-align: left;font-weight: 600;font-family: Helvetica, Arial, sans-serif;font-size:16px;font-size:16px;color: #3c3c3c;text-align:center;"></p>
                                                </td>
                                            </tr>
                                            <tr style="-webkit-text-size-adjust: none;">
                                                <td style="-webkit-text-size-adjust: none;">
                                                    <p style="margin:5px auto;font-weight: 600; text-align: center;font-family: Helvetica, Arial, sans-serif;font-size:14px;color: #00b2fd;font-size:14px;word-wrap:break-word;word-break:break-all;">@lang('email.Dear'),@lang('email.withdraw_success1'){{$amount}} {{$currency}}@lang('email.withdraw_success2')</p>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr style="-webkit-text-size-adjust: none">
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
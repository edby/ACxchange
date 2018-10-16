<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="Generator" content="EditPlus">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'ACxchange') }}</title>
    <link rel="shortcut icon" type="text/css" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/reset.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/toastr.min.css') }}">
    <style>
        #contact_us{
            width: 100%;
            height: 100%;
            background:#161b2e ;

        }
        .contact{
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;

        }
        .contact_us2 .logo_contact img{ width: 300px; margin-top: 20px;}
        .contact_us2 .box_contact{
            margin-top: 20px;
            background: #2c3557;
            padding-bottom: 30px;
        }
        .contact_us2 .box_contact h4{ height: 80px; line-height: 80px; color: #dde1e7; font-size:26px; }
        .contact_us2 .box_contact p{
            color:#dde1e7;
            font-size:12px;
        }
        .box_contact .from_send{
            width: 60%;
            margin: 0 auto;
            padding-top: 50px;

        }
        .box_contact .from_send .kin{
            margin-bottom: 20px;

        }
        .box_contact .from_send .kin input{
            width: 100%;
            height: 40px;
            line-height: 40px;
            text-indent: 12px;
            background: #242e51;
            color: #fff;
            border: none;


        }
        .box_contact .from_send .kin textarea{
            width: 100%;
            height: 100px;
            padding: 12px;
            background: #242e51;
            color: #fff;
            box-sizing: border-box;
            border: none;
            resize: none;

        }
        .box_contact .from_send .kin .sendBtn{
            display: block;
            text-align: center;
            width:300px ;
            height: 50px;
            line-height: 50px;
            background: #2862c7;
            border-radius: 4px;
            color: #fff;
            margin: 30px auto;

        }
        @media (max-width: 1000px) {
            .contact{ max-width:90%;}

        }
        @media (max-width: 700px){
            .contact_us2 .box_contact{ padding-bottom: 10px;}
            .box_contact .from_send{ width:92%; padding-top: 10px; }
            .box_contact .from_send .kin .sendBtn{
                width: 200px;
            }
            .contact_us2 .logo_contact img{ width:48%; }

        }
    </style>

</head>
<body id="contact_us">
<div class="contact">
    <div class="contact_us2">
        <div class="logo_contact"><img src="{{asset('images/logo.png')}}"></div>
        <div class="box_contact">
            <h4>@lang('ac.Getintouchwithus')</h4>
            <div class="from_send">
                <div class="kin">
                    <input type="text" name="email" class="email" placeholder="@lang('ac.enterEmail')">
                </div>
                <div class="kin">
                    <input type="text" name="subject" class="subject" placeholder="@lang('ac.Subject')">
                </div>
                <div class="kin">
                    <textarea name="textarea" class="textarea" placeholder="@lang('ac.message')"></textarea>
                </div>
                <div class="kin">
                    <a class="sendBtn">@lang('ac.SENDMESSAGE')</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('js/ac/jquery.js')}}"></script>
<script src="{{ asset('js/ac/toastr.min.js') }}"></script>

<script>
    toastr.options = {
                   "closeButton": false,//是否配置关闭按钮
                   "debug": false,//是否开启debug模式
                   "newestOnTop": false,//新消息是否排在最上层
                   "progressBar": false,//是否显示进度条
                   "positionClass": "toast-top-right",//消息框的显示位置
                   "preventDuplicates": true,//是否阻止弹出多个消息框
                   "onclick": null,//点击回调函数
                   "showDuration": "300",
                   "hideDuration": "1000",
                   "timeOut": "1500",//1.5s后关闭消息框
                   "extendedTimeOut": "1000",
                   "showEasing": "swing",
                   "hideEasing": "linear",
                   "showMethod": "fadeIn",
                   "hideMethod": "fadeOut"
        }

    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    $(function () {
        $('.contact .sendBtn').click(function () {
            var email=$('.box_contact .from_send .kin input.email').val();
            var subject=$('.box_contact .from_send .kin input.subject').val();
            var textarea=$('.box_contact .from_send .kin .textarea').val();
        //去掉email其左右的空格
            var email2 = $.trim(email);
        //验证邮箱格式的js正则表达式
            var isEmail = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;

            if(email==''){
                toastr.error('@lang("ac.enterEmail")');
            }else if(!(isEmail.test(email))){
                toastr.error('@lang("ac.emailNotValid")');

            }else if(subject==''){
                toastr.error('@lang("ac.enterSubject")');

            }else if (textarea==''){
                toastr.error('@lang("ac.PleaseContent")');

            }else {
                //--发送 联系结果
                var data_emal = {
                    email:email,
                    subject:subject,
                    contact_content:textarea
                };
                contact_us(data_emal);
            }

        })
    });
    //请求联系我们
    function contact_us(datas){
        $.ajax({
            url:'/contact'
            ,type:'post'
            ,data:datas
            ,success:function (data) {
                //提示提交成功
                if (data.code ==200){
                    toastr.success('@lang("ac.Success")');
                }else {
                    toastr.error('@lang("ac.Fail")')
                }
            }
            ,error:function(){
                toastr.error('@lang("ac.Fail")')
            }
        });
    }
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('dashboard/dist/css/login.css')}} ">
    <link rel="stylesheet" href="{{asset('dashboard/js/toastr.min.css')}}">
</head>

<body>
<title>login</title>
    <div class="login-container">

        <img class="logo" src="{{ asset('dashboard/dist/img/login-logo.png')}} " alt="">

        <div class="form-warp">
            <h3>Welcome Back</h3>
            <div class="form-group">
                <label for="InputManager">Admin</label>
                <div class="form-conter">
                    <input type="text" id="InputManager" name="name">
                </div>
            </div>
            <div class="form-group">
                <label for="InputPassword">Password</label>
                <div class="form-conter">
                    <input type="password" id="InputPassword" name="password">
                </div>
            </div>
            <div class="form-group">
                <input value="Login" type="button" id="subsend" class="login-btn">
            </div>
        </div>
    </div>
</body>
<script src="{{asset('dashboard/js/jquery-3.2.1.min.js')}}"></script>
<script src="{{asset('dashboard/js/toastr.min.js')}}"></script>
<script src="{{asset('dashboard/js/toastr_conf.js')}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function asd(){
        $("#loginForm :input").trigger("blur");
        if($('#loginForm .error').length == 0) {
            /*$('#loginForm').submit()*/
            $.ajax({
                url:"{{route('manager_login')}}",
                data:{'name':$("#InputManager").val(),'password':$("#InputPassword").val()},
                dataType:'json',
                type:'post',
                success:function (msg) {
                    if(msg.code == 200) {
                        window.location.href = "{{env('ADMIN_DOMAIN').'/ACdashboard'}}";
                    }else {
                        toastr.error(msg.message);
                    }
                },
                error:function (msg) {
                    var json=JSON.parse(msg.responseText);
                    if (json['errors']['email']){
                        toastr.error(json['errors']['email']);
                    }else if(json['errors']['password']){
                        toastr.error(json['errors']['password']);
                    }else (
                            toastr.error('error')
                    )
                }
            });
        }

    }
    $(function(){
        $("#subsend").on('click',function(){
            asd();
        });
        $("#InputManager").keydown(function(e) {
            if (e.keyCode == 13) {//Enter键调用
                asd();
            }
        });
        $("#InputPassword").keydown(function(e) {
            if (e.keyCode == 13) {//Enter键调用
                asd();
            }
        });
    });

</script>

</html>
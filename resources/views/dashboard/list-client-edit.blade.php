@extends('dashboard.layout.app')
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl"><a href="">List Clients</a><span>&nbsp;&gt;&nbsp;Edit List Clients</span></div>
        <div class="wrapper-content">
            <div class="edit-form">
                <div class="ids"><span>ID :</span><label id="editId">{{$user->id}}</label></div>
                <form action="">
                    <ul>
                        <li>
                            <div class="edit-inline">
                                <label for="">Name</label>
                                <input type="text" id="editName" value="{{$user->name}}">
                                <span class="tips"></span>
                            </div>
                            <div class="edit-inline">
                                <label for="">Email</label>
                                <input type="text" id="editEmail" value="{{$user->email}}">
                                <span class="tips"></span>
                            </div>
                        </li>
                        <li>
                            <div class="edit-inline">
                                <label for="">Password</label>
                                <input type="text" id="editPassword" placeholder="No changes if it is null">
                                <span class="tips"></span>
                            </div>
                            <div class="edit-inline">
                                <label for="">Secpass</label>
                                <input type="text" id="editPin" placeholder="No changes if it is null">
                                <span class="tips"></span>
                            </div> 
                        </li>
                    </ul>
                    <div class="sele0">
                            <div>
                                <label>Email Confirmed</label>
                                <p>
                                        <span> <input type="radio" name="register_confirm" value="0" id="no" @if($user->register_confirm == 0) checked @endif><label for="no">No</label></span>
                                        <span> <input type="radio" name="register_confirm" value="1" id="yes" @if($user->register_confirm == 1) checked @endif><label for="yes">Yes</label></span>
                                </p>
                            </div>
                            <div>
                                <label>Certification Status</label>
                                <p>
                                    <span><input type="radio" name="is_certification" value="0" id="notyetupload" @if($user->is_certification == 0) checked @endif><label for="notyetupload">Not yet upload</label></span>
                                    <span><input type="radio" name="is_certification" value="1" id="upload" @if($user->is_certification == 1) checked @endif><label for="upload">Upload</label> </span>
                                    <span><input type="radio" name="is_certification" value="3" id="nopass" @if($user->is_certification == 3) checked @endif><label for="nopass">No pass</label></span>
                                    <span><input type="radio" name="is_certification" value="4" id="pass" @if($user->is_certification == 4) checked @endif><label for="pass">Pass</label> </span>
                                </p>
                            </div>
                            <div>
                                    <label>Default 2FA</label>
                                    <p>
                                        <span><input type="radio" name="auth_type" value="0" id="notyet" @if($user->auth_type == 0) checked @endif><label for="notyet">Not yet</label></span>
                                        <span><input type="radio" name="auth_type" value="2" id="authy" @if($user->auth_type == 2) checked @endif><label for="authy">Authy</label></span>
                                        <span><input type="radio" name="auth_type" value="3" id="sms" @if($user->auth_type == 3) checked @endif><label for="sms">SMS</label></span>
                                        <span><input type="radio" name="auth_type" value="1" id="google" @if($user->auth_type == 1) checked @endif><label for="google">Google</label></span>
                                    </p>
                            </div>
                        </div>

                    <div class="edit-btn">
                        <button type="button" class="update-btn">UPDATE</button>
                        <button type="button" class="cancel-btn">CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $(function() {
        $(".update-btn").on('click',function(){
            $.ajax({
                url:"{{route('update_user')}}",
                data:{
                    'id':$("#editId").html(),
                    'name':$("#editName").val(),
                    'email':$("#editEmail").val(),
                    'password':$("#editPassword").val(),
                    'pin':$("#editPin").val(),
                    'register_confirm':$('input[name="register_confirm"]:checked').val(),
                    'is_certification':$('input[name="is_certification"]:checked').val(),
                    'auth_type':$('input[name="auth_type"]:checked').val()
                },
                dataType:'json',
                type:'post',
                success:function (msg) {
                    if(msg.code == 200) {
                        layer.msg('update success', {
                            offset: 'auto',
                            anim: 0,
                            area:['420px']
                        });
                        setTimeout(function(){
                            window.location.href = "{{env('ADMIN_DOMAIN').'/getUserList'}}";
                        },800)
                    }else {
                        layer.msg('Error Data', {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                    }
                },
                error:function () {
                    layer.msg('Error Ajax', {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                }
            });
        });
        $(".cancel-btn").on('click',function(){
            window.location.href = "{{env('ADMIN_DOMAIN').'/getUserList'}}";
        })
    })
</script>
@endsection
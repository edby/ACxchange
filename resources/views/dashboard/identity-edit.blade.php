@extends('dashboard.layout.app')
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="wrapper-content">
            <div class="edit-form">
                <div class="ids"><span>User ID :</span><label id="editId">{{$user->id}}</label></div>
                <form action="">
                    <ul>
                        <li>
                            <div class="edit-inline">
                                <label for="firstName">First Name :</label>
                                <input type="text" id="firstName" value="{{$user->first_name}}">
                            </div>
                        </li>
                        <li>
                            <div class="edit-inline">
                                <label for="lastName">Last Name :</label>
                                <input type="text" id="lastName" value="{{$user->last_name}}">
                            </div>
                        </li>
                        <li>
                            <div class="edit-inline">
                                <label for="idNumber">ID Number :</label>
                                <input type="text" id="idNumber" value="{{$user->id_number}}">
                            </div>
                        </li>
                        <li>
                            <div class="edit-inline">
                                <label for="birthday">Birthday :</label>
                                <input type="text" id="birthday" value="{{$user->birthday}}">
                            </div>
                        </li>
                        <li>
                            <div class="edit-inline">
                                <label for="address">Address :</label>
                                <input type="text" id="address" value="{{$user->residential_address}}">
                            </div>
                        </li>
                        <li>
                            <div class="edit-inline">
                                <label for="region">Region :</label>
                                <input type="text" id="region" value="{{$user->region_ode}}">
                            </div>
                        </li>
                        <li>
                            <div class="edit-inline">
                                <label for="phone">Phone :</label>
                                <input type="text" id="phone" value="{{$user->phone}}">
                            </div>
                        </li>
                    </ul>
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
                url:"{{route('update_identity')}}",
                data:{
                    'id':$("#editId").html(),
                    'first_name':$("#firstName").val(),
                    'last_name':$("#lastName").val(),
                    'id_number':$("#idNumber").val(),
                    'birthday':$("#birthday").val(),
                    'address':$("#address").val(),
                    'region':$("#region").val(),
                    'phone':$("#phone").val(),
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
                            window.location.href = "{{env('ADMIN_DOMAIN').'/getUserCheck'}}";
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
            window.location.href = "{{env('ADMIN_DOMAIN').'/getUserCheck'}}";
        })
    })
</script>
@endsection
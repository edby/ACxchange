@extends('dashboard.layout.app')
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl"><a href="">Administrators</a><span>&nbsp;&gt;&nbsp;Edit Administrators</span></div>
        <div class="wrapper-content">
            <div class="edit-form">
                <form action="">
                    <ul>
                        <li>
                            <div class="edit-inline">
                                <label for=""><i>*</i>ID</label>
                                <input type="text" class="manager_id" value="{{$manager->id}}" disabled>
                                <span class="tips"></span>
                            </div>
                            <div class="edit-inline">
                                <label for=""><i>*</i>Name </label>
                                <input type="text" class="manager_name" value="{{$manager->name}}">
                                <span class="tips"></span>
                            </div>
                        </li>
                        <li>
                            <div class="edit-inline">
                                <label for=""><i>*</i>New Password (leave empty for no changes)</label>
                                <input type="text" class="manager_password" value="" placeholder="leave empty for no changes">
                                <span class="tips"></span>
                            </div>
                            <div class="edit-inline">
                                <label for=""><i>*</i>Admin Type </label>
                                <select class="manager_role">
                                    <option value="1" @if($manager->role == 1) selected @endif>Support</option>
                                    <option value="2" @if($manager->role == 2) selected @endif>Normal Administrator</option>
                                    <option value="3" @if($manager->role == 3) selected @endif>Full Administrator</option>
                                    <option value="4" @if($manager->role == 4) selected @endif>Customer Administrator</option>
                                </select>
                                <span class="tips"></span>
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
                url:"{{route('manager_action')}}",
                data:{
                    'id':$(".manager_id").val(),
                    'name':$(".manager_name").val(),
                    'password':$(".manager_password").val(),
                    'role':$(".manager_role").val()
                },
                dataType:'json',
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function (msg) {
                    if(msg.code == 200) {
                        layer.msg('update success', {
                            offset: 'auto',
                            anim: 0,
                            area:['420px']
                        });
                        setTimeout(function(){
                            window.location.href = "{{env('ADMIN_DOMAIN').'/administrators'}}";
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
            window.location.href = "{{env('ADMIN_DOMAIN').'/administrators'}}";
        })
    })
</script>
@endsection
@extends('dashboard.layout.app')
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl">Administrators</div>
        <div class="wrapper-content">
            <div class="search-wrapper">
                <div class="form-box select-box">
                    <form action="">
                        <input type="text" placeholder="New Name" class="ml20 createName">
                        <input type="text" placeholder="New Password" class="ml20 createPassword">
                        <select class="select-term createRole">
                            <option value="1">Support</option>
                            <option value="2">Normal Administrator</option>
                            <option value="3">Full Administrator</option>
                            <option value="4">Customer Administrator</option>
                        </select>
                        <button type="button" class="createAction">Create</button>
                    </form>
                </div>
            </div>
            <div class="list-client">
                <table class="user-table" id="change">
                    <thead>
                        <tr>
                            <th>Created Date</th>
                            <th>Name</th>
                            <th>Admin Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="pagination" class="pagination"></div>
</section>
<!-- model -->
<div class="modal fade" id="user-del" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="del-admin">
                <i class="iconfont icon-cuo close-btn" data-dismiss="modal"></i>
                <span><i class="iconfont icon-gantanhao"></i></span>
                <p>Do you want to delete it?</p>
                <div class="admin-btn-group">
                    <a href="javascript:;" class="affirm sureDel">Yes</a>
                    <a href="javascript:;" class="abolish" data-dismiss="modal">No</a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
<script>
    $(function() {
        // var filter = '';
        // $('#sear').on('click',function () {
        //     opt.onPageClicked('','',1,1);
        // });
        var opt = {
            currentPage: 1,
            totalPages: 1,
            numberOfPages:5,
            onPageClicked:function (event, originalEvent, type, newPage) {
                // var something = $('#search').val();
                // filter = something;
                $.ajax({
                    url:"{{route('manager_list')}}",
                    data:{'page':newPage},
                    dataType:'json',
                    type:'get',
                    success:function (msg) {
                        if(msg.code == 200) {
                            var str = '';
                            opt.totalPages = Math.ceil(msg[1]/10);
                            for(var i in msg[0]['data']) {
                                str += `
                                <tr trid="${msg[0]['data'][i]['id']}">
                                    <td>${msg[0]['data'][i]['created_at']}</td>
                                    <td>${msg[0]['data'][i]['name']}</td>
                                    <td>`;
                                switch (msg[0]['data'][i]['role']){
                                    case 1: var role = 'Support'; break;
                                    case 2: var role = 'Normal Administrator'; break;
                                    case 3: var role = 'Full Administrator'; break;
                                    case 4: var role = 'Customer Administrator'; break;
                                }
                                str += role;
                                str += `</td>
                                    <td>
                                        <a href="javascript:;" class="action-exit"><i class="iconfont icon-bianji"></i>EDIT</a>
                                        <a href="javascript:;" class="action-delete">DELETE</a>
                                    </td>
                                </tr>`
                            }
                            $('#change tbody').empty().append(str);
                            $('#totalPage').empty().html('Total ' + msg[1] + ' Clients')
                        }else {
                            layer.msg('Error Data', {
                                offset: 'auto',
                                anim: 6,
                                area:['420px']
                            });
                        }
                        if(type == 1){
                            $('#pagination').bootstrapPaginator(opt);
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
            }
        }
        opt.onPageClicked('','',1,1);
        $('#pagination').bootstrapPaginator(opt);

        $(".createAction").on('click',function () {
            var name = $(".createName").val();
            var password = $(".createPassword").val();
            var role = $(".createRole").val();
            if(!name){
                layer.msg('Name is required.', {
                    offset: 'auto',
                    anim: 6,
                    area:['420px']
                });
            }else if(!password){
                layer.msg('Password is required.', {
                    offset: 'auto',
                    anim: 6,
                    area:['420px']
                });
            }else{
                $.ajax({
                    url:"{{route('manager_action')}}",
                    data:{'name':name, 'password':password, 'role':role},
                    dataType:'json',
                    type:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function (msg) {
                        if(msg.code == 200) {
                            layer.msg('Create success', {
                                offset: 'auto',
                                anim: 0,
                                area:['420px']
                            });
                            setTimeout(function(){
                                window.location.href = "{{env('ADMIN_DOMAIN').'/administrators'}}";
                            },800)
                        }else {
                            layer.msg(msg.message, {
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
            }
        });
        $("#change").on('click',".action-exit",function(){
            var trid = $(this).parent().parent().attr('trid');
            window.location.href = "{{env('ADMIN_DOMAIN').'/editManager'}}?id="+trid;
        });

        $("#change").on('click',".action-delete",function(){
            var that = $(this);
            model('wrapper','fail','Are you sure to delete?',function() {
                $('#user-del').modal('show');//fa-edit
                $('.sureDel').on('click',function(){
                    $.ajax({
                        url:"{{route('manager_action')}}",
                        data:{'id':that.parent().parent().attr('trid')},
                        dataType:'json',
                        type:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function (msg) {
                            if(msg.code == 200) {
                                that.parent().parent().remove();
                                $('#user-del').modal('hide');
                                layer.msg('Delete success', {
                                    offset: 'auto',
                                    anim: 0,
                                    area:['420px']
                                });
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
            });
        });
    });
</script>
@endsection

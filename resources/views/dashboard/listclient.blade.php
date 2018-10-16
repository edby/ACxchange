@extends('dashboard.layout.app')
@section('style')
    <link href="{{asset('dashboard/plugins/calendar/calendar.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('dashboard/js/toastr.min.css')}}">
@endsection
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl">List Clients</div>
        <div class="wrapper-content">
            <div class="search-wrapper">
                <div class="form-contain">
                    <!-- 日历 开始时间 -->
                    <div class="form-history calendar_box" style="margin-right:0;">
                        <input type="text" class="thedata-start" name="" value="" placeholder="Pick a date " readonly />
                        <i class="iconfont icon-rili riliimg"></i>
                        <div class="thecalendar-start"></div>
                    </div>
                    <div class="heng" style="width:25px; text-align: center">--</div>
                    <!-- 日历 结束时间 -->
                    <div class="form-history calendar_box">
                        <input type="text" class="thedata-end" name="" value="" placeholder="TO date" readonly />
                        <i class="iconfont icon-rili riliimg"></i>
                        <div class="thecalendar-end"></div>
                    </div>
                </div>
                <span class="total totalPage">Total 0 Clients</span>
            </div>
            <div class="search-wrapper">
                <div class="form-box">
                    <form action="" onsubmit="return false;">
                        <input type="text" id="search" placeholder="(id) name or email">
                        <button type="button" id="sear"><i class="iconfont icon-icon--"></i>Search</button>
                        <a onclick="location.reload()"><i class="iconfont icon-xunhuan101"></i>Refresh</a>
                        <a class="user_export">Export</a>
                    </form>
                </div>
            </div>
            <div class="list-client client2">
                <table class="list-client-table" id="change">
                    <thead>
                        <tr>
                            <th>Date Joined</th>
                            <th>User ID</th>
                            <th>Bind</th>
                            <th>Actual name</th>
                            <th>E-mail</th>
                            <th>Last login IP</th>
                            <th>2FA Default</th>
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
    <div class="loading" style="display: none;">
        <img class="img-gif" src="{{asset('dashboard/dist/img/Loading.gif')}}">
    </div>
</section>
<script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
<script src="{{asset('dashboard/plugins/calendar/calendar.js')}}"></script>
<script src="{{asset('dashboard/js/toastr.min.js')}}"></script>
<script src="{{asset('dashboard/js/toastr_conf.js')}}"></script>
<script>
    $(function() {
        var filter = '';
        $('#sear').on('click',function () {
            opt.onPageClicked('','',1,1);
        });
        $("#search").keydown(function(e) {
            if (e.keyCode == 13) {//Enter键调用
                opt.onPageClicked('','',1,1);
            }
        });

        var opt = {
            currentPage: 1,
            totalPages: 1,
            numberOfPages:5,
            onPageClicked:function (event, originalEvent, type, newPage) {
                var something = $('#search').val();
                var t_start = $(".thedata-start").val();
                var t_end = $(".thedata-end").val();
                filter = [something,t_start,t_end];
                $.ajax({
                    url:"{{route('client_list')}}",
                    data:{'page':newPage,'something':something,'t_start':t_start,'t_end':t_end},
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
                                            <td>${msg[0]['data'][i]['id']}</td>`;
                                if(msg[0]['data'][i]['bind_user_id']){
                                    str += `<td>${msg[0]['data'][i]['bind_user_id']}: &nbsp;${msg[0]['data'][i]['bind_info']}</td>`;
                                }else{
                                    str += `<td>--</td>`;
                                }
                                str +=     `<td>${msg[0]['data'][i]['name']}</td>
                                            <td>${msg[0]['data'][i]['email']}</td>
                                            <td>${msg[0]['data'][i]['login_ip']}</td>
                                            <td>`;
                                switch (msg[0]['data'][i]['auth_type']){
                                    case 0: var fa = 'Not yet'; break;
                                    case 2: var fa = 'AUTHY'; break;
                                    case 3: var fa = 'SMS'; break;
                                    case 1: var fa = 'Google'; break;
                                }
                                str += fa;
                                if(fa !== 'Not yet'){
                                    str += `<button class="btn btn-default reset">reset</button>`;
                                }
                                str += `</td>
                                            <td>`;
                                if(msg[0]['data'][i]['name'].substr(msg[0]['data'][i]['name'].length - 8) === 'disabled'){
                                    str += `<a class="btn btn-default disable" value="2" style="background: #f7ba2a; color: #FFF; opacity: 0.4;">reabled</a>`;
                                }else{
                                    str += `<button class="btn btn-default disable" value="1" style="background: #f7ba2a; color: #FFF">disabled</button>`;
                                }
                                str += `    <a class="action-exit edit">
                                                <i class="iconfont icon-bianji"></i>EDIT</a>
                                            <a class="login_as action-login">LOGIN AS</a>`;
                                if(!msg[0]['data'][i]['register_confirm'] && msg[0]['data'][i]['name'].substr(msg[0]['data'][i]['name'].length - 8) !== 'disabled')
                                    str += `<button class="btn btn-default email_resend"  style="background: #f76c2f; color: #FFF">Resend</button>`;
                                str += `</td></tr>`;

                            }
                            $('#change tbody').empty().append(str);
                            $('.totalPage').empty().html('Total ' + msg[1] + ' Clients')
                            if(msg[1] === 0){
                                $('#pagination').empty();
                            }
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

        $("#change").on('click',".edit",function(){
            var trid = $(this).parent().parent().attr('trid');
            window.location.href = "{{env('ADMIN_DOMAIN').'/editUser'}}?id="+trid;
        });
        $(".user_export").on('click',function () {
            window.location.href = ('{{env('ADMIN_DOMAIN').'/getUserList'}}?something='+filter[0]+'&t_start='+filter[1]+'&t_end='+filter[2]+'&export=client');
            toastr.success('Export now');
        });
        $("#change").on('click',".reset",function(){
            var that = $(this);
            model('wrapper','fail','Are you sure to reset?',function() {
                $('.fail_modal').modal('show');//fa-edit
                $('.delete-click').on('click',function() {
                    var str = 'Not yet';
                    var trid = that.parent().parent();
                    that.parent().empty().append(str);
                    $.ajax({
                        url:"{{route('reset_2FA')}}",
                        data:{'id':trid.attr('trid')},
                        dataType:'json',
                        type:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function (msg) {
                            if(msg.code == 200) {
                                layer.msg('Success', {
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
        $("#change").on('click',".disable",function () {
            var trid = $(this).parent().parent().attr('trid');
            var type = $(this).attr('value');
            var str = $(this).html();
            model('wrapper','fail','Are you sure to '+str+' ?',function() {
                $('.fail_modal').modal('show');//fa-edit
                $('.delete-click').on('click', function () {
                    $.ajax({
                        url:"{{route('disable_user')}}",
                        data:{'id':trid, 'type':type},
                        dataType:'json',
                        type:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function (msg) {
                            if(msg.code == 200) {
                                layer.msg('Done', {
                                    offset: 'auto',
                                    anim: 0,
                                    area:['420px']
                                });
                                location.reload();
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
        $("#change").on('click',".login_as",function () {
            var trid = $(this).parent().parent().attr('trid');
            $.ajax({
                url:"{{route('login_as')}}",
                data:{'id':trid},
                dataType:'json',
                type:'get',
                success:function (msg) {
                    if(msg.code == 200) {
                        layer.msg('Success', {
                            offset: 'auto',
                            anim: 0,
                            area:['420px']
                        });
                        window.open("{{env('APP_URL').'/loginAs'}}?id="+trid+"&token="+msg.token);
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
        $("#change").on('click',".email_resend",function(){
            var trid = $(this).parent().parent().attr('trid');
            $(".loading").show();
            $.ajax({
                url:"{{route('email_resend')}}",
                data:{'id':trid},
                dataType:'json',
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function (msg) {
                    $(".loading").hide();
                    if(msg.code === 200) {
                        layer.msg('Success', {
                            offset: 'auto',
                            anim: 0,
                            area:['420px']
                        });
                    }
                    else {
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



        //日历-开始日期
        $('.thecalendar-start').calendar({
            trigger: '.thedata-start',
            zIndex: 999,
            format: 'yyyy-mm-dd',
            onSelected: function (view, date, data) {
                console.log('event: onSelected')
            },
            onClose: function (view, date, data) {
                console.log('event: onClose')
                console.log('data:' + (data || 'None'));
            }
        });
        //日历-结束日期
        $('.thecalendar-end').calendar({
            trigger: '.thedata-end',
            zIndex: 999,
            format: 'yyyy-mm-dd',
            onSelected: function (view, date, data) {
                console.log('event: onSelected')
            },
            onClose: function (view, date, data) {
                console.log('event: onClose')
                console.log('data:' + (data || 'None'));
            }
        });
    })
</script>
@endsection
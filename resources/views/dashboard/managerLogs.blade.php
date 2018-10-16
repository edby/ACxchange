@extends('dashboard.layout.app')
@section('style')
    <link href="{{asset('dashboard/plugins/calendar/calendar.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('dashboard/js/toastr.min.css')}}">
@endsection
@section('content')
    <section class="content managerLogs">
        <div class="dashboard">
            <div class="titl">Manager Logs</div>
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
                    <span class="total totalPage">Total 0 Log</span>
                </div>
                <div class="search-wrapper">
                    <div class="form-box">
                        <form action="" onsubmit="return false;">
                            <input type="text" id="search" placeholder="(id) name or email">
                            <button type="button" id="sear"><i class="iconfont icon-icon--"></i>Search</button>
                            <a onclick="location.reload()"><i class="iconfont icon-xunhuan101"></i>Refresh</a>
                            {{--<a class="user_export">Export</a>--}}
                        </form>
                    </div>
                </div>
                <div class="list-client client2">
                    <table class="list-client-table" id="change">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Manager</th>
                            <th>IP</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="pagination" class="pagination"></div>
    </section>
    <script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('dashboard/plugins/calendar/calendar.js')}}"></script>
    <script src="{{asset('dashboard/js/toastr.min.js')}}"></script>
    <script src="{{asset('dashboard/js/toastr_conf.js')}}"></script>
    <script>
        $(function() {
            var filter = '';
            $.ajax({
                url:"{{route('manager_info')}}",
                dataType:'json',
                type:'get',
                success:function (msg) {
                    if(msg.code == 200) {
                        if(msg[0].role < 2){
                            window.location.href = "{{env('ADMIN_DOMAIN').'/PermissionDenied'}}"
                        }
                    }
                }
            });
            $('#sear').on('click',function () {
                opt.onPageClicked('','',1,1);
            });
            var opt = {
                currentPage: 1,
                totalPages: 1,
                numberOfPages:5,
                onPageClicked:function (event, originalEvent, type, newPage) {
                    var action = $('#search').val();
                    var t_start = $('.thedata-start').val();
                    var t_end = $('.thedata-end').val();
                    filter = [action,t_start,t_end];
                    $.ajax({
                        url:"{{route('manager_log')}}",
                        data:{'page':newPage,'action':action,'t_start':t_start,'t_end':t_end},
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
                                            <td>`;
                                    switch (msg[0]['data'][i]['type']){
                                        case 1: str += `-`;break;
                                        case 2: str += `Move`;break;
                                        case 3: str += `Withdrawal`;break;
                                        case 4: str += `Airdrop`;break;
                                        case 5: str += `Move Bug`;break;
                                    }
                                    str += `</td>
                                            <td>${msg[0]['data'][i]['author_name']}</td>
                                            <td>${msg[0]['data'][i]['ip_address']}</td>
                                            <td>${msg[0]['data'][i]['action']}</td>
                                         </tr>`;
                                }
                                $('#change tbody').empty().append(str);
                                $('#totalPage').empty().html('Total ' + msg[1] + ' Logs')
                                if(msg[1] === 0){
                                    $('#pagination').empty();
                                }
                            }else {
                                layer.msg('Data Error', {
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
                            layer.msg('Failure', {
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
            $(".log_export").on('click',function () {
                window.location.href = ('{{env('ADMIN_DOMAIN').'/managerLog'}}?action='+filter[0]+'&t_start='+filter[1]+'&t_end='+filter[2]+'&export=log');
                prompt('wrapper','success','Export now.');
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
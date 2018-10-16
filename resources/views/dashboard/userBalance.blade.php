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
                            <input type="text" id="search" placeholder="id or email">
                            <button type="button" id="sear"><i class="iconfont icon-icon--"></i>Search</button>
                            <a onclick="location.reload()"><i class="iconfont icon-xunhuan101"></i>Refresh</a>
                            <a class="user_export">Export</a>
                        </form>
                    </div>
                </div>
                <div class="list-client balance2">
                    <table class="list-balance-table" id="change">
                        <thead>
                        <tr>
                            <th>Date Joined</th>
                            <th>User ID</th>
                            <th>E-mail</th>
                            <th>BTC</th>
                            <th>LTC</th>
                            <th>BCH</th>
                            <th>RPZ</th>
                            <th>XVG</th>
                            <th>BTG</th>
                            <th>DASH</th>
                            <th>Refresh</th>
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
                        url:"{{route('client_balance')}}",
                        data:{'page':newPage,'something':something,'t_start':t_start,'t_end':t_end},
                        dataType:'json',
                        type:'get',
                        success:function (msg) {
                            if(msg.code == 200) {
                                var str = '';
                                opt.totalPages = Math.ceil(msg[1]/10);
                                for(var i in msg[0]) {
                                    str += `
                                        <tr trid="${msg[0][i]['id']}">
                                            <td>${msg[0][i]['created_at']}</td>
                                            <td>${msg[0][i]['id']}</td>
                                            <td>${msg[0][i]['email']}</td>
                                            <td>${msg[0][i]['BTC']}</td>
                                            <td>${msg[0][i]['LTC']}</td>
                                            <td>${msg[0][i]['BCH']}</td>
                                            <td>${msg[0][i]['RPZ']}</td>
                                            <td>${msg[0][i]['XVG']}</td>
                                            <td>${msg[0][i]['BTG']}</td>
                                            <td>${msg[0][i]['DASH']}</td>
                                            <td>
                                                <button class="btn refresh_balance" style="background:#0f91ff; color: #FFF; width: 70px; height:28px; line-height:28px;padding:0; border-radius: 0;"><i class="fa fa-refresh"></i></button>
                                            </td>
                                        </tr>`;
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
            $(".user_export").on('click',function () {
                window.location.href = ('{{env('ADMIN_DOMAIN').'/userBalance'}}?something='+filter[0]+'&t_start='+filter[1]+'&t_end='+filter[2]+'&export=balance');
                toastr.success('Export now');
            });
            $("#change").on('click',".refresh_balance",function () {
                var trid = $(this).parent().parent();
                $.ajax({
                    url:"{{route('refresh_balance')}}",
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
                            var balance = ` <td>${msg[0][i]['created_at']}</td>
                                            <td>${msg[0][i]['id']}</td>
                                            <td>${msg[0][i]['email']}</td>
                                            <td>${msg[0][i]['BTC']}</td>
                                            <td>${msg[0][i]['LTC']}</td>
                                            <td>${msg[0][i]['BCH']}</td>
                                            <td>${msg[0][i]['RPZ']}</td>
                                            <td>${msg[0][i]['XVG']}</td>
                                            <td>${msg[0][i]['BTG']}</td>
                                            <td>${msg[0][i]['DASH']}</td>
                                            <td style="width: 30px">
                                                <button class="btn refresh_balance" style="background: #f7ba2a; color: #FFF; opacity: 0.8; width: 38px"><i class="fa fa-refresh"></i></button>
                                            </td>`;
                            trid.empty().append(balance);
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
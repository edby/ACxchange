@extends('dashboard.layout.app')
@section('style')
    <link rel="stylesheet" href="{{asset('dashboard/dist/css/jquery-ui.min.css')}}">
    <link href="{{asset('dashboard/plugins/calendar/calendar.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('dashboard/js/toastr.min.css')}}">
@endsection
@section('content')
    <section class="content" id="WithdrawTerm">
        <div class="dashboard">
            <div class="titl">Clients Withdraw Term</div>
            <div class="wrapper-content">
                <div class="search-wrapper">
                    <div class="form-contain">
                        <!-- 日历 开始时间 -->
                        <div class="form-history calendar_box" style="margin-right:0;">
                            <input type="text" class="thedata-start2" name="" value="" placeholder="Pick a date " readonly />
                            <i class="iconfont icon-rili riliimg"></i>
                            <div class="thecalendar-start2"></div>
                        </div>
                        <div class="heng" style="width:25px; text-align: center">--</div>
                        <!-- 日历 结束时间 -->
                        <div class="form-history calendar_box">
                            <input type="text" class="thedata-end2" name="" value="" placeholder="TO date" readonly />
                            <i class="iconfont icon-rili riliimg"></i>
                            <div class="thecalendar-end2"></div>
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
                            <th>User ID</th>
                            <th>E-mail</th>
                            <th>Nationality</th>
                            <th>Limit</th>
                            <th>Term</th>
                            <th>Action</th>
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
    {{--<script src="{{asset('dashboard/dist/js/jquery-ui.min.js')}}" type="text/javascript"></script>--}}
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
                    var t_start = $(".thedata-start2").val();
                    var t_end = $(".thedata-end2").val();
                    filter = [something,t_start,t_end];
                    $.ajax({
                        url:"{{route('term_withdraw')}}",
                        data:{'page':newPage,'something':something,'t_start':t_start,'t_end':t_end},
                        dataType:'json',
                        type:'get',
                        success:function (msg) {
                            if(msg.code == 200) {
                                var str = '';
                                opt.totalPages = Math.ceil(msg[1]/10);

                                for(var i in msg[0].data) {

                                    var limit=parseInt(msg[0].data[i]['btc_balance']);
                                    var left=parseInt(limit*15)+'px';

                                    str += `
                                        <tr trid="${msg[0].data[i]['user_id']}"`;
                                    if(msg[0].data[i]['start_interval'] !== '-')
                                        str += ` style="opacity: 0.5"`;
                                    str += `>
                                            <td>${msg[0].data[i]['user_id']}</td>
                                            <td>${msg[0].data[i]['email']}</td>
                                            <td>${msg[0].data[i]['en_country']}</td>
                                            <td>

                                            <div class="progress2">
                                            <div class="progress_bg">
                                            <div class="progress_bar" style="width:`+left+`"></div>
                                            </div>
                                            <div class="progress_btn" style="left:`+left+`"></div>
                                            <div class="text limit">`+limit+`</div>
                                            </div>


                                            </td>
                                            <td>
                                                <div class="form-contain">
                                                    <div class="form-history calendar_box" style="margin-right:0;">
                                                        <input type="text" class="thedata-start" name="" value="${msg[0].data[i]['start_interval']}" placeholder="Pick a date " readonly />
                                                        <i class="iconfont icon-rili riliimg"></i>
                                                        <div class="thecalendar-start"></div>
                                                        </div>
                                                        <div class="heng" style="width:25px; text-align: center">--</div>
                                                        <div class="form-history calendar_box">
                                                        <input type="text" class="thedata-end" name="" value="${msg[0].data[i]['end_interval']}" placeholder="TO date" readonly />
                                                        <i class="iconfont icon-rili riliimg"></i>
                                                        <div class="thecalendar-end"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="update action-login">Update</a>
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
                window.location.href = ('{{env('ADMIN_DOMAIN').'/userWithdrawTerm'}}?something='+filter[0]+'&t_start='+filter[1]+'&t_end='+filter[2]+'&export=withdrawTerm');
                toastr.success('Export now');
            });


            $("#change").on('click',".update",function () {
                var trid = $(this).parent().parent();
                var term_amount = $(this).parent().parent().find('.limit').html();
                var start_interval = $(this).parent().parent().find('.thedata-start').val();
                var end_interval = $(this).parent().parent().find('.thedata-end').val();
                $.ajax({
                    url:"{{route('update_term_withdraw')}}",
                    data:{'id':trid.attr('trid'),'term_amount':term_amount,'start_interval':start_interval,'end_interval':end_interval},
                    dataType:'json',
                    type:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function (msg) {
                        if(msg.code == 200) {
                            trid.css('opacity',0.5);
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
//
//           // 日历-开始日期
            $('.dashboard .thecalendar-start2').calendar({
                trigger: '.thedata-start2',
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
            $('.dashboard .thecalendar-end2').calendar({
                trigger: '.thedata-end2',
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
    <script>
//        进度条


      $(function(){



            var tag = false,ox = 0,left = 0,bgleft = 0,max=20,min=2;

            $('#change').on('mousedown','.progress_btn',function(e) {
                left=parseInt($(this).css('left'));//获取当前进度条按钮的开始位置
                ox = e.pageX - left;
                tag = true;

            });
            $(document).mouseup(function() {
                left=0;
                tag = false;
            });

            $('#change').on('mousemove','.progress2',function(e) {
                if (tag) {
                    left = e.pageX - ox;
                    if (left <= 30) {
                        left = 30;
                    }else if (left > 300) {
                        left = 300;
                    }
                    $(this).find('.progress_btn').css('left', left);

                    $(this).find('.progress_bar').width(left);

                    $(this).find('.text').html(parseInt(left/15)>min?parseInt(left/15):min);
                   // 最大值为20,最小值为2

                }
            });

            $('#change').on('click','.progress_bg',function(e) {
                if (!tag) {
                    bgleft = $(this).offset().left;
                    left = e.pageX - bgleft;
                    if (left <= 30) {
                        left = 30;
                    }else if (left > 300) {
                        left = 300;
                    }
                    $(this).next('.progress_btn').css('left', left);
                    $(this).find('.progress_bar').animate({width:left},300);
                    $(this).siblings('.text').html(parseInt(left/15)>min?parseInt(left/15):min);

                }

            });



      })



$(function(){


//    表格内的日期委托

    $('#change').on('click','.calendar_box', function() {
        // console.log('99999')
        var start=$(this).find('.thecalendar-start');
        var end=$(this).find('.thecalendar-end');
        // console.log(start);
        // console.log(end);

        var blockStart=$(this).find('.thedata-start');
        var blockEnd=$(this).find('.thedata-end');
        //日历-开始日期
        start.calendar({
            trigger: blockStart,//日历显示隐藏
            zIndex: 999,
            format: 'yyyy-mm-dd',
            onSelected: function (view, date, data) {
                // console.log('event: onSelected')
            },
            onClose: function (view, date, data) {
                // console.log('event: onClose')
                // console.log('data:' + (data || 'None'));
            }
        });

        //日历-结束日期
            end.calendar({
                trigger:blockEnd,
                zIndex: 999,
                format: 'yyyy-mm-dd',
                onSelected: function (view, date, data) {
                    // console.log('event: onSelected')
                },
                onClose: function (view, date, data) {
                    // console.log('event: onClose')
                    // console.log('data:' + (data || 'None'));
                }
            });


    });



})






    </script>

@endsection
@extends('dashboard.layout.app')
@section('style')
    <link href="{{ asset('dashboard/plugins/calendar/calendar.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="content">
        <div class="wallet_history">
            <h3>Pending</h3>
            <!-- 内容顶部 -->
            <div class="currency_box">
                        <span>
                            <i class="iconfont icon-jiufuqianbaoicon14"></i>
                            Choice of currency
                        </span>
                <div class="choosecurrency_box">
                    <input type="text" id="choosecurrency" name="Bitcoin(BTC)" value="Bitcoin(BTC)" readonly /><i class="toDown_btn iconfont icon-ln_jiantouxia"></i>
                    <ul class="currency_list">
                        <li value="1">Bitcoin(BTC)</li>
                        <li value="3">Litecoin(LTC)</li>
                        <li value="2">Bitcoin Cash(BCH)</li>
                        <li value="4">Rapidz(RPZ)</li>
                        <li value="6">Verge(XVG)</li>
                        <li value="7">Bitcoin Gold(BTG)</li>
                        <li value="8">Dash(DASH)</li>
                    </ul>
                </div>
            </div>
            <div class="currency_box historyserch">
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
                    <div class="newbox" style="margin: 0 15px">
                        <span>UserID</span>
                        <input type="text" class="form-control" id="search" style="width: 150px" placeholder="id or name">
                    </div>
                    <div class="btn-box box2 newbtnbox">
                        <button class="history-btn updatabtn" id="sear">Search</button>
                        <button style="background: #e41e1e;" class="btn newbtn audit_export">
                            Export
                        </button>
                    </div>
                </div>
                <div class="history_total">Total 0 Withdrawals</div>
            </div>
            <!-- 表格 -->
            <div class="pendingTable">
                <!-- 表格1 -->
                <div class="table2 thePendingTable-box ">
                    <table class="pending_userInfoTable" id="change">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Date</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Nationality</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>ToAddress</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div id="pagination" class="pagination"></div>
            </div>
        </div>

    </section>
    <script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
    <script src='{{ asset('dashboard/plugins/calendar/calendar.js')}}'></script>

    <script>
        $(function () {
            var filter = [];
            $('#sear').on('click', function () {
                opt.onPageClicked('', '', 1, 1);
            });
            var opt = {
                currentPage: 1,
                totalPages: 1,
                numberOfPages: 5,
                onPageClicked: function (event, originalEvent, type, newPage) {
                    var t_start = $(".thedata-start").val();
                    var t_end = $(".thedata-end").val();
                    var user_id = $('#search').val();
                    var currency = $('#choosecurrency').attr('currency');
                    if(currency == undefined) currency = 1;
                    filter = [t_start,t_end,currency,user_id];
                    $.ajax({
                        url: "{{route('withdrawal_check','approve')}}",
                        data: {'t_start': t_start, 't_end': t_end,
                            'page': newPage, 'currency_id': currency,
                            'user_id':user_id},
                        dataType: 'json',
                        type: 'get',
                        success: function (msg) {
                            if (msg.code == 200) {
                                var str = '';
                                opt.totalPages = Math.ceil(msg[1] / 10);
                                for (var i in msg[0]['data']) {
                                    str += `<tr trid="${msg[0]['data'][i]['id']}" user_id="${msg[0]['data'][i]['user_id']}" amount="${msg[0]['data'][i]['amount']} ${msg[0]['data'][i]['currency']}">
                                                <td></td>
                                                <td>${msg[0]['data'][i]['created_at']}</td>
                                                <td>${msg[0]['data'][i]['user_id']}</td>
                                                <td>${msg[0]['data'][i]['name']}</td>
                                                <td>${msg[0]['data'][i]['nationality']}</td>
                                                <td>${msg[0]['data'][i]['currency']} ${msg[0]['data'][i]['amount']}</td>
                                                <td>${msg[0]['data'][i]['currency']} ${msg[0]['data'][i]['max_fee']}</td>
                                                <td>${msg[0]['data'][i]['address']}</td>
                                                <td style="color: #00b92c;">Approve</td>
                                            </tr>`;
                                }
                                $('#change tbody').empty().append(str);
                                $('.history_total').empty().html('Total ' + msg[1] + ' Withdrawals');
                                if (msg[1] === 0) {
                                    $('#pagination').empty();
                                }
                            } else {
                                layer.msg('Data Error', {
                                    offset: 'auto',
                                    anim: 6,
                                    area:['420px']
                                });
                            }
                            if (type == 1) {
                                $('#pagination').bootstrapPaginator(opt);
                            }
                        },
                        error: function () {
                            layer.msg('Error', {
                                offset: 'auto',
                                anim: 6,
                                area:['420px']
                            });
                        }
                    });
                }
            }
            opt.onPageClicked('', '', 1, 1);
            $('#pagination').bootstrapPaginator(opt);
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
            $(".audit_export").on('click',function () {
                window.location.href = ('{{env('ADMIN_DOMAIN').'/withdrawal/approve'}}?&t_start='+filter[0]+'&t_end='+filter[1]+'&currency_id='+filter[2]+'&user_id='+filter[3]+'&export=withdraw');
                layer.msg('success', {offset: 'auto', anim: 0, area:['420px']});
            });
            $(function () {
                //币种选择框动态效果1
                $(".choosecurrency_box .toDown_btn").click(function (e) {
                    e.stopPropagation();
                    $(".choosecurrency_box .currency_list").slideToggle();

                })
                $(".choosecurrency_box .currency_list>li").click(function () {
                    $("#choosecurrency").attr('currency',$(this).val());
                    $("#choosecurrency").val($(this).html());
                    $(".choosecurrency_box .currency_list").slideUp();
                })
                $(window).click(function () {
                    //收起币种选择框
                    $(".choosecurrency_box .currency_list").slideUp();

                })
            })
        })
    </script>
@endsection

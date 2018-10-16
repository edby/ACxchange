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
                    <div class="approve_select">

                        <div class="choosecurrency_box">
                            <input type="text" id="update_selection" op="0" name="Approve" value="--" readonly /><i class="toDown_btn2 iconfont icon-ln_jiantouxia"></i>
                            <ul class="update_list">
                                <li value="1">Approve</li>
                                <li value="3">Reject</li>
                            </ul>
                        </div>
                        <div class="btn-box newbtnbox">
                            <button class="history-btn updatabtn all-update">Update Selection</button>

                            <button style="background: #e41e1e;" class="btn newbtn audit_export">
                                Export
                            </button>
                            <button style="background: #00b92c;" class="btn newbtn log_export">
                                Log Export
                            </button>
                        </div>

                    </div>

                </div>
                <div class="history_total">Total 0 Withdrawals</div>
            </div>

            <div class="currency_box historyserch">
                <div class="newflex">
                    <div class="newbox">
                        <span>UserID</span>
                        <input type="text" class="form-control" id="search" placeholder="id or name">
                    </div>
                    <div class="form-contain" >
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
                </div>

                <div class="btn-box box2">
                    <button class="history-btn updatabtn" id="sear">Search</button>
                </div>

            </div>
            <!-- 表格 -->
            <div class="pendingTable">
                <!-- 表格1 -->
                <div class="table2 thePendingTable-box ">
                    <table class="pending_userInfoTable" id="change">
                        <thead>
                        <tr>
                            <th>
                                <div class="choice">
                                    <label class="checkbox allSelect">
                                        <input type="checkbox" name="email-confirmed" value="1">
                                        <i></i>
                                    </label>
                                </div>
                            </th>
                            <th>Date</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Nationality</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>ToAddress</th>
                            <th>Status</th>
                            <th>Bind</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div id="pagination" class="pagination"></div>
                <!-- 表格2 -->
                <div class="table2 thePendingTable-box">
                    <table class="pending_tradeTable" id="change2">
                        <thead>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Manager</th>
                        <th>IP</th>
                        <th>Action</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div id="pagination1" class="pagination"></div>
            </div>
        </div>

    </section>
    <div class="loading" style="display: none;">
        <img class="img-gif" src="{{asset('dashboard/dist/img/Loading.gif')}}">
    </div>

    <script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
    <script src='{{ asset('dashboard/plugins/calendar/calendar.js')}}'></script>

    <script>
        $(function () {
            $('.allSelect').on('click', function (e) {
                e.stopPropagation();
                $('.pending_userInfoTable tbody input[type=checkbox]').prop('checked', $(this).find('input').prop('checked'))
            });
            $('#change').on('click', '.approved_btn', function () {
                var tr = $(this).parent().parent();
                action(tr, 1, 'Approve', 0);
            });
            $('#change').on('click', '.rejected_btn', function () {
                var tr = $(this).parent().parent();
                action(tr, 3, 'Reject', 0);
            });
            $('.all-update').on('click', function () {

                var ac = $('#update_selection').attr('op');
                switch (ac) {
                    case '0':
                        layer.msg('Please select action.', {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                        return;
                    case '1':
                        var m = 'Approve';
                        break;
                    case '3':
                        var m = 'Reject';
                        break;
                }
                var id_list = [];
                $('.pending_userInfoTable tbody input[type=checkbox]').each(function (index, item) {
                    if ($(item).prop('checked') == true) {
                        id_list.push($(item).parent().parent().parent().parent().attr('trid'))
                    }
                });
                action(id_list, ac, m, 1);
            });

            function action(tr, status, m, all) {
                if (all == 1) {
                    var id_list = tr;
                    var str = m + ' these choices ?';
                } else {
                    var user_id = tr.attr('user_id');
                    var amount = tr.attr('amount');
                    var id_list = [tr.attr('trid')];
                    var str = m + ' User ' + user_id + ' amount ' + amount + ' ?';
                }
                model('wrapper', 'fail', str, function () {
                    $('.fail_modal').modal('show');//fa-edit
                    $('.delete-click').on('click', function () {
                        $('.loading').show();
                        $.ajax({
                            url:"{{route('withdraw_action')}}",
                            data:{'id_list':id_list, 'status':status},
                            dataType:'json',
                            type:'post',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (msg) {
                                if (msg.code == 200) {
                                    $('.loading').hide();
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 800);
                                } else if (msg.code == 403 || msg.code == 400) {
                                    layer.msg(msg.msg, {
                                        offset: 'auto',
                                        anim: 6,
                                        area:['420px']
                                    });
                                } else {
                                    layer.msg('Data Error', {
                                        offset: 'auto',
                                        anim: 6,
                                        area:['420px']
                                    });
                                }
                            },
                            error: function () {
                                console.log($('meta[name="csrf-token"]').attr('content'));
                                layer.msg('Error', {
                                    offset: 'auto',
                                    anim: 6,
                                    area:['420px']
                                });
                            }
                        });
                    });
                });
            }

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
                        url: "{{route('withdrawal_check','pending')}}",
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
                                                <td>
                                                    <div class="choice">
                                                        <label class="checkbox">
                                                            <input type="checkbox" name="email-confirmed" value="1">
                                                                <i></i>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>${msg[0]['data'][i]['created_at']}</td>
                                                <td>${msg[0]['data'][i]['user_id']}</td>
                                                <td>${msg[0]['data'][i]['name']}</td>
                                                <td>${msg[0]['data'][i]['nationality']}</td>
                                                <td>${msg[0]['data'][i]['currency']} ${msg[0]['data'][i]['amount']}</td>
                                                <td>${msg[0]['data'][i]['currency']} ${msg[0]['data'][i]['max_fee']}</td>
                                                <td>${msg[0]['data'][i]['address']}</td>
                                                <td style="color: #999999;">Pending</td>`;
                                    if(msg[0]['data'][i]['bind_user_id']){
                                        str += `<td>${msg[0]['data'][i]['bind_user_id']}</td>`;
                                    }else{
                                        str += `<td>--</td>`;
                                    }
                                    str += `    <td>
                                                    <button class="approved_btn">Approve</button>
                                                    <button class="rejected_btn">Reject</button>
                                                </td>
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
            $(".audit_export").on('click',function () {
                window.location.href = ('{{env('ADMIN_DOMAIN').'/withdrawal/pending'}}?&t_start='+filter[0]+'&t_end='+filter[1]+'&currency_id='+filter[2]+'&user_id='+filter[3]+'&export=withdraw');
                layer.msg('success', {offset: 'auto', anim: 0, area:['420px']});
            });
//------------------------ Log -------------------------------------------------
            var log = {
                currentPage: 1,
                totalPages: 1,
                numberOfPages: 5,
                onPageClicked: function (event, originalEvent, type, newPage) {
                    // var something = $('#search').val();
                    // filter = something;
                    $.ajax({
                        url: "{{route('manager_log')}}",
                        data: {'page': newPage, 'type': 3},
                        dataType: 'json',
                        type: 'get',
                        success: function (msg) {
                            if (msg.code == 200) {
                                var str = '';
                                log.totalPages = Math.ceil(msg[1] / 10);
                                for (var i in msg[0]['data']) {
                                    str += `
                                        <tr trid="${msg[0]['data'][i]['id']}">
                                            <td>${msg[0]['data'][i]['created_at']}</td>
                                            <td>`;
                                    if (msg[0]['data'][i]['type'] === 3) {
                                        str += `Withdrawal`;
                                    }
                                    str += `</td>
                                            <td>${msg[0]['data'][i]['author_name']}</td>
                                            <td>${msg[0]['data'][i]['ip_address']}</td>
                                            <td>${msg[0]['data'][i]['action']}</td>
                                         </tr>`;
                                }
                                $('#change2 tbody').empty().append(str);
                                // $('#totalPage').empty().html('Total ' + msg[1] + ' Logs')
                                if (msg[1] === 0) {
                                    $('#pagination1').empty();
                                }
                            } else {
                                layer.msg('Data Error', {
                                    offset: 'auto',
                                    anim: 6,
                                    area:['420px']
                                });
                            }
                            if (type == 1) {
                                $('#pagination1').bootstrapPaginator(log);
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
            log.onPageClicked('', '', 1, 1);
            $('#pagination1').bootstrapPaginator(log);
            $(".log_export").on('click',function () {
                window.location.href = ('{{env('ADMIN_DOMAIN').'/managerLog'}}?type=3&export=log');
                layer.msg('success', {offset: 'auto', anim: 0, area:['420px']});
            });
//----------Logend-----------------------------------------
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
                //币种选择框动态效果2
                $(".choosecurrency_box .toDown_btn2").click(function (e) {
                    e.stopPropagation();
                    $(".choosecurrency_box .update_list").slideToggle();

                })
                $(".choosecurrency_box .update_list>li").click(function () {
                    $("#update_selection").attr('op',$(this).val());
                    $("#update_selection").val($(this).html());
                    $(".choosecurrency_box .update_list").slideUp();
                })

                $(window).click(function () {
                    //收起币种选择框
                    $(".choosecurrency_box .currency_list").slideUp();

                })


                //全选反选
                $('.allSelect').on('click', function (e) {
                    e.stopPropagation();
                    $('.thePendingTable-box tbody input[type=checkbox]').prop('checked', $(this).find('input').prop('checked'))
                })
                $('.thePendingTable-box tbody input[type=checkbox]').on('click', function () {
                    //当选中的长度等于checkbox的长度的时候,就让控制全选反选的checkbox设置为选中,否则就为未选中
                    if ($('.thePendingTable-box tbody input[type=checkbox]').length === $('.thePendingTable-box tbody input[type=checkbox]:checked').length) {
                        $('.allSelect input[type=checkbox]').prop("checked", true);
                    } else {
                        $('.allSelect input[type=checkbox]').prop("checked", false);
                    }
                })
            })
        })
    </script>
@endsection

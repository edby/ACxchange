@extends('dashboard.layout.app')
@section('style')
<link href="{{ asset('dashboard/plugins/calendar/calendar.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<section class="content report">
    <div class="wallet_history">
        <h3>User Withdrawal Report</h3>
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
                <div class="btn-box box2">
                    <button class="history-btn updatabtn" id="sear"><i class="iconfont icon-icon--"></i>Search</button>
                    <a class="user_export">Export</a>
                </div>
            </div>
            <div class="history_total"></div>
        </div>
        <div class="range">
            <div class="label0">Range</div>
            <div class="rep">
                <input class="l_range" type="text" placeholder="low"
                       onkeyup="this.value=this.value.replace(/[^0-9.]/g, '')">
                <span>=></span>
                <input class="h_range" type="text" placeholder="high"
                       onkeyup="this.value=this.value.replace(/[^0-9.]/g, '')">
            </div>
        </div>

        <!-- 表格 -->
        <div class="reportTable">
                <table id="change">
                    <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>

        </div>
    </div>
    <div id="pagination" class="pagination"></div>
</section>
<script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
<script src='{{ asset('dashboard/plugins/calendar/calendar.js')}}'></script>

<script>
$(function () {
    var filter = [];
    $('#sear').on('click',function () {
        opt.onPageChanged('','',1,1);
    });
    // 分页
    var opt = {
        currentPage: 1,
        totalPages: 1,
        numberOfPages: 5,
        onPageChanged: function (event, oldPage,type, newPage) {
            var t_start = $('.thedata-start').val();
            var t_end = $('.thedata-end').val();
            var l_range = $('.l_range').val();
            var h_range = $('.h_range').val();
            var currency_id = $('#choosecurrency').val();
            if(currency_id == 'Bitcoin(BTC)') currency_id = 1;
            filter = [t_start,t_end,currency_id,l_range,h_range];
            $.ajax({
                url:"{{route('withdrawal_report')}}",
                data:{'t_start':t_start,'t_end':t_end,'page':newPage,
                    'currency_id':currency_id,'l_range':l_range,'h_range':h_range},
                dataType:'json',
                type:'get',
                success:function (msg) {
                    if(msg.code == 200) {
                        var str = '';
                        opt.totalPages = Math.ceil(msg[1]/10);
                        for(var i in msg[0]) {
                            str += `<tr>
                                                <td>${msg[0][i]['user_id']}</td>
                                                <td>${msg[0][i]['sum']}</td>
                                        </tr>`;
                        }
                        str += `<tr style="color: red"><td>Total</td><td>${msg[2]}</td></tr>`;
                        $('#change tbody').empty().append(str);
                        $('.totalPage').empty().html('Total ' + msg[1] + ' Users');
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
    opt.onPageChanged('','',1,1);
    $('#pagination').bootstrapPaginator(opt);
    $(".history_export").on('click',function () {
        window.location.href = ('{{env('ADMIN_DOMAIN').'/userWithdrawalReport'}}?&t_start='+filter[0]+'&t_end='+filter[1]+'&currency_id='+filter[2]+'&l_range='+filter[3]+'&h_range='+filter[4]+'&export=withdraw_report');
        layer.msg('Success', {
            offset: 'auto',
            anim: 0,
            area:['420px']
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

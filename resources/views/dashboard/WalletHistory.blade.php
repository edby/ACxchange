@extends('dashboard.layout.app')
@section('style')
    <link href="{{asset('dashboard/plugins/calendar/calendar.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('dashboard/js/toastr.min.css')}}">
@endsection
@section('content')
<section class="content">
    <div class="wallet_history">
        <h3>Wallet History</h3>
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
                    <li value="2">Bitcoin Cash(BCH)</li>
                    <li value="3">Litecoin(LTC)</li>
                    <li value="4">Rapidz(RPZ)</li>
                    <li value="6">XVG(Verge)</li>
                    <li value="7">BTG(Bitcoin Gold)</li>
                    <li value="8">DASH(Dash)</li>
                </ul>
            </div>
        </div>
        <div class="currency_box historyserch">
            <div class="form-contain">
                <span class="filter-t">Filters:</span>
                <div class="form-history">
                    <input type="text" id="search" value="" placeholder="User ID" >
                </div>
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
                <div class="btn-box">
                        <button class="history-btn" id="sear">Search</button>
                        <button class="history-btn tran_export">Export</button>
                </div>
            </div>
            <div class="history_total totalPage">Total 0 History</div>
        </div>
        <!-- 表格 -->
        <div class="table-box" id="change">
            <table class="table-content history-table">
                <thead>
                    <tr>
                        <th>Date </th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Nationality</th>
                        <th>Deposit</th>
                        <th>Withdrawal</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th>RPZ</th>
                        <th>VIT</th>
                        <th>Fund BTC</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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
        var filter = [];
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
                var t_start = $(".thedata-start").val();
                var t_end = $(".thedata-end").val();
                var user_id = $('#search').val();
                var currency = $('#choosecurrency').attr('val');
                if(!currency) currency=1;
                filter = [user_id,currency,t_start,t_end];
                $.ajax({
                    url:"{{route('history_list')}}",
                    data:{'user_id':user_id,'t_start':t_start,'t_end':t_end,
                        'page':newPage,'currency_id':currency},
                    dataType:'json',
                    type:'get',
                    success:function (msg) {
                        if(msg.code == 200) {
                            var str = '';
                            opt.totalPages = Math.ceil(msg[1]/10);
                            for(var i in msg[0]) {
                                str += `<tr>
                                                <td>${msg[0][i]['created_at']}</td>
                                                <td>${msg[0][i]['user_id']}</td>
                                                <td>${msg[0][i]['name']}</td>
                                                <td>${msg[0][i]['email']}</td>
                                                <td>${msg[0][i]['nationality']}</td>
                                                <td style="color: #00b92c;">${msg[0][i]['receive']}</td>
                                                <td style="color: #d71d1d;">${msg[0][i]['send']}</td>
                                                <td>${msg[0][i]['txid']}</td>`;
                                switch (msg[0][i]['status']){
                                    case 1: var status = '<td style="color: #00b92c;">success'; break;
                                    default: var status = '<td style="color: #999999;">Pending'; break;
                                }
                                str += status;
                                str += `    </td>
                                                <td>${msg[0][i]['trade_rpz']}</td>
                                                <td>${msg[0][i]['trade_vit']}</td>
                                                <td>${msg[0][i]['deposit_btc']}</td>
                                                <td>${msg[0][i]['remarks']}</td>
                                            </tr>`;
                            }
                            str += `<tr>
                                                <td>Total</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>${msg[2]}</td>
                                                <td>${msg[3]}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>`;
                            $('#change tbody').empty().append(str);
                            console.log(str);
                            $('.totalPage').empty().html('Total ' + msg[1] + ' History')
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
        $(".tran_export").on('click',function () {
            console.log(filter);
            window.location.href = ('{{env('ADMIN_DOMAIN').'/getHistory'}}?export=history&user_id='
                +filter[0]+'&currency_id='+filter[1]+'&t_start='+filter[2]+'&t_end='+filter[3]);
            toastr.success('Export now');
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
        //币种选择框动态效果
        $(".choosecurrency_box .toDown_btn").click(function (e){
            e.stopPropagation();
            $(".choosecurrency_box .currency_list").slideToggle();
            $("#choosecurrency").css({"border":"1px solid #0f91ff"});
        })
        $(".choosecurrency_box .currency_list>li").click(function () {
            $("#choosecurrency").attr('val',$(this).attr('value'));
            $("#choosecurrency").css({"border":"1px solid #161b2e","boder-bottom":"none"}).val($(this).html());
            $(".choosecurrency_box .currency_list").slideUp();
        })
 
        $(window).click(function(){
            //收起币种选择框
            $(".choosecurrency_box .currency_list").slideUp();
            setTimeout(function () {
                $("#choosecurrency").css({"border":"1px solid #161b2e","boder-bottom":"none"});
            }, 380);
        })
    })
</script>
@endsection
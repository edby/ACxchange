@extends('dashboard.layout.app')
@section('style')
    <link href="{{asset('dashboard/plugins/calendar/calendar.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('dashboard/js/toastr.min.css')}}">
@endsection
@section('content')
    <section class="content transactions_report">
        <div class="wallet_history">
            <h3>Report</h3>
            <!-- 内容顶部 -->
            <div class="currency_box">
                <span>Market:</span>
                <div class="choosecurrency_box">
                    <input type="text" id="choosecurrency" name="Bitcoin(BTC)" value="BTC  -  BCH" readonly /><i class="toDown_btn iconfont icon-ln_jiantouxia"></i>
                    <ul class="currency_list">
                        <li value="2">BTC   -   BCH</li>
                        <li value="3">BTC   -   LTC</li>
                        <li value="4">BTC   -   RPZ</li>
                        <li value="6">BTC   -   XVG</li>
                        <li value="7">BTC   -   BTG</li>
                        <li value="8">BTC   -   DASH</li>
                    </ul>
                </div>
            </div>
            <div class="currency_box filters">
                <span>Ignored:</span>
                <div class="centers">
                    <input type="text" class="ignored_text" placeholder="User ID">
                    <button class="ignored_add">Add</button>
                </div>
                <div class="listadd">
                    <div class="titles">Ignored Users</div>
                    <ul class="ignored_list">
                        <li><i class="iconfont icon-cuo"></i><span>no data</span></li>
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
                <div class="history_total totalPage">Total 0 Transaction</div>
            </div>
            <!-- 表格 -->
            <div class="table-box">
                <table class="table-content transactions-table" id="change">
                    <thead>
                    <tr>
                        <th>Date </th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th><button style="padding: 7px 12px" class="typeRank history-aua-btn">Type</button></th>
                        <th>Amount</th>
                        <th>Value</th>
                        <th>Total Value</th>
                        <th>Transaction Fee</th>
                        <th><button style="padding: 7px 12px" class="statusRank history-aua-btn">Status</button></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="pagination" class="pagination"></div>
    </section>

    <div class="loading" style="display: none">
        <img class="img-gif" src="{{asset('dashboard/dist/img/Loading.gif')}}">
    </div>

    <script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('dashboard/plugins/calendar/calendar.js')}}"></script>
    <script src="{{asset('dashboard/js/toastr.min.js')}}"></script>
    <script src="{{asset('dashboard/js/toastr_conf.js')}}"></script>
    <script>
        $(function() {
            var filter = [];
            function ignored (user_id,type) {
                $.ajax({
                    url:"{{route('ignored_users')}}",
                    data:{'ignored':user_id,'type':type},
                    dataType:'json',
                    type:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function (msg) {
                        if(msg.code === 200) {
                            if(type === 1){
                                var ignored = `<li ignored="${user_id}"><i class="iconfont icon-cuo ignored_minus"></i><span>${user_id}</span></li>`;
                                $('.ignored_list').append(ignored);
                            }
                            prompt('wrapper','success','Done.');
                        }else {
                            prompt('wrapper','fail','This User Id does not exist.');
                        }
                    },
                    error:function () {
                        prompt('wrapper','fail','Error.');
                    }
                });
            }
            $('.ignored_add').on('click',function () {
                var ignored_add = $('.ignored_text').val();
                ignored(ignored_add,1);
            });
            $('.ignored_list').on('click','.ignored_minus',function () {
                var ignored_minus = $(this).parent().attr('ignored');
                ignored(ignored_minus,2);
                $(this).parent().remove();
            });
            $('#sear').on('click',function () {
                opt.onPageClicked('','',1,1);
            });
            $("#search").keydown(function(e) {
                if (e.keyCode == 13) {//Enter键调用
                    opt.onPageClicked('','',1,1);
                }
            });
            $('.typeRank').on('click',function () {
                var t = $(this).html();
                switch (t){
                    case 'Sell':
                        $(this).empty().html('Buy');
                        opt.onPageClicked('','',1,1);
                        break;
                    case 'Buy':
                        $(this).empty().html('Type');
                        opt.onPageClicked('','',1,1);
                        break;
                    case 'Type':
                        $(this).empty().html('Sell');
                        opt.onPageClicked('','',1,1);
                        break;
                }
            });
            $('.statusRank').on('click',function () {
                var s = $(this).html();
                var t = $('.typeRank').html();
                switch (s){
                    case 'Status':
                        $(this).empty().html('Success');
                        opt.onPageClicked('','',1,1);
                        break;
                    case 'Success':
                        $(this).empty().html('In Progress');
                        opt.onPageClicked('','',1,1);
                        break;
                    case 'In Progress':
                        $(this).empty().html('Cancel');
                        opt.onPageClicked('','',1,1);
                        break;
                    case 'Cancel':
                        $(this).empty().html('Status');
                        opt.onPageClicked('','',1,1);
                        break;
                }
            });
            var opt = {
                currentPage: 1,
                totalPages: 1,
                numberOfPages:5,
                onPageClicked:function (event, originalEvent, type, newPage) {
                    var t = $('.typeRank').html();
                    var s = $(".statusRank").html();
                    switch (t){
                        case 'Sell':
                            $(this).empty().html('Buy');
                            break;
                        case 'Buy':
                            $(this).empty().html('Type');
                            break;
                        case 'Type':
                            $(this).empty().html('Sell');
                            break;
                    }
                    switch (s){
                        case 'Status':
                            $(this).empty().html('Success');
                            break;
                        case 'Success':
                            $(this).empty().html('In Progress');
                            break;
                        case 'In Progress':
                            $(this).empty().html('Cancel');
                            break;
                        case 'Cancel':
                            $(this).empty().html('Status');
                            break;
                    }
                    var rank = t+'-'+s;
                    var t_start = $(".thedata-start").val();
                    var t_end = $(".thedata-end").val();
                    var user_id = $('#search').val();
                    var tran = $('#choosecurrency').attr('val');
                    if(!tran) tran = 2;
                    filter = [user_id,tran,t_start,t_end,rank];
                    $('.loading').show();
                    $.ajax({
                        url:"{{route('change_list')}}",
                        data:{'user_id':user_id,'t_start':t_start,'t_end':t_end,
                            'page':newPage,'tran':tran,'rank':rank,'ignored':1},
                        dataType:'json',
                        type:'get',
                        success:function (msg) {
                            if(msg.code == 200) {
                                var str = '';
                                opt.totalPages = Math.ceil(msg[1]/10);
                                for(var i in msg[0]['data']) {
                                    str += `<tr>
                                    <td>${msg[0]['data'][i]['updated_at']}</td>
                                    <td>${msg[0]['data'][i]['user_id']}</td>
                                    <td>${msg[0]['data'][i]['name']}</td>
                                    <td>${msg[0]['data'][i]['email']}</td>
                                    <td>${msg[0]['data'][i]['type']}</td>
                                    <td>${msg[0]['data'][i]['volume']}</td>
                                    <td>${msg[0]['data'][i]['price']}</td>
                                    <td>${msg[0]['data'][i]['total_price']}</td>
                                    <td>${msg[0]['data'][i]['fee']}</td>`;
                                    switch (msg[0]['data'][i]['status']){
                                        case 'unfinished': var status = `<td class="progressing">in progress</td>`; break;
                                        case 'success': var status = `<td class="stutus_Completed">success</td>`; break;
                                        case 'cancel': var status = `<td class="stutus_cancelled">cancel</td>`; break;
                                        default: break;
                                    }
                                    str += status +`</tr>`;
                                }
                                $('#change tbody').empty().append(str);
                                var sum = `<tr>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>
                                Sell:${msg[2]['volume']}<br>
                                Buy:${msg[3]['volume']}
                            </th>
                                <th></th>
                                <th>
                                Sell:${msg[2]['total_price']}<br>
                                Buy:${msg[3]['total_price']}
                            </th>
                                <th>${msg[4]}</th>
                                </tr>`;
                                $('#change tfoot').empty().append(sum);
                                var total = `Total <span class="total">${msg[1]}</span> Transactions`;
                                $('.totalPage').empty().append(total);
                                if(msg[1] === 0){
                                    $('#pagination').empty();
                                }

                                var ignored = '';
                                for(var i in msg[5]) {
                                    ignored += `<li ignored="${msg[5][i]['user_id']}"><i class="iconfont icon-cuo ignored_minus"></i><span>${msg[5][i]['user_id']}</span></li>`;
                                }
                                $('.ignored_list').empty().append(ignored);

                                $('.loading').hide();

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
                window.location.href = ("{{env('ADMIN_DOMAIN').'/getChange'}}?export=tran&ignored=true&user_id="
                    +filter[0]+'&tran='+filter[1]+'&t_start='+filter[2]+'&t_end='+filter[3]+'&rank='+filter[4]);
                toastr.success('Export now');
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
        });
        $(function () {


            //币种选择框动态效果
            $(".choosecurrency_box .toDown_btn").click(function (e) {
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

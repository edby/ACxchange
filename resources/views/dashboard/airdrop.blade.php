@extends('dashboard.layout.app')
@section('content')
<section class="content">
    <div class="adjust">
        <form class="adjust-form">
            <div class="form-g">
                <span><i class="iconfont icon-jiufuqianbaoicon14 form-iconxing"></i><label for="currency" class="form-label">Choice of currency</label></span>
                <div class="dropdown_box"><input type="text" class="form-input" id="currency" name="" value="Bitcoin(BTC)" readonly /><i class="toDown_btn iconfont icon-ln_jiantouxia"></i></div>
                <ul class="dropdown_list" id="currency">
                    @foreach($currency_list as $list)
                        <li value="{{$list['id']}}">{{$list['full_currency']}}({{$list['currency']}})</li>
                    @endforeach
                </ul>
            </div>
            <div class="form-g">
                <span><i class="iconfont icon-jiufuqianbaoicon14 form-iconxing"></i>
                    <label class="total_user" user="{{$userCount}}">Total {{$userCount}} Users</label>
                </span>
            </div>
            <div class="form-g">
                <span><i class="iconfont icon-jiufuqianbaoicon14 form-iconxing"></i><label for="amount" class="form-label">Airdrop Amount To {{$userCount}} Users</label></span>
                <input type="number" class="form-input" id="amount" name="" placeholder="0.00" />
            </div>
            <div class="form-g">
                <span><i class="iconfont icon-jiufuqianbaoicon14 form-iconxing"></i><label for="remarks" class="form-label">Remarks</label></span>
                <input type="text" class="form-input" id="remarks" name="" placeholder="Remarks" />
            </div>
            <div class="form-g">
                <input class="btn-sub" value="SUBMIT" />
            </div>
        </form>
    </div>
    <div class="listClientEdit"><!-- log -->
        <div class="listClientEdit-warp">
            <div class="row" style="margin-top:5px;">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nov_table" id="change">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Manager</th>
                                <th>IP</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div id="pagination"></div>
        </div>
    </div>
    <!-- model -->
    <div class="modal fade" id="check" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="del-admin">
                    <i class="iconfont icon-cuo close-btn" data-dismiss="modal"></i>
                    <span><i class="iconfont icon-gantanhao"></i></span>
                    <p class="title"></p>
                    <div class="admin-btn-group">
                        <a href="javascript:;" class="affirm sureDel">Yes</a>
                        <a href="javascript:;" class="abolish" data-dismiss="modal">No</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <div class="adjust"></div>
</section>
<script src="{{asset('layer/layer.js')}}" type="text/javascript"></script>
<script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
<script>
    $(function(){
        $(".btn-sub").on('click',function(){
            var currency = $("#currency").attr('val')?$("#currency").attr('val'):1;
            switch (currency) {
                case '1': var currency_name = 'BTC'; break;
                case '2': var currency_name = 'BCH'; break;
                case '3': var currency_name = 'LTC'; break;
                case '4': var currency_name = 'RPZ'; break;
                case '5': var currency_name = 'ETH'; break;
                case '6': var currency_name = 'XVG'; break;
                case '7': var currency_name = 'PIVX'; break;
                case '8': var currency_name = 'DASH'; break;
                case '9': var currency_name = 'BTG'; break;
                case '10': var currency_name = 'VIT'; break;
                default: var currency_name = 'BTC'; break;
            }
            var total = $(".total_user").attr('user');
            var amount = $("#amount").val();
            console.log(currency);
            model('wrapper','fail','Check: Adjust Total '+total+' Users * '+amount+' '+currency_name+' = '+total*amount+' '+currency_name,function() {
                if(!amount){
                    layer.msg('Amount is required', {
                       offset: 'auto',
                       anim: 6,
                       area:['420px']
                    });
                }else{
                    $('.fail_modal').modal('show');//fa-edit
                    $('.delete-click').attr('disabled','disabled');
                    $('.delete-click').html('3');
                    setTimeout(function () {
                        $('.delete-click').html('2');
                        setTimeout(function () {
                            $('.delete-click').html('1');
                            setTimeout(function () {
                                $('.delete-click').html('YES');
                                $('.delete-click').attr('disabled',false);
                            },1000);
                        },1000);
                    },1000);
                    $('.delete-click').on('click',function() {
                        $.ajax({
                            url:"{{route('adjust_airdrop')}}",
                            data:{
                                'currency_id':currency,
                                'amount':amount,
                                'remarks':$(".remarks").val()
                            },
                            dataType:'json',
                            type:'get',
                            success:function (msg) {
                                if(msg.code == 200) {
                                    layer.msg('Update Success', {
                                        offset: 'auto',
                                        anim: 0,
                                        area:['420px']
                                    });
                                    setTimeout(function(){
                                        window.location.href = "{{env('ADMIN_DOMAIN').'/WalletManagement/airdrop'}}";
                                    },800)
                                }else {
                                    layer.msg(msg['message'], {
                                        offset: 'auto',
                                        anim: 6,
                                        area:['420px']
                                    });
                                }
                            },
                            error:function (mes) {
                                layer.msg('Error', {
                                    offset: 'auto',
                                    anim: 6,
                                    area:['420px']
                                });
                            }
                        });
                    });
                }
            });
        });

        $(".form-g .toDown_btn").click(function (e) {
            e.stopPropagation();
            $(".form-g .dropdown_list").slideToggle();
            $(this).siblings("#currency").css({"border":"1px solid #0f91ff"});
        })
        $(".form-g .dropdown_list>li").click(function () {
            $(this).siblings("#currency").css({"border":"1px solid #161b2e","boder-bottom":"none"});
            $("#currency").attr('val',$(this).attr('value'));
            $("#currency").val($(this).html());
            $(".form-g .dropdown_list").slideUp();
        })
        $(window).click(function(){
            $(".form-g .dropdown_list").slideUp();
            setTimeout(function(){
                $("#currency").css({"border":"1px solid #161b2e","boder-bottom":"none"});
            }, 380);
        })
        $(".adjust-form .form-input").focus(function(){
            $(this).parents(".form-g").find(".form-input").css({"border":"1px solid #161b2e"}); 
            $(this).css({"border":"1px solid #0f91ff"}); 
        })
        $(".adjust-form .form-input").blur(function(){
            $(this).css({"border":"1px solid #161b2e"}); 
        })

        $(function() {
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
            var opt = {
                currentPage: 1,
                totalPages: 1,
                numberOfPages:5,
                onPageClicked:function (event, originalEvent, type, newPage) {
                    // var something = $('#search').val();
                    // filter = something;
                    $.ajax({
                        url:"{{route('manager_log')}}",
                        data:{'page':newPage,'type':4},
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
                                    if(msg[0]['data'][i]['type'] === 2){
                                        str += `Move`;
                                    }
                                    str += `</td>
                                            <td>${msg[0]['data'][i]['author_name']}</td>
                                            <td>${msg[0]['data'][i]['ip_address']}</td>
                                            <td>${msg[0]['data'][i]['action']}</td>
                                         </tr>`;
                                }
                                $('#change tbody').empty().append(str);
                                // $('#totalPage').empty().html('Total ' + msg[1] + ' Logs')
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
        })
    })
</script>
@endsection
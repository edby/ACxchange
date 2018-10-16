@extends('layouts.app')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/ac/bootstrap.min.css') }}">
    <script src="{{ asset('js/ac/bootstrap.min.js') }}"></script>
    <script src="{{ asset('layer/layer.js') }}?id={{str_random(20)}}" type="text/javascript"></script>
    <style>
        /*浏览器默认input*/
        /* .main-login .login-right .form-group input{
            box-shadow: 40px 40px 40px 40px # inset;
            color: #dde1e8!important;
        } */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #1d1d25 inset !important;//关于解决输入框背景颜色
        -webkit-text-fill-color: #fff !important;//关于接输入框文字颜色
        }
        input{
            text-fill-color:#fff;
            -webkit-text-fill-color:#fff;
        }
        .ver_unput{
            text-fill-color:#000;
            -webkit-text-fill-color:#000;
        }
    </style>
@endsection
@section('content')
    <!--trade内容部分-->
    <div id="trade" class="wallet2">
        <div class="content0">
            <div class="content-left">
                <div class="wallet-left">
                    <div class="left2 wow fadeInLeft">
                        <div class="balan">
                            <div>
                                <div class="handler hand4">
                                    <div class="handl-btn">
                                        <i class="iconfont icon-less"></i><span>@lang('ac.Balance')</span>
                                    </div>
                                    <div class="handl-right">
                                        <div class="total2">
                                            <a>@lang('ac.TotalValue')</a><span> </span><a>BTC</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="bigbox wallet_balance_box">
                                    <table>
                                        <thead class="w_thead"><tr class="title0"><th>@lang('ac.Currency')</th><th>@lang('ac.InTrades')</th><th>@lang('ac.Balance')</th><th>@lang('ac.btc_value')</th></tr></thead>
                                        <tbody class="w_tbody w_balance_tbody">

                                        @if(!empty($statusAccount))
                                            @foreach($statusAccount as $key => $account)
                                                <tr>
                                                    <td><i><img src="{{ asset('images/'.$account['curr_abb'].'.png') }}"></i><span>{{strtoupper($account['curr_abb'])}}</span></td>
                                                    <td> @if($account['in_trade'] == 0.00000000) 0.00000000 @else {{$account['in_trade']}} @endif </td>
                                                    <td>{{$account['balance']}}</td>
                                                    <td>{{$account['btc_rate']}}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td style="line-height: 8px;"> <i><img src="{{ asset('images/acext.png')}}"></i><span>ACEX</span><br>
                                                    <small style="font-size:8px !important;">(coming soon)</small>
                                                </td>
                                                <td>0.00000000 </td>
                                                <td>0.00000000</td>
                                                <td>0.00000000</td>
                                            </tr>
                                        @else
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="center2 wow fadeInDown">
                        <div>
                            <div class="handler hand4">
                                <div class="handl-btn">
                                    <i class="iconfont icon-less"></i>
                                    <span>@lang('ac.Deposit')</span>
                                </div>
                            </div>
                            <div class="bigbox">
                                <!--Deposit-->
                                <div class="withdraw-bit deposit2">
                                    <div class="manu2">
                                        <div class="lists0">
                                            <ul>
                                                <li class="act3">{{$currency}}</li>
                                                <li>BCH</li>
                                                <li>LTC</li>
                                                <li>RPZ</li>
                                                <li>XVG</li>
                                                <li>BTG</li>
                                                <li>DASH</li>
                                            </ul>
                                        </div>
                                        {{--<div class="btn2">--}}
                                        {{--<i class="iconfont icon-caidan"></i>--}}
                                        {{--<div class="dowlist">--}}
                                        {{--<ul>--}}
                                        {{--<li><i><img src="{{ asset('images/zec.png') }}"></i><span>ZEC</span></li>--}}
                                        {{--<!-- <li><i><img src="{{ asset('images/bch.png') }}"></i><span>BCH</span></li> -->--}}
                                        {{--<em></em>--}}
                                        {{--</ul>--}}
                                        {{--</div>--}}
                                        {{--</div>--}}
                                    </div>
                                    <div class="withdraw-box">
                                        <div class="boxlist">
                                            <div>
                                                <div>@lang('ac.Currency'):</div>
                                                <div class="currency">
                                                    <label>{{$currency}}</label>
                                                </div>
                                            </div>
                                            <div class="chang_address">
                                                <div>@lang('ac.Address'):</div>
                                                <div class="copyrow">
                                                    <input class="addre" type="text" id="copy2" value="" style="background: transparent;width:85%;display: block;" readonly>
                                                    <div class="copybtn2"><i>@lang('ac.Copy')</i></div>
                                                </div>
                                            </div>
                                            {{--<div>--}}
                                                {{--<div>@lang('ac.Address'):</div>--}}
                                                {{--<div class="addre2">--}}
                                                    {{--@lang('ac.textbox')--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                            {{--<div class="copyrow">--}}
                                                {{--暂时注释--}}
                                                {{--<input type="text" readonly class="addre" id="copy2" style="background: transparent;width:85%;display: block;" value="{{$address}}">--}}
                                                {{--<input type="text" readonly style="background: transparent;width:85%;display: block;" value=" ">--}}
                                                {{--<input class="addre" type="text" id="copy2" value="" style="background: transparent;width:85%;display: block;" readonly>--}}
                                                {{--<div class="copybtn2"><i>@lang('ac.Copy')</i></div>--}}
                                            {{--</div>--}}
                                            {{--<div class="code2 addre">{{$address}}</div>--}}
                                            {{--<div class="addre"> </div>--}}
                                            <div>
                                                <div>@lang('ac.QRcode'):</div>
                                                {{--<div class="qr">@lang('ac.enlarge')</div>--}}
                                            </div>
                                        </div>
                                        <div class="change_bottom">
                                            <div class="code3">
                                                <div>
                                                    <img src="">
                                                </div>
                                            </div>
                                            <div class="add_new">
                                                <div class="newtop"><span class="bitt">BTC</span><span>@lang('ac.Address'):</span></div>
                                                <div class="addrnew"></div>
                                                <div class="qr">@lang('ac.enlarge')</div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--维护中--}}
                        {{--<div class="maintenance">--}}
                            {{--<div class="box3">--}}
                                {{--<img src="{{ asset('images/deposit2.png') }}">--}}
                                {{--<p>@lang('ac.maintenanceCashDeposit')</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                    <div class="right2 wow fadeInRight">
                        <div>
                            <div class="handler hand4">
                                <div class="handl-btn">
                                    <i class="iconfont icon-less"></i>
                                    <span>@lang('ac.Withdraw')</span>
                                </div>
                                <span class="re-amount">@lang('ac.theRemainingAmount'){{$withDrawOne}}BTC</span>
                            </div>
                            <div class="bigbox">
                                <!--Withdraw-->
                                <div class="withdraw-bit withdraw2">
                                    <div class="manu2">
                                        <div class="lists0">
                                            <ul>
                                                <li class="select_currency act3">BTC</li>
                                                <li class="select_currency">BCH</li>
                                                <li class="select_currency">LTC</li>
                                                <li class="select_currency">RPZ</li>
                                                <li class="select_currency">XVG</li>
                                                <li class="select_currency">BTG</li>
                                                <li class="select_currency">DASH</li>
                                            </ul>
                                        </div>
                                        {{--<div class="btn2">--}}
                                        {{--<i class="iconfont "></i>--}}
                                        {{--<div class="dowlist">--}}
                                        {{--<ul>--}}
                                        {{--<li><i><img src="{{ asset('images/zec.png') }}"></i><span>ZEC</span></li>--}}
                                        {{--<!-- <li><i><img src="{{ asset('images/bch.png') }}"></i><span>BCH</span></li> -->--}}
                                        {{--<em></em>--}}
                                        {{--</ul>--}}
                                        {{--</div>--}}
                                        {{--</div>--}}
                                    </div>
                                    <div class="withdraw-box">
                                        <div class="boxlist">
                                            <div>
                                                <div>@lang('ac.Currency'):</div>
                                                <div class="curr">
                                                    <label class="withdraw_currency">BTC</label>
                                                </div>
                                            </div>
                                            <div>
                                                <div>@lang('ac.Address'):</div>
                                                <div class="address"><input type="text" class="withdraw_address" value=""></div>
                                            </div>
                                            <div>
                                                <div>@lang('ac.Amount'):</div>
                                                <div style="display: flex; flex-direction: column;padding: 0;">
                                                    <div class="amount">
                                                        <input type="text" class="withdraw_amount" placeholder="">
                                                        <a>@lang('ac.MAX'): <span class="maxNum" style="color: #fff;">0.00000000</span></a>
                                                    </div>
                                                    <div id="amountError" style="display:inline-block"></div>
                                                </div>
                                            </div>
                                            {{--<div class="worong">--}}
                                                {{--<div></div><div class="wor2"><i class="iconfont icon-asmkticon0246"></i><span>@lang('ac.AmountWrong')</span></div>--}}
                                            {{--</div>--}}
                                            <div>
                                                <div>@lang('ac.Fees'):</div>
                                                <div class="fee">
                                                    <input class="withdraw_fee" type="text" value="0.004" readonly>
                                                </div>
                                            </div>
                                            <div>
                                                <div>@lang('ac.Total'):</div>
                                                <div class="netTotal">
                                                    <input class="withdraw_total" type="text" value="" readonly>
                                                </div>
                                            </div>
                                            <div>
                                                <div>@lang('ac.Pin'):</div>
                                                <div class="pin">
                                                    @if(empty(\Illuminate\Support\Facades\Auth::user()->pin))
                                                        @lang('ac.proceedToAccount')
                                                    @else
                                                        <input type="password" class="withdraw_pin">
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div>@lang('ac.Remarks'):</div>
                                                <div class="remarks"><input type="text"  class="remarks_input"></div>
                                            </div>
                                        </div>
                                        <div class="withbtn"><a class="withdraw_put btnNotActiv">@lang('ac.Withdraw')</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--维护中--}}
                        {{--<div class="maintenance">--}}
                            {{--<div class="box3">--}}
                                {{--<img src="{{ asset('images/withdrawal2.png') }}">--}}
                                {{--<p>@lang('ac.maintenanCashWithdrawal')</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>
                <div class="wallet-history">
                    <div class="history0 wow fadeInUp">
                        <div>
                            <div style="margin-top: 20px;">
                                <div class="handler hand4">
                                    <div class="handl-btn">
                                        <i class="iconfont icon-less"></i>
                                        <a>
                                            <span class="active-name">@lang('ac.DepositHistory')</span>
                                            <span class="border0">@lang('ac.WithdrawalHistory')</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="bigbox wallet_deh_box">
                                    <!--Deposit history-->
                                    <div class="deposit-history">
                                        <table class="tablebox">
                                            <thead class="w_thead">
                                            <tr>
                                                <th>@lang('ac.Timestamp')</th>
                                                <th>@lang('ac.Currency')</th>
                                                <th>@lang('ac.ToAddress')</th>
                                                <th>@lang('ac.TransactionID')</th>
                                                <th>@lang('ac.Amount')</th>
                                                <th>@lang('ac.Status')</th>
                                                <th>@lang('ac.Confirmations')</th>
                                            </tr>
                                            </thead>
                                            <tbody class="w_tbody w_deh_tbody">
                                            @empty(!$deposits)
                                                @foreach($deposits as $key => $deposit)
                                                    <tr>
                                                        <td>{{ $deposit->created_at}}</td>
                                                        <td><i><img src="{{ asset('images/'.strtolower($currencies[$deposit->currency_id]).'.png') }}"></i><span>{{$currencies[$deposit->currency_id]}}</span></td>
                                                        <td>{{ $deposit->address }}</td>
                                                        <td>{{ $deposit->txid }}</td>
                                                        <td>{{ $deposit->amount }}</td>
                                                        @if( $deposit->status != 0)
                                                            <td class="suce">@lang('ac.successfully')</td>
                                                            <td class="suce">{{ $deposit->confirmations }}</td>
                                                        @else
                                                            <td class="ongo">@lang('ac.OnGoing')</td>
                                                            <td class="ongo">{{ $deposit->confirmations }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endempty
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--Withdrawal history-->
                                    <div class="withdrawal-history">
                                        <table class="tablebox">
                                            <thead class="w_thead">
                                            <tr>
                                                <th>@lang('ac.Timestamp')</th>
                                                <th>@lang('ac.Currency')</th>
                                                <th>@lang('ac.ToAddress')</th>
                                                <th>@lang('ac.TransactionID')</th>
                                                <th>@lang('ac.Amount')</th>
                                                <th>@lang('ac.Status')</th>
                                                <th>@lang('ac.Confirmations')</th>
                                            </tr>
                                            </thead>
                                            <tbody class="w_tbody w_deh_tbody">
                                            @empty(!$withDraws)
                                                @foreach($withDraws as $key => $withDraw)
                                                    <tr>
                                                        <td>{{ $withDraw->created_at }}</td>
                                                        <td><i><img src="{{ asset('images/'.strtolower( $withDraw->currency ).'.png') }}"></i><span>{{$withDraw->currency}}</span></td>
                                                        <td>{{$withDraw->address}}</td>
                                                        <td>{{$withDraw->txid}}</td>
                                                        <td >{{$withDraw->amount}}</td>
                                                        @if($withDraw->status == 0)
                                                            <td class="ongo" onclick="reSend({{$withDraw->id}})" style="display: flex; align-items:center;flex-direction:column;line-height: 12px;">
                                                                <span>@lang('ac.Emailconfirmation')</span><span style="color: red;">resend?</span>
                                                            </td>
                                                            <td class="ongo">0</td>
                                                        @elseif($withDraw->status == 2)
                                                            <td class="ongo">@lang('ac.Pending')</td>
                                                            <td class="ongo">0</td>
                                                        @elseif($withDraw->status == 1)
                                                            <td class="suce">@lang('ac.successfully')</td>
                                                            <td class="suce">{{ $withDraw->confirmations}}</td>
                                                        @else
                                                            <td class="ongo">@lang('ac.refused')</td>
                                                            <td class="ongo">{{ $withDraw->confirmations }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endempty
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--右边内容-->
            <div class="content-right">
                @component('front.smallMarket')@endcomponent
                @component('front.smallOrder')@endcomponent
            </div>
        </div>
        <i class="iconfont icon-caidan" id="caidan"></i>
    </div>
    <div class="modal fade" id="authy_modal" tabindex="-1" role="dialog" style="padding-right: 17px;">
        <div class="modal-dialog" role="document" style="margin-top: 341.5px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">@lang('ac.FA')</h4>
                </div>
                <div class="modal-body">
                    <p id="2fa_verify_title">@lang('ac.YouopenedCode')</p>
                    <div class="authy_code">
                        {{--<input type="text" class="" id="newtext" autofocus="autofocus">--}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="send_authy" type="button" class="btn btn-nov" onclick="two_pwd()">OK</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {
            limitWithdraw('0.00000001','btc')
        });

        function limitWithdraw(amount,curr) {
            $.ajax({
                url:'/wallet/withdrawalInterval',
                type:'get',
                data:{curr:curr,amount:amount},
                success:function (data) {
                    if (data.status === 0){
                        $('#amountError').text(data.message);
                        console.log(116575611);
                    }
                }
            })
        }
        function reSend(id){
            $.ajax({
                url:'/wallet/restWithdrawEmail',
                type:'POST',
                data:{id:id},
                success:function (data) {
                    console.log(data);
                    successfully(data.message);
                },
                error:function (error) {
                    console.log('/wallet/restWithdrawEmail');
                }
            })
        }

        $(".withdraw_put").on('click',function () {
            $.ajax({
                url:'/wallet/authSelect',
                type:'POST',
                success:function (data) {
                    if(data.status === 2){
                        $("#2fa_verify_title").html(data.title);
                        var authy = '';
                        $(".authy_code").empty();
//                        for(var i = 0; i < data.len; i ++) {//msg.len返回输入框的长度
//                            authy += "<input type='number' maxlength='1' class='gg-input ver_unput' id='verify_"+i+"' >"
//                        }

                        authy += "<input type='text'  id='newtext' autofocus='autofocus'>"
                        authy += "<input type='hidden' maxlength='1' class='gg-input' id='type' value='"+data.type+"'>";
                        authy += "<input type='hidden' maxlength='1' class='gg-input' id='check' value='"+data.check+"'>";
                        $('.authy_code').append(authy);
                        $("#authy_modal").modal('show');
//                        keyFun('#authy_modal .authy_code',data.len-1,'#authy_modal')
                    }else if (data.status === 0){
                        error(data.message);
                    }
                },
                error:function (error) {
                    console.log('/wallet/authSelect ask error');
//                    layer.msg('data error', {
//                        offset: 'auto',
//                        anim: 6,
//                        area:['420px']
//                    });
                }
            })
        });
//        function keyFun(input,num,parent){
//            //当输入一个框自动跳到另一个框
//            $(input).find('input').each(function (r, a) {
//                $(a).on("focus", function (e) {
//                    // $(e.target).val("")
//                })
//                $(a).on("keydown", function () {
//                    // return !1
//                })
//                $(a).on("keyup", function (a) {
//                    if (a.keyCode >= 96 && a.keyCode <= 105 || a.keyCode >= 48 && a.keyCode <= 57) {
//                        if (num != (r)) {
//                            $(this).val(a.key);
//                            $(input).find("input")[r + 1].focus();
//                        } else {
//                            $(this).val(a.key);
//                            $(this).blur();
//                            if(parent) {
//                                $(parent).focus()
//                            }
//                        }
//                    }
//                    if (8 !== a.keyCode) {
//                        return !1;
//                    } else {
//                        if (0 !== r) {
//                            $(input).find("input")[r-1].focus();
//                            $(input).find("input")[r-1].focus();
//                        }
//                    }
//                })
//
//            })
//        }
        function two_pwd(){
            var withdraw_code = $("#newtext").val();
            var withdraw_type = $("#type").val();
            var withdraw_check = $("#check").val();
            var withdraw_currency = $("#trade .withdraw2 .manu2 .lists0 ul li.act3").html();
            var withdraw_address = $(".withdraw_address").val();
            var withdraw_amount = $(".withdraw_amount").val();
            var withdraw_fee = $(".withdraw_fee").val();
            var remarks_input = $(".remarks_input").val();
            var withdraw_pin = $(".withdraw_pin").val();
            var index = layer.load(0, {shade: [0.5,'#2c3557 ']});

            console.log({
                currency:withdraw_currency,
                address:withdraw_address,
                amount:withdraw_amount,
                check:withdraw_check,
                type:withdraw_type,
                code:withdraw_code,
                fee:withdraw_fee,
                pin:withdraw_pin,
                remarks:remarks_input
            });

            $.ajax({
                url:'/wallet/withdraw',
                type:'POST',
                data:{
                    currency:withdraw_currency,
                    address:withdraw_address,
                    amount:withdraw_amount,
                    check:withdraw_check,
                    type:withdraw_type,
                    code:withdraw_code,
                    fee:withdraw_fee,
                    pin:withdraw_pin,
                    remarks:remarks_input
                },
                success:function (data) {
                    if(data.status === 1){
                        successfully(data.message);
                        setTimeout(function () {
                            layer.close(index)
                            window.location.reload();
                        },600);
                    }else{
                        error(data.message);
                        setTimeout(function () {
                            layer.close(index);
                        },2100);
                    }
                },
                error:function (error) {
                   console.log('/wallet/withdraw ask error')
                    layer.close(index);
//                    layer.msg('Please enter full code', {
//                        offset: 'auto',
//                        anim: 6,
//                        area:['420px']
//                    });
                }
            })
        }
//enter回车调用two_pwd()；
        $('.authy_code').on('keydown','#newtext',function(e){

            // 兼容FF和IE和Opera
            var theEvent = e || window.event;
            var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
            if (event.keyCode == "13") {
                two_pwd();
            }
        });
    </script>
@endsection

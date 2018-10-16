<!--尾部-->
<div id="footers">
    <ul>
        <li><a href="{{ url('/help/index?#faq') }}">@lang('ac.FAQ')</a></li>
        @if(\Illuminate\Support\Facades\App::getLocale() === 'en')
            <li><a href="{{ url('/help/en') }}">@lang('ac.APIDocumentation')</a></li>
        @else
            <li><a href="{{ url('/help/ch') }}">@lang('ac.APIDocumentation')</a></li>
        @endif
        <li><a href="{{ url('/help/contactUs') }}">@lang('ac.ContactACxchange')</a></li>
    </ul>
    <a href="{{ url('/') }}">
        <img src="{{ asset('images/footer-logo.png') }}" alt="">
    </a>
    <p>© Copyright 2018 ACxchange</p>
</div>
<!--菜单弹框-->
<div id="menu">
    <div class="colu-menu">
        <div class="btnn"><strong><i class="iconfont icon-caidan"></i></strong></div>
        <ul>
            <li @if($current == url('/trade/index')) class="actives" @endif>
                <a href="{{ url('/trade/index') }}">
                    <i class="iconfont icon-ts01"></i>
                    <span>@lang('ac.Trading')</span>
                </a>
            </li>
            <li  @if($current == url('/order/index')) class="actives" @endif>
                <a href="{{ url('/order/index') }}">
                    <i class="iconfont icon-Delistingorders"></i>
                    <span>@lang('ac.Orders')</span>
                </a>
            </li>
            <li @if($current === url('/wallet/index')) class="actives" @endif>
                <a href="{{ url('/wallet/index') }}">
                    <i class="iconfont icon-qianbao"></i>
                    <span>@lang('ac.Wallet')</span>
                </a>
            </li>
            <li @if($current === url('/user/index')) class="actives" @endif>
                <a href="{{ url('/user/index') }}">
                    <i class="iconfont icon-account"></i>
                    <span>@lang('ac.Account')</span>
                </a>
            </li>
            <li @if($current === url('/help/index')) class="actives" @endif>
                <a href="{{ url('/help/index') }}">
                    <i class="iconfont icon-help"></i>
                    <span>@lang('ac.Help')</span>
                </a>
            </li>
        </ul>
        <div class="lastbtn">
            <a href="javascript:logOut();">
                <i class="iconfont icon-logoutcloudboot"></i>
                <span>@lang('ac.Logout')</span>
            </a>
        </div>
    </div>
</div>
<!--菜单弹框结束-->

{{--买卖弹框--}}
<div class="tankuang">
    <div class="box2">
        <div class="title2"><span>@lang('ac.LimitOrder')</span><i class="iconfont icon-guanbi cancel"></i></div>
        <div class="cont2">
            <div class="bigbox2">
                <div class="orderlist2">
                    <div>
                        <label>@lang('ac.Type'):</label>
                        <div class="type2"><span class="nice">@lang('ac.LimitSell')</span></div>
                    </div>
                    <div class="">
                        <label>@lang('ac.Market'):</label>
                        <div class="market2"><span class="nice">RPZ/BTC</span></div>
                    </div>
                    {{--<div class="">--}}
                        {{--<label>@lang('ac.TimeInForce'):</label>--}}
                        {{--<div class="time2"><span class="nice">@lang('ac.ImmediateCancel')</span></div>--}}
                    {{--</div>--}}
                    <div class="">
                        <label>@lang('ac.Price') :</label>
                        <div class="lists price2"><input type="text" name="price" value="0.00000000" readonly><span class="bit2">BTC</span></div>
                    </div>
                    <div class="">
                        <label>@lang('ac.Amount'):</label>
                        <div class="lists amount2"><input type="text" name="amount" value="0.00000000" readonly><span class="bit2">BCH</span></div>
                    </div>
                    <div class="">
                        <label>@lang('ac.Fees'):</label>
                        <div class="lists fee2"><input type="text" name="fees" value="0.00000000" readonly><span class="bit2">BTC</span></div>
                    </div>
                    <div class="">
                        <label class="paymentOrTotal">@lang('ac.Total'):</label>
                        <div class="lists total2"><input type="text" name="total" value="0.00000000" readonly><span class="bit2">BTC</span></div>
                    </div>
                </div>
            </div>
            <div class="disc2">
                {{--<h3>@lang('ac.Disclaimer')</h3>--}}
                <p>
                    @lang('ac.YouAre') <span class="typeing">buying</span> <span class="amount3">0.00</span> <span class="bit3" style="text-transform: uppercase;">LTC</span> @lang('ac.for') <span class="total3">0.00</span> <span class="">BTC</span>.@lang('ac.confirmTransaction')
                </p>
                <div>
                    @lang('ac.OnceCompletedRefund')
                </div>
            </div>
            <div class="btns2">
                <div><a class="cancel">@lang('ac.Cancel')</a></div>
                <div><a class="confirm">@lang('ac.Confirm')</a></div>
            </div>
        </div>
    </div>
</div>
{{--弹框结束--}}

{{--cancel取消弹框验证pin的弹框--}}
<div class="modelPin">
    <div class="modal2">
        <div class="pinTitle">
            <div class="please">@lang('ac.enterThePin')</div>
            <div class="closePin"><i class="close0">×</i></div>
        </div>
        <div class="bodyPin">
            <label>@lang('ac.Pin')</label>
            <div><input type="password" value="" autofocus="autofocus" autocomplete="off"></div>
            <div class="forget"><a>@lang('ac.ForgetPin')</a></div>
        </div>
        <div class="footerPin">
            <div><a class="ok0">@lang('ac.OK')</a></div>
            <div><a class="close0">@lang('ac.Close')</a></div>
        </div>
    </div>
</div>

<!-- forget Pin -->
<div class="forgetPin2">
    <div class="dialog-part2">
        <div class="dialog-header2">
            <span>@lang('ac.ResetPin')</span><i class="iconfont icon-guanbi closebtn2"></i>
        </div>
        <div class="dialog-content2">
            <p>@lang('ac.leastCharacters')</p>
            <div class="sheet2">
                <div class="rank">
                    <label for="">@lang('ac.NewPin'):</label>
                    <div class="rank-row">
                        <input type="password" name="newpin"  placeholder="" autocomplete="off">
                        <i class="iconfont icon-duihao ft-green2"></i>
                    </div>
                    <span class="tips"><i class="iconfont icon-cuo"></i>@lang('ac.NewPin')</span>
                </div>
                <div class="rank">
                    <label for="">@lang('ac.NewPinAgain'):</label>
                    <div class="rank-row">
                        <input type="password" name="pinagain" placeholder="" autocomplete="off">
                        <i class="iconfont icon-duihao ft-green2"></i>
                    </div>
                    <span class="tips"><i class="iconfont icon-cuo"></i>@lang('ac.NewPinAgain')</span>
                </div>
                <div class="rank">
                    <label for="">@lang('ac.Emailverification'):</label>
                    <div class="rank-row">
                        <input type="text" name="emailCode"  placeholder="" autocomplete="off">
                        <i class="iconfont icon-duihao ft-green2"></i>
                    </div>
                    <span class="tips"><i class="iconfont icon-cuo"></i>@lang('ac.Emailverification')</span>
                </div>
                <div class="rank">
                    <button type="button" class="">@lang('ac.Submit')</button>
                </div>
            </div>
        </div>
    </div>
</div>


{{--successfully成功提示--}}
<div class="successbox">
    <div class="icon0">
        <div class="yuan0"><i class="iconfont icon-dui"></i></div>
    </div>
    <div class="success_tips"></div>
</div>

{{--error成功提示--}}
<div class="errorbox">
    <div class="icon0">
        <div class="yuan0"><i class="iconfont icon-open-warn"></i></div>
    </div>
    <div class="error_tips"></div>
</div>
<script>
    function logOut(){
        layer.open({
             title:'@lang('ac.prompt')'
            ,content:'@lang('ac.AreLogOut')'
            ,btn: ['@lang('ac.Confirm')', '@lang('ac.Cancel')']
            ,yes: function(index, layero){
                $.ajax({
                    url:'/index/logOut'
                    ,type:"GET"
                    ,success:function (msg) {
                        window.location.href = '/login';
                    }
                    ,error:function (error) {
                        console.log('error');
                        window.location.href = '/login';
                    }
                });
                layer.close(index);
            }
            ,btn2: function(index, layero){
                layer.close();
            }
        });
    }
</script>







<div class="market0 Balance0">
    <div>
        <div class="handler hand2">
            <div class="handl-btn">
                <i class="iconfont icon-less"></i>
                <span>@lang('ac.Balance')</span>
            </div>
            <div class="handl-right">
                <div>@lang('ac.Deposit')</div>
                {{--<div>Withdraw</div>--}}
            </div>
        </div>
        <div class="bigbox">
            <!--Balance-->
            <div>
                <div class="trade_table_th">
                    <span>@lang('ac.Currency')</span>
                    <span>@lang('ac.InTrades')</span>
                    <span>@lang('ac.Balance')</span>
                </div>
                <div class="lists-bit tr_right_blance trade_chart_tbody">
                    <ul>
                        {{--<li>--}}
                            {{--<div><i><img src="{{ asset('images/zec.png') }}"></i><span>ZEC</span></div>--}}
                            {{--<div>0.0000001</div>--}}
                            {{--<div>0.0000001</div>--}}
                        {{--</li>--}}
                        {{--<li>--}}
                            {{--<div><i><img src="{{ asset('images/btc.png') }}"></i><span>BTC</span></div>--}}
                            {{--<div>4.00311830</div>--}}
                            {{--<div>34.00311830</div>--}}
                        {{--</li>--}}
                    </ul>
                </div>
            </div>
            {{--@foreach($currList as $key => $value)--}}
                {{--{{$value->curr_abb}}--}}
            {{--@endforeach--}}
            <!--Deposit-->
            <div class="deposit-bit deposit2">
                <div class="manu2">
                    <div class="lists0">
                        <ul>
                            <li class="act3">BTC</li>
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
                                {{--<li><i><img src="{{ asset('images/bch.png') }}"></i><span>BCH</span></li>--}}
                                {{--<em></em>--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="deposit-box">
                    <div class="conten2">
                        <label>@lang('ac.Address'):</label>
                        <div class="copy0">
                            {{--<div class="number0 addre" id="copy0">--}}
                                {{--12EpNaVzhqbudGaDbncpsaujgLYEQLrcJRpsaujgLYEQ1LrcJR--}}
                            {{--</div>--}}
                            <input type="text" readonly class="number0 addre" id="copy0" style="background: transparent">
                            <div class="copybtn"><i class="iconfont icon-copy"></i></div>
                        </div>
                        <label>@lang('ac.QRcode')</label>
                        <div class="code0">
                            <div class="iconcode0"><img src="" class="code2"></div>
                            <div class="deli0">@lang('ac.ClickDeposit')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Withdraw-->
            <div class="withdraw-bit withdraw2">
                <div class="manu2">
                    <div class="lists0">
                        <ul>
                            <li class="act3">BTC</li>
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
                                {{--<li><i><img src="{{ asset('images/bch.png') }}"></i><span>BCH</span></li>--}}
                                {{--<em></em>--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="withdraw-box">
                    <div class="boxlist">
                        <div>
                            <div>@lang('ac.Balance'):</div>
                            <div class="balance">
                                <span>0.00</span><label>BTG</label>
                            </div>
                        </div>
                        <div>
                            <div>@lang('ac.Address'):</div>
                            <div class="address addre">
                                <input type="text" style="width: 100%; font-size: 12px; background: transparent;vertical-align:top;">
                            </div>
                        </div>
                        <div>
                            <div>@lang('ac.Amount'):</div>
                            <div class="amount"><input type="text" value="0.000"><label>BTG</label></div>
                        </div>
                        <div>
                            <div>@lang('ac.Fees'):</div>
                            <div class="fee">
                                <input type="text" value="0.000" readonly><label class="color_Fee">BTG</label>
                            </div>
                        </div>
                        <div>
                            <div>@lang('ac.Pin'):</div>
                            <div class="pin"><input type="password"></div>
                        </div>
                    </div>
                    <div class="withbtn" style="display: none;"><a>@lang('ac.Withdraw')</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
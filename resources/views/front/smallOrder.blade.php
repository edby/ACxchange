<div class="order0">
    <div>
        <div class="handler hand2">
            <div class="handl-btn">
                <i class="iconfont icon-less"></i>
                           <span class="select0">
                               <span class="acty">@lang('ac.Buy')</span>
                               <span>@lang('ac.Sell')</span>
                           </span>
            </div>
        </div>
        <div class="bigbox">
            <div class="buybox">
                <div class="orderlist">
                    <div>
                        <label>@lang('ac.Amount'):</label>
                        <div class="casket"><input type="text" name="amount" value="0.00000000" autocomplete="off"><span class="currency">BCH</span></div>
                    </div>
                    <div class="buy_pri">
                        <div class="bid">
                            <label>@lang('ac.Bid')</label> /
                            <a class="chang"><b>@lang('ac.Last')</b><i class="iconfont icon-xiala"></i></a>
                            <div class="lists2">
                                <ul>
                                    <li>@lang('ac.Last')</li>
                                    <li>@lang('ac.Bid')</li>
                                    <li>@lang('ac.Ask')</li>
                                </ul>
                            </div>
                        </div>
                        <div class="casket"><input type="text" name="price" value="0.00000000" autocomplete="off"><span class="tradeCurr">BTC</span></div>
                    </div>
                    <div class="order_price" style="display: none" >
                        <label>@lang('ac.Total')</label>
                        <div class="casket"><input type="text" name="total" value="0.00000000" readonly><span class="tradeCurr">BTC</span></div>
                    </div>

                    <div class="order_payment">
                        <label>@lang('ac.NetTotal')</label>
                        <div class="casket"><input type="text" name="payment" value="0.00000000" readonly><span class="tradeCurr">BTC</span></div>
                    </div>

                    <div class="order_fee" >
                        <label>@lang('ac.Fees')(<i class="buyfee0">0.2%</i>)</label>
                        <div class="casket noborder"><input type="text" name="fee" value="0.00000000" readonly><span class="currency">BCH</span></div>
                    </div>

                    <div class="order_received">
                        <label>@lang('ac.Total'):</label>
                        <div class="casket noborder"><input type="text" name="received" value="0.00000000" readonly><span class="currency">BCH</span></div>
                    </div>

                    <div class="order_net" style="display: none">
                        <label>@lang('ac.Total'):</label>
                        <div class="casket noborder"><input type="text" name="netTotal" value="0.00000000" readonly><span class="tradeCurr">BTC</span></div>
                    </div>
                </div>
                <div class="orderbtn">
                    <div><a class="buy0">@lang('ac.Buy')</a></div>
                </div>
            </div>
            <div class="sellbox">
                <div class="orderlist">
                    <div>
                        <label>@lang('ac.Amount'):</label>
                        <div class="casket"><input type="text" name="amount" value="0.00000000" autocomplete="off"><span class="currency">BCH</span></div>
                    </div>
                    <div class="sell_pri">
                        <div class="bid">
                            <label>@lang('ac.Ask')</label> /
                            <a class="chang"><b>@lang('ac.Last')</b><i class="iconfont icon-xiala"></i></a>
                            <div class="lists2">
                                <ul>
                                    <li>@lang('ac.Last')</li>
                                    <li>@lang('ac.Bid')</li>
                                    <li>@lang('ac.Ask')</li>
                                </ul>
                            </div>
                        </div>
                        <div class="casket"><input type="text" name="price" value="0.00000000" autocomplete="off"><span class="tradeCurr">BTC</span></div>
                    </div>
                    <div class="sell_price">
                        <label>@lang('ac.NetTotal'):</label>
                        <div class="casket"><input type="text" name="total" value="0.00000000" readonly><span class="tradeCurr">BTC</span></div>
                    </div>
                    <div class="sell_fee">
                        <label>@lang('ac.Fees')(<i class="sellfee0">0.2%</i>)</label>
                        <div class="casket noborder"><input type="text" name="fee" value="0.00000000" readonly><span class="tradeCurr">BTC</span></div>
                    </div>
                    <div class="sell_net">
                        <label>@lang('ac.Total'):</label>
                        <div class="casket noborder"><input type="text" name="netTotal" value="0.00000000" readonly><span class="tradeCurr">BTC</span></div>
                    </div>
                </div>
                <div class="orderbtn">
                    <div><a class="sell0">@lang('ac.Sell')</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

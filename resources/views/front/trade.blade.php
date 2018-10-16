@extends('layouts.app')
@section('content')
    <!--trade内容部分-->
    <div id="trade">
        <div class="content0">
            <div class="content-left">
                <h2 class="title0"><span class="bit-1">Bitcoin (BTC)</span> / <span class="bit-2">Bitcoin Cash(BCH)</span></h2>
                <div class="details">
                    <div>
                        <span>@lang('ac.lastPrice')</span><strong class="lastPrice">12.12345678</strong>
                    </div>
                    <div>
                        <span>@lang('ac.Low24HR')</span><strong class="low">11.10000008</strong>
                    </div>
                    <div>
                        <span>@lang('ac.High24HR')</span><strong class="high">11.10000008</strong>
                    </div>
                    <div>
                        <span>@lang('ac.Change24HR')</span><strong class="status"><b class="change">12.34%</b></strong>
                    </div>
                    <div>
                        <span>@lang('ac.Volume24HR')</span><strong><b class="volume">1.23456789M</b><a class="curr"> BTC</a></strong>
                    </div>
                </div>
                <div class="chart-box bigbox">
                    <div class="trade_tubiao_box">
                        <div  class="trade_tubiao">
                            <div class="handler hand0">
                                <div class="handl-btn">
                                    <i class="iconfont icon-less"></i><span>@lang('ac.Chart')</span>
                                </div>
                                <div class="handl-right">
                                    <div>
                                        <ul style="margin-right: 12px;">
                                            <li><i class="iconfont"></i></li>

                                            <li style="visibility: hidden;"><a href="javascript:void(0)" id="c1W" data-time="604800" data-size="100" class="btn btn-sm btn-default candleget">@lang('ac.1W')</a></li>
                                            <li style="visibility: hidden;"><a href="javascript:void(0)" id="c1D" data-time="86400" data-size="100" class="btn btn-sm btn-default candleget">@lang('ac.1D')</a></li>
                                            <li style="visibility: hidden;"><a href="javascript:void(0)" id="c12H" data-time="43200" data-size="100" class="btn btn-sm btn-default candleget">@lang('ac.12H')</a></li>
                                            <li><a href="javascript:void(0)" id="c6H" data-time="21600" data-size="100" class="btn btn-sm btn-default candleget">@lang('ac.6H')</a></li>
                                            <li><a href="javascript:void(0)" id="c2H" data-time="7200" data-size="100" class="btn btn-sm btn-default candleget">@lang('ac.2H')</a></li>
                                            <li class="activ0"><a href="javascript:void(0)" id="c1H" data-time="3600" data-size="100" class="btn btn-sm btn-default candleget">@lang('ac.1H')</a></li>
                                            <li><a href="javascript:void(0)" id="c30m" data-time="1800" data-size="100" class="btn btn-sm btn-default candleget">@lang('ac.30m')</a></li>
                                            <li ><a href="javascript:void(0)" id="c15m" data-time="900" data-size="100" class="btn btn-sm btn-nov candleget">@lang('ac.x15m')</a></li>
                                            <li ><a href="javascript:void(0)" id="c5m" data-time="300" data-size="100" class="btn btn-sm btn-nov candleget">@lang('ac.x5m')</a></li>
                                            <li><a href="javascript:void(0)" id="c1m" data-time="60" data-size="100" class="btn btn-sm btn-nov candleget">@lang('ac.x1m')</a></li>
                                            <li style="text-align: right;"><i class="iconfont"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="bigbox">
                                <div class="panel panel-default wow fadeInDownBig" style="border:0;">
                                    <div class="panel-heading chart" style="text-align:left; font-size:18px; border:0;">
                                        {{--<ul class="ohlc">--}}
                                            {{--<li>--}}
                                                {{--<span class="title">O</span>--}}
                                                {{--<span class="open">0.00000004</span>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<span class="title">L</span>--}}
                                                {{--<span class="high">0.00000003</span>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<span class="title">H</span>--}}
                                                {{--<span class="high">0.11454654</span>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<span class="title">C</span>--}}
                                                {{--<span class="low">0.11454654</span>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<span class="title">A</span>--}}
                                                {{--<span class="clos">0.11454654</span>--}}
                                            {{--</li>--}}
                                        {{--</ul>--}}
                                    </div>
                                    <div class="panel-body">
                                        <div id="chartdiv" style="width:100%; height:402px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="sellorbuy">
                    <div class="sell2 wow fadeInLeft">
                        <div>
                            <div class="handler hand3">
                                <div class="handl-btn">
                                    <i class="iconfont icon-less"></i><span>@lang('ac.sellOrders')</span>
                                </div>
                                <div class="handl-right">
                                    <div class="total2">
                                        <a>@lang('ac.Total'):</a><span class="totalNum"> </span><a>BTC</a>
                                    </div>
                                </div>
                            </div>
                            <div class="bigbox trade_chart_box">
                                <table>
                                    <thead class="trade_chart_thead">
                                    <tr class="title0"><th>@lang('ac.ASKBTC')</th><th>@lang('ac.Amount') (<span class="curr_abb">RPZ</span>)</th><th>@lang('ac.ValueBTC')</th><th>@lang('ac.TotalBTC')</th></tr>
                                    </thead>
                                    <tbody class="trade_chart_tbody sellOrders">
                                    {{--<tr><td>1234.00311830</td><td>6.70000001</td><td>1274.00311830</td><td>0234.003118</td></tr>--}}
                                    {{--<tr class="actsell"><td>91234.003118</td><td>6.00008001</td><td>9934.0031189</td><td>6734.00311888</td></tr>--}}
                                    {{--<tr><td>4234.00311830</td><td>7.0070001</td><td>8234.0031190</td><td>934.00319</td></tr>--}}
                                    {{--<tr><td>1534.0031183</td><td>900009001</td><td>9234.0039830</td><td>034.00311930</td></tr>--}}
                                    {{--<tr><td>5534.00311830</td><td>0.090009</td><td>734.0031997</td><td>734.0031187</td></tr>--}}
                                    {{--<tr><td>334.003118</td><td>0.7000001</td><td>1234.070311830</td><td>734.00311730</td></tr>--}}
                                    {{--<tr><td>4234.00311830</td><td>7.0000001</td><td>1234.00311830</td><td>834.00311830</td></tr>--}}
                                    {{--<tr><td>1334.00311</td><td>90000001</td><td>194.003190</td><td>194.0038830</td></tr>--}}
                                    {{--<tr><td>4234.0031183</td><td>8008891</td><td>184.00311830</td><td>1234.00811830</td></tr>--}}
                                    {{--<tr><td>5234.0031183</td><td>889000001</td><td>94.0031190</td><td>94.00311930</td></tr>--}}
                                    {{--<tr><td>6234.00311835</td><td>997000001</td><td>17734.00376</td><td>78.0031189</td></tr>--}}
                                    {{--<tr><td>7234.00311</td><td>8900001</td><td>17800311830</td><td>1934.003119</td></tr>--}}
                                    {{--<tr><td>8234.0031183</td><td>880000005</td><td>154.0031150</td><td>534.0031150</td></tr>--}}
                                    {{--<tr><td>9234.0031183</td><td>56.00070801</td><td>84.0036830</td><td>64.0036830</td></tr>--}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="buy2 wow fadeInRight">
                        <div>
                            <div class="handler hand3">
                                <div class="handl-btn">
                                    <i class="iconfont icon-less"></i><span>@lang('ac.BuyOrders')</span>
                                </div>
                                <div class="handl-right">
                                    <div class="total2">
                                        <a>@lang('ac.Total'):</a><span class="totalNum"> </span><a>BTC</a>
                                    </div>
                                </div>
                            </div>
                            <div class="bigbox trade_chart_box">
                                <table>
                                    <thead class="trade_chart_thead">
                                    <tr class="title0"><th>@lang('ac.BIDBTC')</th><th>@lang('ac.Amount') (<span class="curr_abb">BCH</span>)</th><th>@lang('ac.ValueBTC')</th><th>@lang('ac.TotalBTC')</th></tr>
                                    </thead>
                                    <tbody class="trade_chart_tbody buyOrders">
                                    {{--<tr class="actbuy"><td>164.00311830</td><td>0.05601</td><td>164.635830</td><td>1264.05360</td></tr>--}}
                                    {{--<tr><td>16.003118</td><td>0.0666001</td><td>1234.00730</td><td>17.061830</td></tr>--}}
                                    {{--<tr><td>1564.00311630</td><td>0.07601</td><td>94.006830</td><td>12394.007918</td></tr>--}}
                                    {{--<tr><td>1534.005183</td><td>0.00600001</td><td>127.00311730</td><td>1274.0071830</td></tr>--}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--添加的表格-->
                <div class="sellorbuy addtable">
                    <div class="sell2 wow fadeInLeft">
                        <div>
                            <div class="handler hand3">
                                <div class="handl-btn">
                                    <i class="iconfont icon-less"></i><span>@lang('ac.MyOpenOrders')</span>
                                </div>
                            </div>
                            <div class="bigbox trade_chart_box">
                                <table>
                                    <thead class="trade_chart_thead">
                                    <tr class="title0">
                                        <th>@lang('ac.Timestamp')</th>
                                        <th>@lang('ac.InitialVol')</th>
                                        <th>@lang('ac.ResidualVol')</th>
                                        <th>@lang('ac.Type')</th>
                                        <th>@lang('ac.Price')</th>
                                        <th>@lang('ac.Operation')</th>
                                    </tr>
                                    </thead>
                                    <tbody class="trade_chart_tbody">
                                    {{--<tr><td>2018-1-12 17:30:30</td><td>15,678.12561</td><td>45,678.123678</td><td>0.0070001</td><td class="buycolor">Buy</td><td class="buycolor">13457789</td><td>Cancel</td></tr>--}}
                                    {{--<tr><td>2018-1-11 16:30:31</td><td>25,678.134562</td><td>15,678.125677</td><td>0.00070001</td><td class="sellcolor">Sell</td><td class="sellcolor">223756789</td><td>Cancel</td></tr>--}}
                                    {{--<tr><td>2018-1-13 18:30:32</td><td>35,678.1234563</td><td>25,678.123678</td><td>0.0050005</td><td class="sellcolor">Sell</td><td class="sellcolor">1756788</td><td>Cancel</td></tr>--}}
                                    {{--<tr><td>2018-1-14 19:30:33</td><td>44,678.134564</td><td>35,678.123675</td><td>0.000054003</td><td class="buycolor">Buy</td><td class="buycolor">42366789</td><td>Cancel</td></tr>--}}
                                    {{--<tr><td>2018-1-15 20:30:34</td><td>55,678.1234565</td><td>45,678.125678</td><td>0.000400001</td><td class="sellcolor">Sell</td><td class="sellcolor">5656786</td><td>Cancel</td></tr>--}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="buy2 wow fadeInRight">
                        <div>
                            <div class="handler hand3">
                                <div class="handl-btn">
                                    <i class="iconfont icon-less"></i><span>@lang('ac.MarketHistory')</span>
                                </div>
                            </div>
                            <div class="bigbox trade_chart_box">
                                <table>
                                    <thead class="trade_chart_thead">
                                    <tr class="title0">
                                        <th>@lang('ac.Timestamp')</th>
                                        {{--<th>@lang('ac.Type')</th>--}}
                                        <th>@lang('ac.PriceBTC')</th>
                                        <th>@lang('ac.Volume') (<span class="curr_abb">BTC</span>)</th>
                                        <th>@lang('ac.VolumeBTC')</th>
                                        <th>@lang('ac.ValueBTC')</th>
                                    </tr>
                                    </thead>
                                    <tbody class="trade_chart_tbody marketHistory">
                                    {{--<tr><td>2018-1-1 12:30:30</td><td class="buycolor">Buy</td><td class="buycolor">0.00098902</td><td>176.00008000</td><td>0.173078</td><td>0.173074</td></tr>--}}
                                    {{--<tr><td>2018-1-2 13:30:31</td><td class="sellcolor">Sell</td><td class="buycolor">1.00098903</td><td>165.00090000</td><td>2.173075</td><td>2.173075</td></tr>--}}
                                    {{--<tr><td>2018-1-3 14:30:32</td><td class="buycolor">Buy</td><td class="buycolor">2.00098904</td><td>175.00090000</td><td>0.173076</td><td>4.173075</td></tr>--}}
                                    {{--<tr><td>2018-1-4 15:30:33</td><td class="sellcolor">Sell</td><td class="buycolor">3.00098905</td><td>475.00300000</td><td>4.173074</td><td>7.173076</td></tr>--}}
                                    {{--<tr><td>2018-1-5 16:30:34</td><td class="buycolor">Buy</td><td class="buycolor">4.00098906</td><td>575.0003000</td><td>5.173075</td><td>8.173077</td></tr>--}}
                                    {{--<tr><td>2018-1-6 17:30:35</td><td class="sellcolor">Sell</td><td class="buycolor">5.00098907</td><td>675.00600000</td><td>7.17305</td><td>4.173078</td></tr>--}}
                                    {{--<tr><td>2018-1-7 18:30:36</td><td class="buycolor">Buy</td><td class="buycolor">6.00098908</td><td>275.00008000</td><td>8.1730757</td><td>0.173079</td></tr>--}}
                                    {{--<tr><td>2018-1-8 19:30:37</td><td class="sellcolor">Sell</td><td class="buycolor">0.00098909</td><td>175.00500000</td><td>9.17307</td><td>6.173070</td></tr>--}}
                                    {{--<tr><td>2018-1-9 20:30:38</td><td class="buycolor">Buy</td><td class="buycolor">1.00098901</td><td>172.00008000</td><td>0.473077</td><td>0.1730751</td></tr>--}}
                                    {{--<tr><td>2018-1-10 21:30:39</td><td class="sellcolor">Sell</td><td class="buycolor">2.00098902</td><td>275.00700000</td><td>1.17375</td><td>7.173072</td></tr>--}}
                                    {{--<tr><td>2018-1-11 22:30:33</td><td class="buycolor">Buy</td><td class="buycolor">4.00098903</td><td>174.0005000</td><td>2.17575</td><td>8.173073</td></tr>--}}
                                    {{--<tr><td>2018-1-12 23:30:34</td><td class="sellcolor">Sell</td><td class="buycolor">5.00098904</td><td>575.0080000</td><td>3.175075</td><td>9.173074</td></tr>--}}
                                    {{--<tr><td>2018-1-13 24:30:35</td><td class="buycolor">Buy</td><td class="buycolor">6.00098904</td><td>155.00000500</td><td>04.177075</td><td>0.173075</td></tr>--}}
                                    {{--<tr><td>2018-1-14 10:30:36</td><td class="sellcolor">Sell</td><td class="buycolor">8.00098905</td><td>175.00030000</td><td>5.12075</td><td>2.173076</td></tr>--}}
                                    {{--<tr><td>2018-1-15 11:30:37</td><td class="buycolor">Buy</td><td class="buycolor">2.00098906</td><td>145.0000300</td><td>6.173375</td><td>0.273077</td></tr>--}}
                                    {{--<tr><td>2018-1-16 12:30:38</td><td class="sellcolor">Sell</td><td class="buycolor">3.00098907</td><td>175.00060000</td><td>7.13075</td><td>3.173078</td></tr>--}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--右边内容-->
            <div class="content-right">
                @component('front.smallMarket')@endcomponent
                @component('front.smallOrder')@endcomponent
                @component('front.smallBalance')@endcomponent
            </div>
        </div>
        <i class="iconfont icon-caidan" id="caidan"></i>
    </div>
@endsection

@extends('layouts.app')
@section('content')
        <!--order内容部分-->
<div id="trade">
    <div class="content0">
        <div class="content-left">
            <div class="chart-box">
                <div class="wow fadeInDown">
                    <div class="handler hand0">
                        <div class="handl-btn">
                            <i class="iconfont icon-less"></i><span class="table_name">@lang("ac.Open_orders")</span>
                        </div>
                    </div>
                    <div class="bigbox bigbox_in_order">
                        <div>
                            <table class="order_table">
                                <thead class="order_head">
                                <tr>
                                    <th>@lang('ac.Timestamp')</th>
                                    <th class="openbtn">
                                        <span>All</span>
                                        <i class="iconfont icon-xiala">
                                            <div class="marketSelect">
                                                <ul>
                                                </ul>
                                                <em></em>
                                            </div>
                                        </i>
                                    </th>
                                    <th>@lang('ac.InitialVolume')</th>
                                    <th>@lang('ac.ResidualVolume')</th>
                                    <th>@lang('ac.Type')</th>
                                    <th>@lang('ac.Price')</th>
                                    <th>@lang('ac.Operation')</th>
                                </tr>
                                </thead>
                                <tbody class="order_body">
                                {{--<tr>--}}
                                {{--<td>2018-04-14 15:11:32</td>--}}
                                {{--<td><i><img src="{{ asset("images/btc.png") }}"></i><span>BTC</span></td>--}}
                                {{--<td>1.12205000</td>--}}
                                {{--<td>1.12234556</td>--}}
                                {{--<td>1234567890</td>--}}
                                {{--<td class="type_buy">Buy</td>--}}
                                {{--<td class="price_buy">1234567</td>--}}
                                {{--<td class="cancel2">Cancel</td>--}}
                                {{--</tr>--}}
                                {{--<tr>--}}
                                {{--<td>2018-04-14 15:11:32</td>--}}
                                {{--<td><i><img src="{{ asset("images/btc.png") }}"></i><span>BTC</span></td>--}}
                                {{--<td>1.12205000</td>--}}
                                {{--<td>1.12234556</td>--}}
                                {{--<td>1234567890</td>--}}
                                {{--<td class="type_sell">Sell</td>--}}
                                {{--<td class="price_sell">7654321</td>--}}
                                {{--<td class="cancel2">Cancel</td>--}}
                                {{--</tr>--}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--order history-->
                <div class="wow fadeInUp" style="margin-top: 20px;">
                    <div class="handler hand0">
                        <div class="handl-btn">
                            <i class="iconfont icon-less"></i><span class="table_name">@lang('ac.OrderHistory')</span>
                        </div>
                    </div>
                    <div class="bigbox bigbox_in_order">
                        <div>
                            <table class="history_table">
                                <thead class="history_head">
                                <tr>
                                    <th>@lang('ac.Timestamp')</th>
                                    <th class="historybtn">
                                        <span>ALL</span>
                                        <i class="iconfont icon-xiala">
                                            <div class="marketSelect">
                                                <ul>
                                                    {{--<li>BCH</li>--}}
                                                    {{--<li>LTC</li>--}}
                                                    {{--<li>RPZ</li>--}}
                                                    {{--<li>XVG</li>--}}
                                                    {{--<li>BTG</li>--}}
                                                </ul>
                                                <em></em>
                                            </div>
                                        </i>
                                    </th>
                                    <th>@lang('ac.InitialVolume')</th>
                                    <th>@lang('ac.ResidualVolume')</th>
                                    <th>@lang('ac.Type')</th>
                                    <th>@lang('ac.Price')</th>
                                    <th>@lang('ac.Fees')</th>
                                    <th>@lang('ac.Status')</th>
                                </tr>
                                <tbody class="history_body">
                                {{--<tr>--}}
                                {{--<td>2018-04-14 14:14:14</td>--}}
                                {{--<td><i><img src="{{ asset("images/zec.png") }}"></i><span>ZEC</span></td>--}}
                                {{--<td>0.1234567</td>--}}
                                {{--<td class="type_buy">Buy</td>--}}
                                {{--<td class="price_buy">1234567</td>--}}
                                {{--<td>12345678</td>--}}
                                {{--</tr>--}}
                                {{--<tr>--}}
                                {{--<td>2018-04-14 14:14:14</td>--}}
                                {{--<td><i><img src="{{ asset("images/zec.png") }}"></i><span>ZEC</span></td>--}}
                                {{--<td>0.1234567</td>--}}
                                {{--<td class="type_sell">Sell</td>--}}
                                {{--<td class="price_sell">7654321</td>--}}
                                {{--<td>12345678</td>--}}
                                {{--</tr>--}}
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
            @component('front.smallBalance',['currList'=>$currList])@endcomponent
        </div>
    </div>
    <i class="iconfont icon-caidan" id="caidan"></i>
</div>
@endsection


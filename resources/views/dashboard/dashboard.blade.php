@extends('dashboard.layout.app')
@section('content')
    <section class="content">
        <div class="dashboard">
            <div class="titl">Dashboard</div>
            <div class="balance">
                <div class="box">
                    <h5>BTC FLOAT BALANCE</h5>
                    <div><img src="{{asset('dashboard/dist/img/1.png')}}"></div>
                    <div class="nums">BTC:<span>{{$data['BTC']['balance']}}</span></div>
                    <div class="cods">{{$data['BTC']['address']}}</div>
                    <div class="bigs">
                    <div class="listflex btcflex">
                        <div><img src="{{asset('dashboard/dist/img/1-1.png')}}"></div>
                        <div class="right0">
                            <h6>BTC WITHDRAW BALANCE</h6>
                            <div>BTC:<span>{{$data['BTC']['withdraw']}}</span></div>
                        </div>
                    </div>
                    <div class="listflex btcflex">
                        <div><img src="{{asset('dashboard/dist/img/1-1.png')}}"></div>
                        <div class="right0">
                            <h6>BTC SYSTEM NODE BALANCE</h6>
                            <div>BTC:<span>{{$data['BTC']['node']}}</span></div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="box">
                    <h5>LTC FLOAT BALANCE</h5>
                    <div><img src="{{asset('dashboard/dist/img/2.png')}}"></div>
                    <div class="nums">LTC:<span>{{$data['LTC']['balance']}}</span></div>
                    <div class="cods">{{$data['LTC']['address']}}</div>
                    <div class="bigs">
                            <div class="listflex ltcflex">
                                    <div><img src="{{asset('dashboard/dist/img/2-1.png')}}"></div>
                                    <div class="right0">
                                        <h6>LTC WITHDRAW BALANCE</h6>
                                        <div>LTC:<span>{{$data['LTC']['withdraw']}}</span></div>
                                    </div>
                                </div>
                                <div class="listflex ltcflex">
                                    <div><img src="{{asset('dashboard/dist/img/2-1.png')}}"></div>
                                    <div class="right0">
                                        <h6>LTC SYSTEM NODE BALANCE</h6>
                                        <div>LTC:<span>{{$data['LTC']['node']}}</span></div>
                                    </div>
                                </div>
                    </div>
                </div>
                <div class="box">
                    <h5>BCH FLOAT BALANCE</h5>
                    <div><img src="{{asset('dashboard/dist/img/3.png')}}"></div>
                    <div class="nums">BCH:<span>{{$data['BCH']['balance']}}</span></div>
                    <div class="cods">{{$data['BCH']['address']}}</div>
                    <div class="bigs">
                    <div class="listflex bchflex">
                        <div><img src="{{asset('dashboard/dist/img/3-1.png')}}"></div>
                        <div class="right0">
                            <h6>BCH WITHDRAW BALANCE</h6>
                            <div>BCH:<span>{{$data['BCH']['withdraw']}}</span></div>
                        </div>
                    </div>
                    <div class="listflex bchflex">
                        <div><img src="{{asset('dashboard/dist/img/3-1.png')}}"></div>
                        <div class="right0">
                            <h6>BCH SYSTEM NODE BALANCE</h6>
                            <div>BCH:<span>{{$data['BCH']['node']}}</span></div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="box">
                    <h5>RPZ FLOAT BALANCE</h5>
                    <div><img src="{{asset('dashboard/dist/img/4.png')}}"></div>
                    <div class="nums">RPZ:<span>{{$data['RPZ']['balance']}}</span></div>
                    <div class="cods">{{$data['RPZ']['address']}}</div>
                    <div class="bigs">
                        <div class="listflex rpzflex">
                            <div><img src="{{asset('dashboard/dist/img/4-1.png')}}"></div>
                            <div class="right0">
                                <h6>RPZ WITHDRAW BALANCE</h6>
                                <div>RPZ:<span>{{$data['RPZ']['withdraw']}}</span></div>
                            </div>
                        </div>
                        <div class="listflex rpzflex">
                            <div><img src="{{asset('dashboard/dist/img/4-1.png')}}"></div>
                            <div class="right0">
                                <h6>RPZ SYSTEM NODE BALANCE</h6>
                                <div>RPZ:<span>{{$data['RPZ']['node']}}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="balance">
                <div class="box">
                    <h5>XVG FLOAT BALANCE</h5>
                    <div><img src="{{asset('dashboard/dist/img/XVG.png')}}"></div>
                    <div class="nums">XVG:<span>{{$data['XVG']['balance']}}</span></div>
                    <div class="cods">{{$data['XVG']['address']}}</div>
                    <div class="bigs">
                        <div class="listflex xvgflex">
                            <div><img src="{{asset('dashboard/dist/img/5-1.png')}}"></div>
                            <div class="right0">
                                <h6>XVG WITHDRAW BALANCE</h6>
                                <div>XVG:<span>{{$data['XVG']['withdraw']}}</span></div>
                            </div>
                        </div>
                        <div class="listflex xvgflex">
                            <div><img src="{{asset('dashboard/dist/img/5-1.png')}}"></div>
                            <div class="right0">
                                <h6>XVG SYSTEM NODE BALANCE</h6>
                                <div>XVG:<span>{{$data['XVG']['node']}}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <h5>BTG FLOAT BALANCE</h5>
                    <div><img src="{{asset('dashboard/dist/img/BTG.png')}}"></div>
                    <div class="nums">BTG:<span>{{$data['BTG']['balance']}}</span></div>
                    <div class="cods">{{$data['BTG']['address']}}</div>
                    <div class="bigs">
                        <div class="listflex btgflex">
                            <div><img src="{{asset('dashboard/dist/img/6-1.png')}}"></div>
                            <div class="right0">
                                <h6>BTG WITHDRAW BALANCE</h6>
                                <div>BTG:<span>{{$data['BTG']['withdraw']}}</span></div>
                            </div>
                        </div>
                        <div class="listflex btgflex">
                            <div><img src="{{asset('dashboard/dist/img/6-1.png')}}"></div>
                            <div class="right0">
                                <h6>BTG SYSTEM NODE BALANCE</h6>
                                <div>BTG:<span>{{$data['BTG']['node']}}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <h5>DASH FLOAT BALANCE</h5>
                    <div><img src="{{asset('dashboard/dist/img/DASH.png')}}"></div>
                    <div class="nums">DASH:<span>{{$data['DASH']['balance']}}</span></div>
                    <div class="cods">{{$data['DASH']['address']}}</div>
                    <div class="bigs">
                        <div class="listflex dashflex">
                            <div><img src="{{asset('dashboard/dist/img/7-1.png')}}"></div>
                            <div class="right0">
                                <h6>DASH WITHDRAW BALANCE</h6>
                                <div>DASH:<span>{{$data['DASH']['withdraw']}}</span></div>
                            </div>
                        </div>
                        <div class="listflex dashflex">
                            <div><img src="{{asset('dashboard/dist/img/7-1.png')}}"></div>
                            <div class="right0">
                                <h6>DASH SYSTEM NODE BALANCE</h6>
                                <div>DASH:<span>{{$data['DASH']['node']}}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box"></div>
            </div>
            <div class="outgoing">
                {{--左边列--}}
                <div class="goingleft list0">
                    <div class="boss">
                        <div class="tuwen"><img src="{{asset('dashboard/dist/img/a.png')}}"><span>TODAY’S BTC OUTGOING</span></div>
                        <div class="shuju">
                            <table>
                                <thead>
                                <tr><th>NO</th><th>User ID</th><th>Amount</th><th>Date</th></tr>
                                </thead>

                                <tbody>
                                @if(isset($outGoing['BTC'][0]))
                                    @foreach($outGoing['BTC'] as $list)
                                        @if(is_numeric($key))
                                            <tr>
                                                <td>
                                                    {{$list['no']}}
                                                </td>
                                                <td>{{$list['user_id']}}</td>
                                                <td>
                                                    {{$list['amount']}}
                                                </td>
                                                <td>
                                                    {{$list['date']}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><div class="no-transfer">no transfer</div></td></tr>
                                @endif
                                </tbody>
                                <tfoot>
                                <tr><th>Total</th><td></td><td>{{$outGoing['BTC']['total']}}</td><td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="boss">
                        <div class="tuwen"><img src="{{asset('dashboard/dist/img/c.png')}}"><span>TODAY’S LTC OUTGOING</span></div>
                        <div class="shuju">
                            <table>
                                <thead>
                                <tr><th>NO</th><th>User ID</th><th>Amount</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                @if(isset($outGoing['LTC'][0]))
                                    @foreach($outGoing['LTC'] as $list)
                                        @if(is_numeric($key))
                                            <tr>
                                                <td>
                                                    {{$list['no']}}
                                                </td>
                                                <td>{{$list['user_id']}}</td>
                                                <td>
                                                    {{$list['amount']}}
                                                </td>
                                                <td>
                                                    {{$list['date']}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><div class="no-transfer">no transfer</div></td></tr>
                                @endif
                                </tbody>
                                <tfoot>
                                <tr><th>Total</th><td></td><td>{{$outGoing['LTC']['total']}}</td><td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="boss">
                        <div class="tuwen"><img src="{{asset('dashboard/dist/img/xvg0.png')}}"><span>TODAY’S XVG OUTGOING</span></div>
                        <div class="shuju">
                            <table>
                                <thead>
                                <tr><th>NO</th><th>User ID</th><th>Amount</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                @if(isset($outGoing['XVG'][0]))
                                    @foreach($outGoing['XVG'] as $list)
                                        @if(is_numeric($key))
                                            <tr>
                                                <td>
                                                    {{$list['no']}}
                                                </td>
                                                <td>{{$list['user_id']}}</td>
                                                <td>
                                                    {{$list['amount']}}
                                                </td>
                                                <td>
                                                    {{$list['date']}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><div class="no-transfer">no transfer</div></td></tr>
                                @endif
                                </tbody>
                                <tfoot>
                                <tr><th>Total</th><td></td><td>{{$outGoing['XVG']['total']}}</td><td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="boss">
                        <div class="tuwen"><img src="{{asset('dashboard/dist/img/btg0.png')}}"><span>TODAY’S BTG OUTGOING</span></div>
                        <div class="shuju">
                            <table>
                                <thead>
                                <tr><th>NO</th><th>User ID</th><th>Amount</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                @if(isset($outGoing['BTG'][0]))
                                    @foreach($outGoing['BTG'] as $key=>$list)
                                        @if(is_numeric($key))
                                            <tr>
                                                <td>
                                                    {{$list['no']}}
                                                </td>
                                                <td>{{$list['user_id']}}</td>
                                                <td>
                                                    {{$list['amount']}}
                                                </td>
                                                <td>
                                                    {{$list['date']}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><div class="no-transfer">no transfer</div></td></tr>
                                @endif
                                </tbody>
                                <tfoot>
                                <tr><th>Total</th><td></td><td>{{$outGoing['BTG']['total']}}</td><td></td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                {{--右边列--}}
                <div class="goingright list0">
                    <div class="boss">
                        <div class="tuwen"><img src="{{asset('dashboard/dist/img/b.png')}}"><span>TODAY’S BCH OUTGOING</span></div>
                        <div class="shuju">
                            <table>
                                <thead>
                                <tr><th>NO</th><th>User ID</th><th>Amount</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                @if(isset($outGoing['BCH'][0]))
                                    @foreach($outGoing['BCH'] as $list)
                                        @if(is_numeric($key))
                                            <tr>
                                                <td>
                                                    {{$list['no']}}
                                                </td>
                                                <td>{{$list['user_id']}}</td>
                                                <td>
                                                    {{$list['amount']}}
                                                </td>
                                                <td>
                                                    {{$list['date']}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><div class="no-transfer">no transfer</div></td></tr>
                                @endif
                                </tbody>
                                <tfoot>
                                <tr><th>Total</th><td></td><td>{{$outGoing['BCH']['total']}}</td><td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="boss">
                        <div class="tuwen"><img src="{{asset('dashboard/dist/img/d.png')}}"><span>TODAY’S RPZ OUTGOING</span></div>
                        <div class="shuju">
                            <table>
                                <thead>
                                <tr><th>NO</th><th>User ID</th><th>Amount</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                @if(isset($outGoing['RPZ'][0]))
                                    @foreach($outGoing['RPZ'] as $key=>$list)
                                        @if(is_numeric($key))
                                            <tr>
                                                <td>
                                                    {{$list['no']}}
                                                </td>
                                                <td>{{$list['user_id']}}</td>
                                                <td>
                                                    {{$list['amount']}}
                                                </td>
                                                <td>
                                                    {{$list['date']}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><div class="no-transfer">no transfer</div></td></tr>
                                @endif
                                </tbody>
                                <tfoot>
                                <tr><th>Total</th><td></td><td>{{$outGoing['RPZ']['total']}}</td><td></td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="boss">
                        <div class="tuwen"><img src="{{asset('dashboard/dist/img/dash0.png')}}"><span>TODAY’S DASH OUTGOING</span></div>
                        <div class="shuju">
                            <table>
                                <thead>
                                <tr><th>NO</th><th>User ID</th><th>Amount</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                @if(isset($outGoing['DASH'][0]))
                                    @foreach($outGoing['DASH'] as $list)
                                        @if(is_numeric($key))
                                            <tr>
                                                <td>
                                                    {{$list['no']}}
                                                </td>
                                                <td>{{$list['user_id']}}</td>
                                                <td>
                                                    {{$list['amount']}}
                                                </td>
                                                <td>
                                                    {{$list['date']}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><div class="no-transfer">no transfer</div></td></tr>
                                @endif
                                </tbody>
                                <tfoot>
                                <tr><th>Total</th><td></td><td>{{$outGoing['DASH']['total']}}</td><td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
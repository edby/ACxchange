@extends('layouts.app')
@section('content')
    <!--help内容部分-->
    <div id="trade">
        <div class="content0">
            <div class="content-left">
                <div class="chart-box">
                    <div>
                        <div class="handler hand0">
                            <div class="handl-btn">
                                <i class="iconfont icon-less"></i><span>@lang('ac.Help')</span>
                            </div>
                            <div class="menu-btn" style="">
                                <a><img src="{{ asset('images/left-btn.png') }}" alt="" /> </a>
                            </div>
                        </div>
                        <div class="bigbox help ">
                            <div class="menu-help">
                                <ul class="help-nav">
                                    <li>
                                        <a href="#faq">@lang('ac.FAQ')</a>
                                    </li>
                                    <li>
                                        <a href="#gettingstarted">@lang('ac.GettingStarted')</a>
                                    </li>
                                    <li>
                                        <a href="#orders">@lang('ac.OrdersTrades')</a>
                                    </li>
                                    <li class="acti">
                                        <a href="#withdrawal">@lang('ac.Withdrawal')</a>
                                    </li>
                                    <li>
                                        <a href="#deposit">@lang('ac.Deposit')</a>
                                    </li>
                                    <li>
                                        <a href="#2fa">2FA</a>
                                    </li>
                                </ul>
                                <ul class="help-nav-h">
                                    <li>
                                        <a href="#faq">@lang('ac.FAQ')</a>
                                    </li>
                                    <li>
                                        <a href="#gettingstarted">@lang('ac.GettingStarted')</a>
                                    </li>
                                    <li>
                                        <a href="#orders">@lang('ac.OrdersTrades')</a>
                                    </li>
                                    <li class="acti">
                                        <a href="#withdrawal">@lang('ac.Withdrawal')</a>
                                    </li>
                                    <li>
                                        <a href="#deposit">@lang('ac.Deposit')</a>
                                    </li>
                                    <li>
                                        <a href="#2fa">2FA</a>
                                    </li>
                                </ul>

                            </div>

                            <div class="help-content">
                                <h3>@lang('ac.FAQ')</h3>
                                <ul class="ques">
                                    <li>
                                        <a href="#1"><span>@lang('ac.newCoin')</span></a>
                                    </li>
                                    <li>
                                        <a href="#2"><span>@lang('ac.CanYouCurrency')</span></a>
                                    </li>
                                    <li>
                                        <a href="#3"><span>@lang('ac.WhatIsACxchange')</span></a>
                                    </li>
                                    <li>
                                        <a href="#4"><span>@lang('ac.AreProtectedACxchange')</span></a>
                                    </li>
                                    <li>
                                        <a href="#5"><span>@lang('ac.FeeUsingACxchange')</span></a>
                                    </li>
                                    <li>
                                        <a href="#6"><span>@lang('ac.balancesandtrades')</span></a>
                                    </li>
                                    <li>
                                        <a href="#7"><span>@lang('ac.SupportrequestTicket')</span></a>
                                    </li>
                                    <li>
                                        <a href="#8"><span>@lang('ac.registeredEmailAccount')</span></a>
                                    </li>
                                </ul>
                                <a name="1"></a>
                                <em class="para">@lang('ac.newCoin')</em>
                                <p>@lang('ac.untestedCurrency')</p>
                                <a name="2"></a>
                                <em class="para">@lang('ac.CanYouCurrency')</em>
                                <p>@lang('ac.ThesimpleanswerisYes')</p>
                                <a name="3"></a>
                                <em class="para">@lang('ac.WhatIsACxchange')</em>
                                <p>@lang('ac.ACxchangeIsCryptocurrency')</p>
                                <a name="4"></a>
                                <em class="para">@lang('ac.AreProtectedACxchange')</em>
                                <p>@lang('ac.AttackProofEncryption')</p>
                                <a name="5"></a>
                                <em class="para">@lang('ac.FeeUsingACxchange')</em>
                                <p>@lang('ac.ACxchangeFulfilsExchange')</p>
                                <a name="6"></a>
                                <em class="para">@lang('ac.balancesandtrades')</em>
                                <p>@lang('ac.InOrderCalculations')</p>
                                <a name="7"></a>
                                <em class="para">@lang('ac.SupportrequestTicket')</em>
                                <p>@lang('ac.UsersAndTheirSecurity')</p>
                                <a name="8"></a>
                                <em class="para">@lang('ac.registeredEmailAccount')</em>
                                <p>@lang('ac.visitingthispage')</p>
                                <br/>
                                {{--<p class="help-contact" style="padding-bottom: 200px"><a href="{{url('/help/contactUs')}}">{{url('/help/contactUs')}}</a></p>--}}
                            </div>

                            <div class="help-content">
                                <h3>@lang('ac.GettingStarted')</h3>
                                <ul class="ques">
                                    <li>
                                        <a href="#9"><span>@lang('ac.WhatIsACryptocurrency')</span></a>
                                    </li>
                                    <li>
                                        <a href="#10"><span>@lang('ac.theConnectedPeers')</span></a>
                                    </li>
                                    <li>
                                        <a href="#11"><span>@lang('ac.newBlocksMeans')</span></a>
                                    </li>
                                    <li>
                                        <a href="#12"><span>@lang('ac.legaltousecryptocurrencie')</span></a>
                                    </li>
                                    <li>
                                        <a href="#13"><span>@lang('ac.accountAndWallet')</span></a>
                                    </li>
                                    <li>
                                        <a href="#14"><span>@lang('ac.myregisteredemailandpassword')</span></a>
                                    </li>
                                    <li>
                                        <a href="#16"><span>@lang('ac.NewsAndImportantUpdates')</span></a>
                                    </li>
                                </ul>
                                <a name="9"></a>
                                <em class="para">@lang('ac.WhatIsACryptocurrency')</em>
                                <p>@lang('ac.Cryptocurrencyisadigitalform')</p>
                                <a name="10"></a>
                                <em class="para">@lang('ac.theConnectedPeers')</em>
                                <p>@lang('ac.Apeeristhedirectconnectivity')</p>
                                <a name="11"></a>
                                <em class="para">@lang('ac.newBlocksMeans')</em>
                                <p>@lang('ac.Miningmeansthatacomputerhardware')</p>
                                <a name="12"></a>
                                <em class="para">@lang('ac.legaltousecryptocurrencie')</em>
                                <p>@lang('ac.ACxchangeisanexchangeplatform')</p>
                                <a name="13"></a>
                                <em class="para">@lang('ac.accountandwallet')</em>
                                <p>@lang('ac.Tominimisetheprobabilityofelectronic')</p>
                                <a name="14"></a>
                                <em class="para">@lang('ac.myregisteredemailandpassword')</em>
                                <p>
                                    @lang('ac.Changetheemailaddress')
                                </p>
                                <p>@lang('ac.GotoAccountBasicSettingsinsert')</p>
                                <p>@lang('ac.Changethepassword')</p>
                                <p>@lang('ac.Gopasswordbutton')</p>
                                <p>@lang('ac.Resetthepincod')</p>
                                <p>@lang('ac.GotoAccountpressthe')</p>
                                <a name="16"></a>
                                <em class="para">@lang('ac.NewsAndImportantUpdates')</em>
                                <p>@lang('ac.Dofollowusonoursocialmedia')</p>
                                <p>@lang('ac.ForTwitterpleasegotoTwitter')</p>
                                <p style="padding-bottom: 200px">@lang('ac.ForFacebookpleasegotoFacebook')</p>
                            </div>

                            <div class="help-content">
                                <h3>@lang('ac.OrdersTrades')</h3>
                                <ul class="ques">
                                    <li>
                                        <a href="#17"><span>@lang('ac.WhatareBuyingSellingorders')</span></a>
                                    </li>
                                    <li>
                                        <a href="#18"><span>@lang('ac.WhatisOrderBook')</span></a>
                                    </li>
                                    <li>
                                        <a href="#19"><span>@lang('ac.WhatisMarketHistory')</span></a>
                                    </li>
                                    <li>
                                        <a href="#20"><span>@lang('ac.WhatareOpenorders')</span></a>
                                    </li>
                                </ul>
                                <a name="17"></a>
                                <em class="para">@lang('ac.WhatareBuyingSellingorders')</em>
                                <p>@lang('ac.Afterdepositingyourcurrency')</p>
                                <p>@lang('ac.Thoughtheprinciplesarequite')</p>
                                <p>@lang('ac.BidBuyorderiswhat')</p>
                                <p>@lang('ac.AskorSellorderisthe')</p>
                                <p>@lang('ac.Onceusetthewhether')</p>
                                <p>@lang('ac.Pleasenotethattransactions')</p>
                                <a name="18"></a>
                                <em class="para">@lang('ac.WhatisOrderBook')</em>
                                <p>@lang('ac.Intheorderbooks')</P>
                                <p>@lang('ac.Abuyorderwillbeappearatthe')</p>
                                <a name="19"></a>
                                <em class="para">@lang('ac.WhatisMarketHistory')</em>
                                <p>@lang('ac.Onceanorderiscompletedit')</p>
                                <a name="20"></a>
                                <em class="para">@lang('ac.WhatareOpenorders')</em>
                                <p>@lang('ac.Whenyouaretradingyouare')</p>
                                <br />
                                {{--<p class="help-contact" style="padding-bottom: 200px"><a href="{{url('/help/contactUs')}}">{{url('/help/contactUs')}}</a></p>--}}
                            </div>

                            <div class="help-content show">
                                <h3>@lang('ac.Withdrawal')</h3>
                                <ul class="ques">
                                    <li><a href="#21"><span>@lang('ac.Beforemakingwithdrawals')</span></a></li>
                                    <li><a href="#22"><span>@lang('ac.Withdrawalconfirmationemail')</span></a></li>
                                    <li><a href="#23"><span>@lang('ac.Withdrawalwaitingtime')</span></a></li>
                                    <li>
                                        <a href="#24"><span>@lang('ac.Ongoingwithdrawal')</span></a>
                                    </li>
                                    <li>
                                        <a href="#25"><span>@lang('ac.Withdrawalhistoryss')</span></a>
                                    </li>
                                    <li>
                                        <a href="#26"><span>@lang('ac.Arethereanywithdrawalfees')</span></a>
                                    </li>
                                    <li>
                                        <a href="#27"><span>@lang('ac.ManualWithdrawal')</span></a>
                                    </li>
                                    <li>
                                        <a href="#28"><span>@lang('ac.toanytaxformywithdrawals')</span></a>
                                    </li>
                                    <li>
                                        <a href="#29"><span>@lang('ac.Whatisthelimit')</span></a>
                                    </li>
                                </ul>
                                <a name="21"></a>
                                <em class="para">@lang('ac.Beforemakingwithdrawals')</em>
                                <p>@lang('ac.Itisveryimportanttocheck')</p>
                                <a name="22"></a>
                                <em class="para">@lang('ac.Withdrawalconfirmationemail')</em>
                                <p>@lang('ac.Wheneveryoumakeawithdrawal')</p>
                                <a name="23"></a>
                                <em class="para">@lang('ac.Withdrawalwaitingtime')</em>
                                <p>@lang('ac.WithdrawalsandDepositstakesome')</p>
                                <p>@lang('ac.Ifbeingexploredthenthe')</p>
                                <p>@lang('ac.Youcanalwaysthestatus')</p>


                                <a name="24"></a>
                                <em class="para">@lang('ac.Ongoingwithdrawal')</em>
                                <p>@lang('ac.ongoingwithdrawalsyoucan')</p>
                                <a name="25"></a>
                                <em class="para">@lang('ac.WithdrawalHistory')</em>
                                <p>@lang('ac.findwithdrawalWithdrawalhistory')</p>
                                <a name="26"></a>
                                <em class="para">@lang('ac.Arethereanywithdrawalfees')</em>
                                <p>@lang('ac.DOES')</p>
                                <a name="27"></a>
                                <em class="para">@lang('ac.ManualWithdrawal')</em>
                                <p>@lang('ac.ACxchangehasinbuiltlayerstobetter')</p>
                                <a name="28"></a>
                                <em class="para">@lang('ac.toanytaxformywithdrawals')</em>
                                <p>@lang('ac.ACxchangeisnotresponsibletolias')</p>
                                <a name="29"></a>
                                <em class="para">@lang('ac.Whatisthelimit')</em>
                                {{--<p>@lang('ac.TherearenowithdrawalfeesfromACxchange')</p>--}}
                                {{--<p>@lang('ac.Limitforwithdrawalsisbasedon')<a href="{{url('/wallet/index')}}">{{url('/wallet/index')}} </a>)@lang('ac.andcontactsupportteamforupgradingthelevel')</p>--}}
                                <p>
                                   @lang('ac.Withdrawallimitsasfollows')
                                    <br />
                                    @lang('ac.Level1')
                                    <br />
                                    @lang('ac.Level2')
                                    <br />
                                    @lang('ac.Level3')
                                    <br />
                                    @lang('ac.Level3A')
                                    <br />
                                    @lang('ac.Level4')
                                    <br />
                                    @lang('ac.Level4A')
                                </p>
                                {{--<p class="help-contact" style="padding-bottom: 200px"><a href="{{url('/help/contactUs')}}">{{url('/help/contactUs')}}</a></p>--}}
                            </div>
                            <div class="help-content">
                                <h3>@lang('ac.Deposit')</h3>
                                <ul class="ques">
                                    <li>
                                        <a href="#30"><span>@lang('ac.Beforemakingdeposits')</span></a>
                                    </li>
                                    <li>
                                        <a href="#31"><span>@lang('ac.MakeaDeposit')</span></a>
                                    </li>
                                    <li>
                                        <a href="#32"><span>@lang('ac.DepositwaitingtimeatACxchange')</span></a>
                                    </li>
                                    <li>
                                        <a href="#33"><span>@lang('ac.Arethereanydepositingfees')</span></a>
                                    </li>
                                    <li>
                                        <a href="#34"><span>@lang('ac.fundsdepositingcreditcards')</span></a>
                                    </li>
                                </ul>
                                <a name="30"></a>
                                <em class="para">@lang('ac.Beforemakingdeposits')</em>
                                <p>@lang('ac.checkthestatusandaddress')</p>
                                <a name="31"></a>
                                <em class="para">@lang('ac.MakeaDeposit')</em>
                                <p>
                                   @lang('ac.OnceintoyourACxchangeaccountgo')(<a href="{{url('/wallet/index')}}">{{url('/wallet/index')}}</a>).@lang('ac.depositingspecificcoinyouraccount')</p>
                                <br />
                                <p>@lang('ac.Pleaseeverycryptocurrencydifferentdepositing')</p>
                                <a name="32"></a>
                                <em class="para">@lang('ac.DepositwaitingtimeatACxchange')</em>
                                <p>@lang('ac.Depositsandwithdrawalstakesometime')</p>
                                <a name="33"></a>
                                <em class="para">@lang('ac.Arethereanydepositingfees')</em>
                                <p>@lang('ac.DOESNOTchargeandwithdrawalfees')</p>
                                <a name="34"></a>
                                <em class="para">@lang('ac.fundsdepositingcreditcards')</em>
                                <p>@lang('ac.momentACxchangenotacceptFIAT')</p>
                                <br />
                                {{--<p class="help-contact" style="padding-bottom: 200px"><a href="{{url('/help/contactUs')}}">{{url('/help/contactUs')}}</a></p>--}}
                            </div>
                            <div class="help-content">
                                <h3>@lang('ac.TwoFactorAuthentication')</h3>
                                <ul class="ques">
                                    <li>
                                        <a href="#35"><span>@lang('ac.Whatis2FAGoogleAuthenticator')</span></a>
                                    </li>
                                    <li>
                                        <a href="#36"><span>@lang('ac.toenablethe2FA')</span></a>
                                    </li>
                                    <li>
                                        <a href="#37"><span>@lang('ac.IdophoneIenable2FA')</span></a>
                                    </li>
                                    <li>
                                        <a href="#38"><span>@lang('ac.WhathappensifIlosemyphone')</span></a>
                                    </li>
                                </ul>
                                <a name="35"></a>
                                <em class="para">@lang('ac.Whatis2FAGoogleAuthenticator')</em>
                                <p>@lang('ac.issingleusethatprovidesextrasecurity')</p>
                                <a name="36"></a>
                                <em class="para">@lang('ac.toenablethe2FA')</em>
                                <p>@lang('ac.TofeatureyouneedGoogleAuthenticator')</p>
                                <a name="37"></a>
                                <em class="para">@lang('ac.IdophoneIenable2FA')</em>
                                <p>@lang('ac.YesyoucanWerecommend')</p>
                                <br />
                                <p>@lang('ac.KeepintheMFAcodethat')</p>
                                <a name="38"></a>
                                <em class="para">@lang('ac.WhathappensifIlosemyphone')</em>
                                <p>@lang('ac.contactsupporttoreset')</p>
                                <br />
                                <br />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(function(){
                    var url=window.location.href;
                    console.log(url);
                    if(url.indexOf('#faq') > -1) {
                        $('.help-nav li').eq(0).addClass('acti').siblings().removeClass('acti');
                        $("div.help-content").hide().eq(0).show();

                    }
                });

                $(document).ready(function() {
                    $('.help-nav li a').click(function(e) {
                        $('.help-nav li').eq($(this).parent().index()).addClass('acti').siblings().removeClass('acti');
                        $("div.help-content").hide().eq($(this).parent().index()).show();
                        if($('.menu-btn').is(':visible')) {
                            $('.help-nav').hide();
                            $('.menu-btn a img').attr('src', '{{ asset('images/left-btn.png') }}');
                        }
                        e.stopPropagation()
                    })
                    $('.help-nav-h li a').click(function(e) {
                        $('.help-nav-h li').eq($(this).parent().index()).addClass('acti').siblings().removeClass('acti');
                        $("div.help-content").hide().eq($(this).parent().index()).show();
                        if($('.menu-btn').is(':visible')) {
                            $('.help-nav-h').hide();
                            $('.menu-btn a img').attr('src', '{{ asset('images/left-btn.png') }}');
                        }
                        e.stopPropagation()
                    })
                    $('.menu-btn').click(function(e) { //菜单栏出现
                        if($('.help-nav-h').is(':hidden')) {
                            $('.help-nav-h').show();
                            $('.menu-btn a img').attr('src', 'images/left-btn2.png');
                        } else if($('.help-nav-h').is(':visible')) {
                            $('.help-nav-h').hide()
                            $('.menu-btn a img').attr('src', '{{ asset('images/left-btn.png') }}')
                        }
                        e.stopPropagation()
                    });
                    $(document).click(function() {
                        $('.help-nav-h').hide();
                        $('.menu-btn a img').attr('src', '{{ asset('images/left-btn.png') }}');
                    })
                    $(document).scroll(function() {
                        $('.help-nav-h').hide();
                        $('.menu-btn a img').attr('src', '{{ asset('images/left-btn.png') }}');
                    })
                    $('.help-content').scroll(function() {
                        $('.help-nav-h').hide();
                        $('.menu-btn a img').attr('src', '{{ asset('images/left-btn.png') }}');
                    })

                    $('.ques li a').click(function(e){
                        $("html,body").animate({"scrollTop": "0"}, 10);
                    })

                })

            </script>

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
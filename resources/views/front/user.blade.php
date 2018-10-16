@extends('layouts.app')
@section('content')
    <!--user part-->
    <div class="user">
    <div class="user-content">
        <div class="user-tips">
            @if(!$user->auth_type)
                <p><i class="iconfont icon-open-warn"></i>@lang('ac.securityTransactions') @lang('ac.Authentication')!</p>
                <p><i class="iconfont icon-open-warn"></i>@lang('ac.unlockFeatures')</p>
            @endif
        </div>
        <div class="user-wrapper">
            <div class="user-part wow fadeInLeftBig">
                <!--un未绑定ok绑定-->
                <div class="bound @if(empty($user->bind_user_id)) un @else ok @endif">
                  <h1 class="handler handl-btn"><i class="iconfont icon-less"></i>@lang('ac.AllianceCapitalsClientLogin') <span>@if(empty($user->bind_user_id))@lang('ac.Notlinked') @else @lang('ac.linked') @endif</span></h1>
                  <div class="informate stoggBox bigbox">
                      @if(empty($user->bind_user_id))
                          <div class="wenzi">
                              <p>@lang('ac.notLinkeds') </p>
                              {{--<p>@lang('ac.following').</p>--}}
                          </div>
                          <div class="row-element row-gap">
                              <div class="row-inline" style="margin-bottom: 20px;">
                                  <label>@lang('ac.ClientID'):</label>
                                  <input type="text" name="userName"  value="" autocomplete="off" placeholder="@lang('ac.egAccount')">
                              </div>
                              <div class="row-inline">
                                  <label>@lang('ac.Password'):</label>
                                  <input type="password" name="bindPass" value="" autocomplete="off" placeholder="@lang('ac.acPassword')">
                              </div>
                          </div>
                          <div class="tipbound"></div>
                          <div class="row-element">
                              <button type="button" id="idBind" class="notPoint">@lang('ac.Bind')</button>
                          </div>
                      @else
                          <div class="wenzi">
                              <p>@lang('ac.alreadyBoundAccount') </p>
                          </div>
                      @endif
                </div>
                </div>
                <div>
                    <h1 class="handler handl-btn"><i class="iconfont icon-less"></i>@lang('ac.BasicInformation')</h1>
                    <div class="informate stoggBox bigbox">
                        <div class="row-element row-gap">
                            <div class="row-inline">
                                <label>@lang('ac.UniqueID'):</label>
                                <input type="text" id="loginID" readonly value="1234{{$user->id}}">
                            </div>

                        </div>
                        <div class="row-element row-gap">
                            <div class="row-inline">
                                <label>@lang('ac.Email'):</label>
                                <input type="text" readonly value="{{$user->email}}" id="infoEmail">
                                {{--<span class="infoID">--}}
                                    {{--Change--}}
                                {{--</span>--}}
                            </div>

                        </div>
                        <div class="row-element row-gap">
                            <div class="row-inline">
                                <label>@lang('ac.Password'):</label>
                                <input type="password" readonly value="***************">
                                <span class="showDialog" id="changePwdText">@lang('ac.Change')</span>
                            </div>

                        </div>
                        <div class="row-element row-gap">
                            <div class="row-inline">
                                <label>@lang('ac.Pin'):</label>
                                <input type="password" readonly @empty(!$user->pin) value="******************" @endempty>
                                <span class="showDialog" id="changePinText">@lang('ac.Change')</span>
                            </div>
                        </div>
                        <div class="row-element row-gap">
                            <div class="row-inline row-disabed">
                                <label>@lang('ac.Lastlogin'):</label>
                                <input type="text" value="{{$user->last_login}}" readonly>
                            </div>
                        </div>
                        <div class="row-element row-gap">
                            <div class="row-inline row-disabed">
                                <label>@lang('ac.LoginIP'):</label>
                                <input type="text" readonly value="{{$user->login_ip}}">
                            </div>

                        </div>
                        <div class="row-element row-gap">
                            <div class="row-inline row-disabed">
                                <label>@lang('ac.Registered'):</label>
                                <input type="text" readonly value="{{$user->created_at}}">
                            </div>
                        </div>
                        <!-- <div class="row-element">
                            <button type="button">Save</button>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="user-part wow fadeIn" data-wow-delay=".5s">
                <h1 class="handler handl-btn newflex">
                    <div class="nowarp">
                      <i class="iconfont icon-less"></i>
                      @lang('ac.Authentication')
                    </div>
                    <ul class="verify-group" style="display: inline-block">
                        <li class="active" style="background-color:#eee0;">
                            @if( $user->is_certification == 1 )
                                <i class="verify-btn" style="background-color: #f5741d;"> @lang('ac.underReview') </i>
                            @elseif($user->is_certification == 4)
                                <i class="verify-btn"  style="background-color: #00a65a">  @lang('ac.examinationPassed')</i>
                            @elseif($user->is_certification == 3)
                                <i class="verify-btn"> @lang('ac.refused') </i>
                                @if(count($reasons)>0)
                                    <i style="color: #999ea3; background: none; padding: 0;">
                                        <div style="text-align: left">
                                            @foreach($reasons as $one) <p style="font-size: 12px;"><b style="font-weight: bold">·</b>{{$one}} </p>@endforeach
                                        </div>
                                    </i>
                                @endif
                            @endif
                        </li>
                    </ul>
                </h1>
                <div class="stoggBox bigbox">
                    <div class="select2" style="padding:24px 16px; ">
                        <div class="row-element">
                            <label for="">@lang('ac.certificateType')</label>
                            <select name="" class="addselect" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                @if(  $user->is_certification == 0 || $user->is_certification == 3 )
                                    @if($user->card_id)
                                        <option value="1">@lang('ac.IDCard') </option>
                                        <option value="0">@lang('ac.Passport')</option>
                                    @else
                                        <option value="0">@lang('ac.Passport')</option>
                                        <option value="1">@lang('ac.IDCard') </option>
                                    @endif
                                @elseif($user->card_id  && ($user->is_certification != 0 || $user->is_certification != 3) )
                                    <option value="0">@lang('ac.IDCard')</option>
                                @else
                                    <option value="0">@lang('ac.Passport')</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="authencon select3">
                        <div class="idcard @if($user->card_id) @else hide @endif " id="idCard">
                            <form>
                                <div class="row-element">
                                    <label>@lang('ac.FirstName'):</label>
                                    <input type="text" name="fname" id="fname" @empty(!$user->first_name) value="{{$user->first_name}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                    <span class="tips"></span>
                                </div>
                                <div class="row-element">
                                    <label>@lang('ac.LastName')</label>
                                    <input type="text" name="lname" id="lname" @empty(!$user->last_name) value="{{$user->last_name}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                    <span class="tips"></span>
                                </div>
                                <div class="row-element">
                                    <label>@lang('ac.Nationality'):</label>
                                    <select id="nation" name="nation" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                        @foreach($countryRegion as $country)
                                        <option value="{{$country->country_id}}" @if($user->nationality == $country->country_id ) selected @endif>  @if( session('setLocale') === 'cn') {{$country->country}} @elseif(session('setLocale') === 'tw') {{$country->tw_country}}  @else {{$country->en_country}}  @endif </option>
                                        @endforeach
                                    </select>
                                    <span class="tips"></span>
                                </div>
                                <div class="row-element" class="">
                                    <label>@lang('ac.IDNumber'):</label>
                                    <input type="text" name="idNum" id="idnum" @empty(!$user->IdCards()->value('card_number')) value="{{$user->IdCards()->value('card_number')}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                    <span class="tips"></span>
                                </div>
                                <div class="row-element">
                                    <label>@lang('ac.Birthday'):</label>
                                    <div class="row-flex" id="birthday">
                                        <select name="selectYear" id="select_year" rel="@if($user->year === 0)2018 @else{{$user->year}} @endif" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif></select>
                                        <label for="">@lang('ac.Year')</label>

                                        <select name="selectMonth" id="select_month" rel="@if($user->month === 0)1 @else{{$user->month}} @endif" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif></select>
                                        <label for="">@lang('ac.Month')</label>

                                        <select name="selectDay" id="select_day" rel="@if($user->day === 0)1 @else{{$user->day}} @endif" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif></select>
                                        <label for="">@lang('ac.Day')</label>
                                    </div>
                                    <span class="tips"></span>
                                </div>
                                <div class="row-element">
                                    <label>@lang('ac.ResidentialAddress'):</label>
                                    <input type="text" name="raddress" id="raddress" @empty(!$user->residential_address) value="{{$user->residential_address}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                    <span class="tips"></span>
                                </div>
                                <div class="row-element">
                                    <label for="">@lang('ac.RegionCode'):</label>
                                    <select name="idCode" class="verifyArea1" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                        @foreach($countryRegion as $value)
                                            <option value="{{$value->country_id}}" @if($user->region_ode == $value->country_id ) selected @endif>  +{{$value->region}} @if( session('setLocale') === 'en') ( {{$value->en_country}} ) @elseif(session('setLocale') === 'tw') ({{$value->tw_country}})  @else  ({{$value->country}})  @endif </option>
                                        @endforeach
                                    </select>
                                    <span class="tips"></span>
                                </div>
                                <div class="row-element">
                                    <label>@lang('ac.ContactNumber'):</label>
                                    <input type="text" name="idPhone" id="phone" @empty(!$user->phone) value="{{$user->phone}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                    <span class="tips"></span>
                                </div>
                                <div class="row-element">
                                    <label for=""><span>@lang('ac.yourIDCard')</span></label>
                                    <div class="row-flex">
                                        <div class="upload-img">
                                            <img src="@if($user->IdCards()->value('img_front') == '' ){{ asset('images/idcard.png') }}@else{{asset($user->IdCards()->value('img_front')) }}@endif " alt="">
                                            <p>@lang('ac.PositivePhoto')<br />
                                                (<2M)</p>
                                            <div class="upload-input">
                                                <input type="file" name="idfile1" id="idfile1" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                                <input type="hidden" name="card1" value="" id="card1">
                                            </div>
                                        </div>
                                        <div class="upload-img">
                                            <img src="@if($user->IdCards()->value('img_back') == '' ){{ asset('images/idcard2.png') }}@else{{asset($user->IdCards()->value('img_back')) }}@endif" alt="">
                                            <p>@lang('ac.ReversePhoto')<br />
                                                (<2M)</p>
                                            <div class="upload-input">
                                                <input type="file" name="idfile2"  id="idfile2" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                                <input type="hidden" name="card2" value="" id="card2">
                                            </div>
                                        </div>
                                        <div class="upload-img" style="height: auto">
                                            <img src="@if($user->IdCards()->value('img_hand') == '' ){{ asset('images/idcard3.jpg') }}@else {{asset($user->IdCards()->value('img_hand')) }}@endif" alt="">
                                            <p>@lang('ac.HandHeld')<br />
                                                (<2M)</p>
                                            <div class="upload-input">
                                                <input type="file" name="idfile3" id="idfile3" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                                <input type="hidden" name="card3" value="" id="card3">
                                            </div>
                                        </div>
                                    </div>
                                    <p>@lang('ac.UploadPhotosNote'):</p>
                                    <ul>
                                        <li>@lang('ac.onlyOfficialDocument').</li>
                                        <li>@lang('ac.theInformationIn').</li>
                                        {{--<li>@lang('ac.IDcardeffective').</li>--}}
                                        {{--<li>@lang('ac.IDCardPhotos')</li>--}}
                                        {{--<li>@lang('ac.reflectionsCreases')</li>--}}
                                        {{--<li>@lang('ac.photosYouUploaded')</li>--}}
                                    </ul>
                                </div>
                                @if( empty($user->card_id) || $user->is_certification == 0 || $user->is_certification == 3 )
                                    <div class="row-element">
                                        <button type="button" class="notPoint saveIdCard">Submit</button>
                                    </div>
                                @endif
                            </form>
                        </div>

                        <div class="idcard @if($user->card_id) hide @else  @endif " id="passPort">
                            <form>
                            <div class="row-element">
                                <label>@lang('ac.FirstName'):</label>
                                <input type="text" name="fname2" id="fname2" @empty(!$user->first_name) value="{{$user->first_name}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                <span class="tips"></span>
                            </div>
                            <div class="row-element">
                                <label>@lang('ac.LastName')</label>
                                <input type="text" name="lname2" id="lname2" @empty(!$user->last_name) value="{{$user->last_name}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                <span class="tips"></span>
                            </div>
                            <div class="row-element">
                                <label>@lang('ac.Nationality'):</label>
                                <select name="nation2" id="nation2" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                    @foreach($countryRegion as $country)
                                        <option value="{{$country->country_id}}" @if($user->nationality == $country->country_id ) selected @endif>@if( session('setLocale') === 'cn'){{$country->country}} @elseif(session('setLocale') === 'tw') {{$country->tw_country}}  @else {{$country->en_country}}  @endif</option>
                                    @endforeach
                                </select>
                                <span class="tips"></span>
                            </div>
                            <div class="row-element" class="">
                                <label>@lang('ac.PassportNumber'):</label>
                                <input type="text" name="passNum" @empty(!$user->Passports()->value('passport_number')) value="{{$user->Passports()->value('passport_number')}}" @endempty  class="portid" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                <span class="tips"></span>
                            </div>
                                {{--<div class="row-element">--}}
                                    {{--<label>@lang('ac.Birthday'):</label>--}}
                                    {{--<div class="row-flex" id="birthday">--}}
                                        {{--<select name="selectYear" id="select_year" rel="@if($user->year === 0)2018 @else{{$user->year}} @endif"></select>--}}
                                        {{--<label for="">@lang('ac.Year')</label>--}}

                                        {{--<select name="selectMonth" id="select_month" rel="@if($user->month === 0)1 @else{{$user->month}} @endif"></select>--}}
                                        {{--<label for="">@lang('ac.Month')</label>--}}

                                        {{--<select name="selectDay" id="select_day" rel="@if($user->day === 0)1 @else{{$user->day}} @endif"></select>--}}
                                        {{--<label for="">@lang('ac.Day')</label>--}}
                                    {{--</div>--}}
                                    {{--<span class="tips"></span>--}}
                                {{--</div>--}}
                            <div class="row-element">
                                <label>@lang('ac.Birthday'):</label>
                                <div class="row-flex" id="birthday2">
                                    <select name="selectYear2" id="select_year2" rel="@if($user->year === 0)2018 @else{{$user->year}} @endif" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif></select>
                                    <label for="">@lang('ac.Year')</label>

                                    <select name="selectMonth2" id="select_month2" rel="@if($user->month === 0)1 @else{{$user->month}} @endif" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif></select>
                                    <label for="">@lang('ac.Month')</label>

                                    <select name="selectDay2" id="select_day2" rel="@if($user->day === 0)1 @else{{$user->day}} @endif" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif></select>
                                    <label for="">@lang('ac.Day')</label>
                                </div>
                                <span class="tips"></span>
                            </div>
                            <div class="row-element">
                                <label>@lang('ac.ResidentialAddress'):</label>
                                <input type="text" name="raddress2" id="raddress2"  @empty(!$user->residential_address) value="{{$user->residential_address}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                <span class="tips"></span>
                            </div>
                                <div class="row-element">
                                    <label for="">@lang('ac.RegionCode'):</label>
                                    <select name="idCode2" class="verifyArea1" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                        @foreach($countryRegion as $value)
                                            <option value="{{$value->country_id}}" @if($user->region_ode == $value->country_id ) selected @endif>+{{$value->region}} @if( session('setLocale') === 'en') ( {{$value->en_country}} ) @elseif(session('setLocale') === 'tw') ({{$value->tw_country}})  @else  ({{$value->country}} )  @endif</option>
                                        @endforeach
                                    </select>
                                    <span class="tips"></span>
                                </div>
                            <div class="row-element">
                                <label>@lang('ac.ContactNumber'):</label>
                                <input type="text" name="phone2" id="phone2" @empty(!$user->phone) value="{{$user->phone}}" @endempty @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                <span class="tips"></span>
                            </div>
                            <div class="row-element">
                                <label for=""><span>@lang('ac.PleaseUploadPassport').</span></label>
                                <div class="row-flex">
                                    <div class="upload-img">
                                        <img src="@if($user->Passports()->value('img_front') == '' ){{ asset('images/passport.jpg') }}@else {{asset($user->Passports()->value('img_front'))}} @endif" alt="">
                                        <p>@lang('ac.PositivePhoto')<br />
                                            (<2M)</p>
                                        <div class="upload-input">
                                            <input type="file" name="prFile1" id="port1" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                            <input type="hidden" name="prFileBin1" value="" id="protImg1">
                                        </div>
                                    </div>
                                    <div class="upload-img">
                                        <img src="@if($user->Passports()->value('img_back') == '' ){{ asset('images/passport2.jpg') }}@else {{asset($user->Passports()->value('img_back'))}} @endif" alt="">
                                        <p>@lang('ac.HandHeld')<br />
                                            (<2M)</p>
                                        <div class="upload-input">
                                            <input type="file" name="prFile2" id="port2" @if($user->is_certification == 1 || $user->is_certification == 4) disabled @endif>
                                            <input type="hidden" name="prFileBin2" value="" id="protImg2">
                                        </div>
                                    </div>
                                </div>
                                <p>@lang('ac.UploadPhotosNote'):</p>
                                <ul>
                                    <li>@lang('ac.onlyOfficialDocument').</li>
                                    <li>@lang('ac.theInformationIn').</li>
                                    {{--<li>@lang('ac.NoReflections')</li>--}}
                                    {{--<li>@lang('ac.ThePhotosUploaded').</li>--}}
                                </ul>
                            </div>
                            @if( $user->is_certification == 3 || $user->is_certification == 0 || empty($user->passport_id))
                                <div class="row-element">
                                    <button type="button" class="notPoint licenseId">Submit</button>
                                </div>
                            @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="user-part wow wow fadeInRightBig ">
                <div class="myapi">
                    <div class="bound">
                        <h1 class="handler handl-btn"><i class="iconfont icon-less"></i>@lang('ac.MYAPI')</h1>
                        <div class="informate stoggBox bigbox apibox">
                            @if(!empty($key))
                                <div class="row-element row-gap"  id="hadApi" >
                                    <div class="row-inline" style="margin-bottom: 20px;">
                                        <label>@lang('ac.ApiKey'):</label>
                                        <input type="text" name="key" value="{{$key}}" disabled style=" color: #828893;">
                                    </div>
                                    <div class="row-inline">
                                        <label>@lang('ac.Secret'):</label>
                                        <input type="password" name="secret" value="{{$secretKey}}" disabled style=" color: #828893;">
                                        <i class="yan"><img src="{{ asset('images/yan.png') }}"></i>
                                    </div>
                                    <div class="row-element"  id="reApi" style="width: 100%" >
                                        <button type="button" onclick="createApiKey({{\Illuminate\Support\Facades\Auth::id()}})" class="idAPI">@lang('ac.Recreate')</button>
                                    </div>
                                </div>
                            @endif
                            @if(empty($key))
                                <div class="row-element"  id="noApi" >
                                    <button type="button" onclick="createApiKey({{\Illuminate\Support\Facades\Auth::id()}})" class="idAPI">@lang('ac.CreateAPI')</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <h1 class="handler handl-btn"><i class="iconfont icon-less"></i>@lang('ac.Security')</h1>
                    <div class="stoggBox bigbox">
                        <ul class="verify-group" id="security-list">
                            <li class="active"><span>@lang('ac.AuthyVerificat')</span></li>
                            <li><span>@lang('ac.SMSVerification')</span></li>
                            <li><span>@lang('ac.GoogleAuthent')</span></li>
                        </ul>
                        <div class="security-part">
                            <div class="verification" id="verifyAuthy">
                                <form>
                                    <div class="line">
                                        <div class="line-block mb20">
                                            <label for="">@lang('ac.RegionCode'):</label>
                                            <select name="" class="verifyArea1  authy_region" @if(empty(!$authy)) disabled @endif>
                                                @foreach($countryRegion as $value)
                                                    @if(empty(!$authy))
                                                        <option value="{{$value->region}}" @if($authy->country_code == $value->region ) selected @endif> +{{$value->region}} ( @if( session('setLocale') === 'cn') {{$value->country}}  @elseif(session('setLocale') === 'tw') ({{$value->tw_country}}  @else  {{  $value->en_country }}  @endif ) </option>
                                                    @else
                                                        <option value="{{$value->region}}" @if($user->region_ode == $value->country_id ) selected @endif> +{{$value->region}} ( @if( session('setLocale') === 'cn')  {{$value->country}}  @elseif(session('setLocale') === 'tw') {{$value->tw_country}}  @else  {{  $value->en_country }}   @endif ) </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="line-block">
                                            <label for="">@lang('ac.PhoneNumber'):</label>
                                            <input type="text" class="verifyPhone authPnum" @if(empty(!$authy)) disabled value="{{$authy->phone_number}}" @else(if(empty(!$user->phone))) value="{{$user->phone}}" @endif>
                                        </div>
                                        <span class="authy_phone_error tips"></span>
                                    </div>
                                    <div class="line">
                                        <div class="line-block">
                                            <label for="">@lang('ac.AuthEmail'):</label>
                                            <div>
                                                <input type="text" class="authEmail" @if(empty(!$authy)) disabled value="{{$authy->email}}" @else(if(empty(!$user->phone))) value="{{$user->email}}" @endif>
                                                <!-- 发送验证码的时候，加个class undisabled -->
                                                @if(empty($authy))<button type="button" class="send sendAuthEmail"><span>@lang('ac.SendCode')</span></button>@endif
                                            </div>
                                            <span class="tips"></span>
                                        </div>
                                    </div>
                                    @if(empty($authy))
                                        <div class="line">
                                            <div class="line-block">
                                                <label for="">@lang('ac.VerificationCode'):</label>
                                                <input type="text" value="" class="verifyCode codePhone authy_code">
                                            </div>
                                            <span class="email_error"></span>
                                        </div>
                                        <div class="line">
                                            <button type="button" class="verifyBtn notPoint authy_verify" id="verifyBtn1">@lang('ac.VerifyEnable')</button>
                                        </div>
                                    @else
                                        <div class="line">
                                        @if($user->auth_type === 2)
                                                <span>@lang('ac.Default2FA')</span>
                                        @else
                                           <button type="button" class="verifyBtn set_default" def="2">@lang('ac.SetAsDefault')</button>
                                        @endif
                                            <button type="button" class="verifyBtn" onclick="rest2FAShow(1)" style="background-color:#eea236;width:20%;margin-right: 8px;">@lang('ac.Reset2FA')</button>
                                        </div>
                                    @endif
                                </form>
                            </div>
                            <div class="verification hide" id="verifySms">
                                <form>
                                    <div class="line">
                                        <div class="line-block mb20">
                                            <label for="">@lang('ac.RegionCode'):</label>
                                            <select name="" id="" class="verifyArea2 sms_region" @if(empty(!$sms)) disabled @endif>
                                                @foreach($countryRegion as $value)
                                                    @if(empty(!$sms))
                                                        <option value="{{$value->region}}" @if($sms->country_code == $value->region ) selected @endif>+{{$value->region}}({{$value->en_country}})</option>
                                                    @else
                                                        <option value="{{$value->region}}" @if($user->region_ode == $value->country_id ) selected @endif>+{{$value->region}}({{$value->en_country}})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="line-block">
                                            <label for="">@lang('ac.PhoneNumber'):</label>
                                            <div>
                                                <input type="text" class="verifyPhone sms_phone" @if(empty(!$sms)) disabled value="{{$sms->phone_number}}" @else(if(empty(!$user->phone))) value="{{$user->phone}}" @endif>
                                                <!-- 发送验证码的时候，加个class undisabled -->
                                                @if(empty($sms))
                                                    <button type="button" class="send" id="sendSmsAuth"><span>@lang('ac.SendCode')</span></button>
                                                @endif
                                            </div>
                                            <span class="tips"></span>
                                        </div>
                                    </div>
                                    @if(empty($sms))
                                        <div class="line">
                                            <div class="line-block">
                                                <label for="">@lang('ac.VerificationCode'):</label>
                                                <input type="text" value="" class="verifyCode sms_code">
                                            </div>
                                            <span class="tips"></span>
                                        </div>
                                        <div class="line">
                                            <button type="button" class="verifyBtn notPoint sms_verify" id="verifyBtn2">@lang('ac.VerifyEnable')</button>
                                        </div>
                                    @else
                                        <div class="line">
                                        @if($user->auth_type === 3)
                                             <span>@lang('ac.Default2FA')</span>
                                        @else
                                            <button type="button" class="verifyBtn set_default" def="3">@lang('ac.SetAsDefault')</button>
                                        @endif
                                             <button type="button" class="verifyBtn" onclick="rest2FAShow(2)" style="background-color:#eea236;width:20%;margin-right: 8px;">@lang('ac.Reset2FA')</button>
                                        </div>
                                    @endif
                                </form>
                            </div>
                            <div class="verification hide">
                                <form>
                                    <div class="google-part" id="gooleVerify">
                                        @if(!empty($qrCodeUrl))
                                            <h6>@lang('ac.PleaseScanTheQR')</h6>
                                        @endif
                                        <dl>
                                            @if(!empty($qrCodeUrl))<dt><img src="{{$qrCodeUrl}}"></dt>@endif
                                            <dd>
                                                <p>
                                                    <i>@lang('ac.AccountName'): </i>
                                                    <span>{{getenv('APP_TITLE')}}</span>
                                                </p>
                                                <p>
                                                    <i>@lang('ac.Key'): </i>
                                                    <span id="keySecret">@if(empty($secret)){{$user->secret}} @else{{$secret}} @endif</span>
                                                </p>
                                            </dd>
                                        </dl>
                                        <p>@lang('ac.installGoogleVerification')</p>
                                        <ul>
                                            <li>@lang('ac.GoogleAuthenticator')</li>
                                            <li>@lang('ac.SearchGoogle')</li>
                                            <li>@lang('ac.GoogleSecondary')</li>
                                        </ul>
                                        <a href="https://helpdesk.lastpass.com/zh/multifactor-authentication-options/google-authenticator/">@lang('ac.seeTheDetailedTutorial')</a>
                                        @if(!empty($qrCodeUrl))
                                            <div class="line">
                                                <div class="line-block">
                                                    <label for="">@lang('ac.GoogleVerificationCode'):</label>
                                                    <input type="text" class="gooleCode">
                                                </div>
                                                <span class="tips"></span>
                                            </div>
                                            <!-- <div class="line">
                                                <div class="authy-code" id="authy-code">
                                                    <input type='number' maxlength='1' class='gg-input'>
                                                    <input type='number' maxlength='1' class='gg-input'>
                                                    <input type='number' maxlength='1' class='gg-input'>
                                                    <input type='number' maxlength='1' class='gg-input'>
                                                    <input type='number' maxlength='1' class='gg-input'>
                                                    <input type='number' maxlength='1' class='gg-input'>
                                                </div>
                                            </div> -->
                                            <div class="line">
                                                <button type="button" class="verifyBtn notPoint" id="verifyGoole">@lang('ac.VerifyEnable')</button>
                                            </div>
                                        @else
                                            <div class="line">
                                                @if($user->auth_type === 1)
                                                    @lang('ac.Default2FA')
                                                @else
                                                    <button type="button" class="verifyBtn set_default" def="1">@lang('ac.SetAsDefault')</button>
                                                @endif
                                                    <button type="button" class="verifyBtn" onclick="rest2FAShow(0)" style="background-color:#eea236;width:20%;margin-right: 8px;">@lang('ac.Reset2FA')</button>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="authy_modal" tabindex="-1" role="dialog" style="padding-right: 17px;">
    <div class="modal-dialog" role="document" style="margin-top: 341.5px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">@lang('ac.FA')</h4>
            </div>
            <div class="modal-body">
                <p id="2fa_verify_title">@lang('ac.Pin')</p>
                <div class="authy_code"></div>
            </div>
            <div class="modal-footer">
                <button id="send_authy" type="button" class="btn btn-nov" onclick="rest2FA()">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    function rest2FAShow(num) {
        $("#2fa_verify_title").html('Pin');
        var authy = '';
        $(".authy_code").empty();
        authy += "<input type='password' value='' style='background-color:transparent;border:1px solid #4c567f;' name='pin2FA'>";
        authy += "<input type='hidden' id='type2FA' value='"+num+"'>";
        $('.authy_code').append(authy);
        console.log(authy)
        $("#authy_modal").modal('show');
    }
    function rest2FA () {
        var num = $('#type2FA').val();
        var pin = $("input[name='pin2FA']").val();
        var url = ['/user/restGoogle','/user/restAuthy','/user/restSmS'];
        console.log(url[num]);
        console.log(pin);
        console.log(1111123456);
        //$("#authy_modal").modal('hide');
        return
        $.ajax({
            url:url[num]
            ,type:'POST'
            ,data:{pin:pin}
            ,success:function (data) {
                if (data.status){
                    successfully(data.message);
                    window.location.reload();
                }else {
                    error(data.message);
                }
                return false;
            }
            ,error:function (data) {
                error("@lang('ac.ResetFailed')");
                return false;
            }
        });
    }
    $(function () {
        var qrc = $('#gg_qrcode').attr('qrc');
        console.log(qrc);
        $('#gg_qrcode').qrcode(qrc);

        //绑定缓存问题清空
        $('div.bound .row-inline input[name="userName"]').val('');
        $('div.bound .row-inline input[name="bindPass"]').val('');

        //选择id card 或者passport
        $('.select2 .addselect').change(function(){
            var index=$(this).val();
            if (index==0){
                $('div.select3 #idCard').addClass('hide').siblings().removeClass('hide')
            }else if(index==1){
                $('div.select3 #passPort').addClass('hide').siblings().removeClass('hide')
            }
        })
    });
    //Authy-----------------------------------------------------
    $(".sendAuthEmail").on('click',function () {
        var region = $(".authy_region").val();
        var phone = $(".authPnum").val();
        var email = $(".authEmail").val();
        $.ajax({
            url:'/user/authy/auth'
            ,type:'GET'
            ,data:{'region':region,'phone':phone,'email':email}
            ,success:function (data) {
                if (data.status){
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 0,
                        area:['350px']
                    });
                    return false;
                }else {
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 6,
                        area:['350px']
                    });
                    return false;
                }
            }
            ,error:function (data) {
                layer.msg("@lang('ac.AuthyErrorCode')",{
                    offset: 'auto',
                    anim: 0,
                    area:['350px']
                });
                return false;
            }
        });
    });
    $(".authy_verify").on('click',function () {
        var authy_code = $(".authy_code").val();
        $.ajax({
            url:'/user/authy/check'
            ,type:'POST'
            ,data:{'authy_code':authy_code}
            ,success:function (data) {
                if (data.status){
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 0,
                        area:['350px']
                    });
                    setTimeout(function () {
                        window.location.reload()
                    },800);
                }else {
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 6,
                        area:['350px']
                    });
                    return false;
                }
            }
            ,error:function (data) {
                layer.msg("@lang('ac.AuthyErrorCode')",{
                    offset: 'auto',
                    anim: 0,
                    area:['350px']
                });
                return false;
            }
        });
    });
    //---------SMS----------------------------------------------------
    $("#sendSmsAuth").on('click',function () {
        var region = $(".sms_region").val();
        var phone = $(".sms_phone").val();
        $.ajax({
            url:'/user/sms/auth'
            ,type:'GET'
            ,data:{'region':region,'phone':phone}
            ,success:function (data) {
                if (data.status){
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 0,
                        maxWidth:370
                    });
                    return false;
                }else {
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 6,
                        area:['350px']
                    });
                    return false;
                }
            }
            ,error:function (data) {
                layer.msg("@lang('ac.SMSCodeError')",{
                    offset: 'auto',
                    anim: 0,
                    area:['350px']
                });
                return false;
            }
        });
    });
    $(".sms_verify").on('click',function () {
        var sms_code = $(".sms_code").val();
        var phone_number = $(".sms_phone").val();
        $.ajax({
            url:'/user/sms/check'
            ,type:'POST'
            ,data:{'sms_code':sms_code,'phone_number':phone_number}
            ,success:function (data) {
                if (data.status){
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 0,
                        area:['350px']
                    });
                    setTimeout(function () {
                        window.location.reload()
                    },800);
                }else {
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 6,
                        area:['350px']
                    });
                    return false;
                }
            }
            ,error:function (data) {
                layer.msg("@lang('ac.SMSCodeError')",{
                    offset: 'auto',
                    anim: 0,
                    area:['350px']
                });
                return false;
            }
        });
    });
    //------------Default------------------------------------------------
    $(".set_default").on('click',function () {
        var def = $(this).attr('def');
        $.ajax({
            url:'/user/set_auth_default'
            ,type:'POST'
            ,data:{'type':def}
            ,success:function (data) {
                if (data.status){
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 0,
                        area:['350px']
                    });
                    setTimeout(function () {
                        window.location.reload()
                    },800);
                }else {
                    layer.msg(data.message,{
                        offset: 'auto',
                        anim: 6,
                        area:['350px']
                    });
                    return false;
                }
            }
            ,error:function (data) {
                layer.msg('@lang('ac.Fail')',{
                    offset: 'auto',
                    anim: 0,
                    area:['350px']
                });
                return false;
            }
        });
    });
    $("input[name='userName']").blur(function(){
        rmClass();
    });
    $("input[name='bindPass']").blur(function(){
        rmClass();
    });
    function rmClass() {
        var userName = $("input[name='userName']").val();
        var bindPass = $("input[name='bindPass']").val();
        if (userName !== '' && bindPass !== ''){
            $('#idBind').removeClass('notPoint');
        }else {
            $('#idBind').addClass('notPoint');
        }
    }
    $('#idBind').on('click',function(){
        var status = $('#idBind').hasClass('notPoint');
        if (!status){
            var userName = $("input[name='userName']").val();
            var bindPass = $("input[name='bindPass']").val();
            $.ajax({
                url:'bindApiUser'
                ,type:'POST'
                ,data:{userName:userName,bindPass:bindPass}
                ,success:function (data) {
                    if (data.status){
                        $('.tipbound').html(data.message).removeClass('error').addClass('succ');
                        window.location.reload();
                    }else {
                        $('.tipbound').html(data.message).removeClass('succ').addClass('error');
                    }
                }
                ,error:function (data) {
                    var mg = JSON.parse(data.responseText);
                    for (var k in mg.errors){
                        var tip = mg.errors[k][0];
                    }
                    $('.tipbound').html(tip).removeClass('succ').addClass('error');
                    return false;
                }
            });
        }
        return false;
    });

    function createApiKey(id) {
        $.ajax({
            url: '/user/createApiKey'
            , type: 'POST'
            , data: {user_id: id}
            , success: function (data) {
                successfully(data.message);
                window.location.reload();
            }
            , error: function (data) {
                var mg = JSON.parse(data.responseText);
                for (var k in mg.errors) {
                    var tip = mg.errors[k][0];
                }
                error(tip);
            }
        })
    }

    $(function(){
        var userName = $("input[name='userName']").val();
        var bindPass = $("input[name='bindPass']").val();

        $('.user-part .myapi .yan').click(function(){

            var src=$(this).children('img').attr('src');
            var env = $("input[name='env']").val();
            if (env === undefined){
                env = imgUrl
            }

            if(src=== env+"/yan2.png"){
                $('.myapi .informate .row-element .row-inline input[name="secret"]').attr('type','password');//隐藏
                src=$(this).children('img').attr('src').replace('yan2.png','yan.png');
            }else {
                $('.myapi .informate .row-element .row-inline input[name="secret"]').attr('type','text');//显示
                src=$(this).children('img').attr('src').replace('yan.png','yan2.png');

            }
            $(this).children('img').attr('src',src);
        })
    });
</script>
<script src="{{ asset('js/ac/jquery.qrcode.min.js').'?id='.str_random(20)}}"></script>
@endsection
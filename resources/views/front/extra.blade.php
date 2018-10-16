<!-- 弹出层 -->
<div class="dialog hide">
    <div class="dialog-container">
        <div class="show-classify hide" id="changePwdContent">
            <!-- change password -->
            <div class="dialog-part">
                <div class="dialog-header">
                    <h1><span>@lang('ac.ChangePassword')</span><i class="iconfont icon-guanbi iconClose"></i></h1>
                </div>
                <div class="dialog-content">
                    <p>@lang('ac.leastCharacters')</p>
                    <div class="sheet" id="changePwd">
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.OldPassword'):</label>
                                <input type="password" id="oldpwd" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"></span>
                        </div>
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.NewPassword'):</label>
                                <input type="password" id="newpwd" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"></span>
                        </div>
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.ConfirmNewPassword'):</label>
                                <input type="password" id="againpwd" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"></span>
                        </div>
                        <div class="rank">
                            <button type="button" class="forPwdSub notPoint">@lang('ac.Submit')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="show-classify hide" id="changePinContent">
            <!-- set Pin -->
            <div class="dialog-part hide">
                <div class="dialog-header">
                    <h1><span>@lang('ac.SetPin')</span><i class="iconfont icon-guanbi iconClose"></i></h1>
                </div>
                <div class="dialog-content">
                    <p>@lang('ac.leastCharacters')</p>
                    <div class="sheet">
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.OldPassword'):</label>
                                <input type="password" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"><i class="iconfont icon-cuo"></i>@lang('ac.occupied')</span>
                        </div>
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.NewPassword'):</label>
                                <input type="password" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"><i class="iconfont icon-cuo"></i>@lang('ac.occupied')</span>
                        </div>
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.ConfirmNewPassword'):</label>
                                <input type="password" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"><i class="iconfont icon-cuo"></i>@lang('ac.occupied')</span>
                        </div>
                        <div class="rank">
                            <button type="button">@lang('ac.Submit')</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- change Pin -->
            <div class="dialog-part" id="changePinDom">
                <div class="dialog-header">
                    <h1><span>@lang('ac.ChangePin')</span><i class="iconfont icon-guanbi iconClose"></i></h1>
                </div>
                <div class="dialog-content">
                    <p>@lang('ac.PinleastCharacters')</p>
                    <div class="sheet" id="changePin">
                        @empty(!$pin)
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.OldPin'):</label>
                                <input type="password" id="oldpin" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"></span>
                        </div>
                        @endempty
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.NewPin'):</label>
                                <input type="password" id="newpin" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"></span>
                        </div>
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.ConfirmNewPin'):</label>
                                <input type="password" id="againpin" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green hide"></i>
                            </div>
                            <span class="tips"></span>
                        </div>
                        <p><a  onclick="sendEmail()" href="javascript:;" class="ForgetBtn">@lang('ac.ForgetPin')</a></p>
                        <div class="rank">
                            <button type="button" class="notPoint forPinSub">@lang('ac.Submit')</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- forget Pin -->
            <div class="dialog-part hide" id="forgetPinDom">
                <div class="dialog-header">
                    <h1><span>@lang('ac.ResetPin')</span><i class="iconfont icon-guanbi iconClose"></i></h1>
                </div>
                <div class="dialog-content">
                    <p>@lang('ac.PinleastCharacters')</p>
                    <div class="sheet">
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.NewPin'):</label>
                                <input type="password" name="pinNew"  placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green"></i>
                            </div>
                            <span class="tips"><i class="iconfont icon-cuo"></i><span>@lang('ac.occupied')</span></span>
                        </div>
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.NewPinAgain'):</label>
                                <input type="password"  name="pinNew_confirmation" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green"></i>
                            </div>
                            <span class="tips"><i class="iconfont icon-cuo"></i><span>@lang('ac.occupied')</span></span>
                        </div>
                        <div class="rank">
                            <div class="rank-row">
                                <label for="">@lang('ac.Emailverification'):</label>
                                <input type="text" name="pinCode" placeholder="" autocomplete="off">
                                <i class="iconfont icon-duihao ft-green"></i>
                            </div>
                            <span class="tips"><i class="iconfont icon-cuo"></i><span>@lang('ac.occupied')</span></span>
                        </div>
                        <div class="rank">
                            <button class="resetPin" type="button">@lang('ac.Submit')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 显示返回的状态 -->
<div class="show-status hide">
    <div class="show-stauts-result">
        <i class="iconfont icon-dui"></i>
        <p>@lang('ac.passwordIsSuccess')</p>
    </div>
</div>
<script>

    $(function () {
        tabs('#authen-verify li','.authencon .idcard');
        tabs('#security-list li','.security-part .verification');
        infoForm.init();
        /*密码组*/
        // keyFun('.line #authy-code',6)
        // enable('#verifyGoole')
        $.ms_DatePicker({
            YearSelector: "#select_year",
            MonthSelector: "#select_month",
            DaySelector: "#select_day"
        });
        $.ms_DatePicker({
            YearSelector: "#select_year2",
            MonthSelector: "#select_month2",
            DaySelector: "#select_day2"
        });

    });
    // tab切换
    function tabs(ele,dom) {
        $(ele).on('click',function(){
            $(this).addClass('active').siblings().removeClass('active');
            var index = $(this).index();
            $(dom).eq(index).removeClass('hide').siblings().addClass('hide')
        })
    }
</script>
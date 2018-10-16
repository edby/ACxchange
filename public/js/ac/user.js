function reJsonData() {
    var data;
    $.ajax({
        url:'/index/getJson/data'
        ,type:'GET'
        ,async:false
        ,success:function (res) {
            data = res.data;
        }
    });
    return data;
}
var  jsonData = reJsonData();
var infoForm = {
    btn1: '.infoID',
    idInput: '#infoEmail',
    email: '#infoEmail',
    closeBtn: '.iconClose',
    dom: '.showDialog',
    box: '.dialog',
    btn2:'.ForgetBtn',
    dom2: '#forgetPinDom',
    dom3: '#changePinDom',
    init: function(){
        this.modifyID()
        // this.isSave()
        this.closeDialog()
        this.according()
        this.submission()
    },
    modifyID: function(){
        var that = this
        $(this.btn1).on('click',function(e){
            $(this).prev().removeAttr('readonly')
            $(this).prev().focus();

            that.isSave()
        })
    },
    isSave: function(){
        var that = this;
        $(this.idInput).blur(function(){
            //失去焦点的时候，调用ajax
            layer.confirm('你确定修改Email吗？', {
                area: ['500px','250px'],
                btn: ['确定','取消'],//按钮
                title:'Change Email'
            }, function(){
                $.ajax({
                    url:'changeEmail',
                    type:'POST',
                    data: {email: $(that.idInput).val()},
                    dataType: 'json',
                    success: function(data){
                       console.log(data);
                    },
                    error: function(data){
                       console.log(data);
                    }
                })
                layer.closeAll();
            }, function(){
                //取消修改
                layer.closeAll();
            });

        });
    },
    closeDialog: function(){
        var $box = $(this.box)
        $(this.closeBtn).on('click',function(){
            $box.addClass('hide')
            $('body').removeClass('forbidden');

            $('.dialog-part .dialog-content .sheet .rank .rank-row input').val('');
            //清changePin 的val输入框
            $('#newpin').val('');
            $('#againpin').val('');
            //清resetPin 的val输入框 可以一次清空所有的 input
            $('.sheet .rank .rank-row input[name="pinNew"]').val('');
            $('.sheet .rank .rank-row input[name="pinNew_confirmation"]').val('');
            $('.sheet .rank .rank-row input[name="pinCode"]').val('');

        })
    },
    according: function(){
        var $box = $(this.box)
        var that = this
        $(this.dom).on('click',function(){
            $box.removeClass('hide')
            $('body').addClass('forbidden')
            $box.find('.show-classify').addClass('hide')
            if($(this).is('#changePwdText')){
                $box.find('#changePwdContent').removeClass('hide');
            }else{
                $box.find('#changePinContent').removeClass('hide')
                $box.find('.show-classify').find(that.dom3).removeClass('hide').siblings().addClass('hide');
            }
            
        })
    },
    submission: function() {
        // change pin
        this.formVerify('#changePin','#oldpin','#newpin','#againpin','.forPinSub')
        this.formOnFun('.forPinSub','#changePin','#oldpin','#newpin','#againpin','pin')
        // change pin

        this.forgetBtn(this.btn2,this.box,this.dom2);

        //change pwd
        this.formVerify('#changePwd','#oldpwd','#newpwd','#againpwd','.forPinSub')
        this.formOnFun('.forPwdSub','#changePwd','#oldpwd','#newpwd','#againpwd','pwd')
        //change pwd

        // idCard
        this.idCard('#idCard','#card1','#card2','#card3')
        this.passPort('#passPort','#protImg1','#protImg2')

        this.security('#verifyAuthy','#verifyBtn1','email')
        this.security('#verifySms','#verifyBtn2','sms')
        
        this.gooleVer('#gooleVerify','#verifyGoole')
    },
    formVerify: function(dom,oldP,newP,againP,btn) {
        $(dom).find(':input').blur(function(){
            $(this).parent().next().removeClass("error").text("");
            var id = $(this).parents('.sheet').attr('id');
            var old;
            var ne;
            var confirm;
            var match;
            if (id==='changePwd'){
                old = jsonData['Password cannot be empty'];
                ne = jsonData['Password cannot be empty'];
                confirm = jsonData['Password cannot be empty'];
                match = jsonData['The two passwords don\'t match'];
            }else {
                old = jsonData['New Pin cannot be empty'];
                ne = jsonData['New Pin cannot be empty'];
                confirm = jsonData['New Pin cannot be empty'];
                match = jsonData['The two pin don\'t match'];
                // old = jsonData['Please enter an old pin'];
                // ne = jsonData['Please enter an new pin'];
                // confirm = jsonData['Please confirm the pin'];
                // match = jsonData['The two pin don\'t match'];
            }
            var regExp = /^[a-zA-Z0-9!"\#$%&'()*+,-./:;<=>?@\[\\\]^_`\{\|\}\~]{6,18}$/;
            if( $(this).is(oldP)){
                var uname = $(this).val();
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+old;
                    $(this).parent().next().addClass("error").html(onMessage);
                    $(this).parent().next().css('visibility','visible');
                    $(this).next('i').addClass('hide')
                }else if( !regExp.test(uname) ){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+old;
                    $(this).parent().next().addClass("error").html(onMessage);
                    $(this).parent().next().css('visibility','visible');
                    $(this).next('i').addClass('hide')
                }else{
                    $(this).next('i').removeClass('hide')
                }
            }
            if( $(this).is(newP) ){
                var pwd = $(this).val();
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+ne;
                    $(this).parent().next().addClass("error").html(onMessage);
                    $(this).parent().next().css('visibility','visible');
                    $(this).next('i').addClass('hide')
                }else if( !regExp.test(pwd) ){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+ne;
                    $(this).parent().next().addClass("error").html(onMessage);
                    $(this).parent().next().css('visibility','visible');
                    $(this).next('i').addClass('hide')
                }else{
                    $(this).next('i').removeClass('hide')
                }
            }
            if( $(this).is(againP) ){
                var repwd = $(this).val();
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+confirm;
                    $(this).parent().next().addClass("error").html(onMessage);
                    $(this).parent().next().css('visibility','visible');
                    $(this).next('i').addClass('hide')
                }else if( repwd != $(newP).val() ){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+match;
                    $(this).parent().next().addClass("error").html(onMessage);
                    $(this).parent().next().css('visibility','visible');
                    $(this).next('i').addClass('hide')
                }else{
                    $(this).next('i').removeClass('hide')
                }
            }
            if($(oldP).val() != '' && $(newP).val() !== '' &&  $(againP).val() !=''  ){
                $(btn).removeClass('notPoint')
            }else{
                $(btn).addClass('notPoint')
            }
        }).keyup(function()
        {
            $(this).triggerHandler("blur");
        });
    },
    forgetBtn: function(btn,dom,dom2) {
        var $box = $(dom)
        
        $(btn).on('click',function() {
            $box.removeClass('hide')
            $('body').addClass('forbidden')
            $box.find('.show-classify').addClass('hide')
            $box.find('#changePinContent').removeClass('hide')
            $box.find('.show-classify').find(dom2).removeClass('hide').siblings().addClass('hide');
        })
    },
    formOnFun:function(btn,dom,v1,v2,v3,part){
        var that = this
        $(btn).on("click", function(){
            $(dom).find(':input').trigger("blur");
            var para1 = $(v1).val();
            var para2 = $(v2).val();
            var para3 = $(v3).val();

            if(part === 'pin'){
                if (para1 === undefined ){
                    var data = {pin:para2,pin_confirmation:para3};
                }else {
                    var data = {old_pin:para1,pin:para2,pin_confirmation:para3};
                }
                $.ajax({
                    url:'changePin'
                    ,type:'POST'
                    ,data:data
                    ,success:function (data) {
                        console.log(data.status);
                        if (data.status === 1){
                            that.showResult(1,'.show-status', data.message);
                            return false;
                        }else {
                            that.showResult(0,'.show-status',data.message);
                            return false;
                        }
                        //清changePin 的val输入框
                        $('#newpin').val('');
                        $('#againpin').val('');
                        //清resetPin 的val输入框
                        $('.sheet .rank .rank-row input[name="pinNew"]').val('');
                        $('.sheet .rank .rank-row input[name="pinNew_confirmation"]').val('');
                        $('.sheet .rank .rank-row input[name="pinCode"]').val('');

                    }
                    ,error:function (msg) {
                        var mg = JSON.parse(msg.responseText);
                        for (var k in mg.errors){
                            var tip = mg.errors[k][0];
                        }
                        if (k===undefined){
                            layer.msg(jsonData['systemError'], {
                                offset: 'auto',
                                anim: 6,
                                area:['420px']
                            });
                            return false;
                        }

                        layer.msg(k+' : '+tip, {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                        return false;
                    }
                });
                /*ajax 成功后失败后的时候调用*/
                //0为false 1位true，或者传值false 或者 true
                // that.showResult(0,'.show-status','Submitted fail');
                // $(dom).find(':input').val('');
                // $(dom).find('span.tips').text('')
                // $(dom).find(':input').next('i').addClass('hide')
                /*ajax 成功后失败后的时候调用*/

            }else{
                var data = {old_password:para1,password:para2,password_confirmation:para3};
                $.ajax({
                    url:'changePassword'
                    ,type:'POST'
                    ,data:data
                    ,success:function (data) {
                        console.log(data.status);
                        if (data.status === 1){
                            $('.dialog-part .dialog-content .sheet .rank .rank-row input').val('');
                            that.showResult(1,'.show-status',data.message);
                            window.location.href = '/login';
                        }else {
                            $('.dialog-part .dialog-content .sheet .rank .rank-row input').val('');
                            that.showResult(0,'.show-status',data.message);
                        }
                    }
                    ,error:function (msg) {
                        var mg = JSON.parse(msg.responseText);
                        for (var k in mg.errors){
                            var tip = mg.errors[k][0];
                        }

                        if (k===undefined){
                            layer.msg(jsonData['systemError'], {
                                offset: 'auto',
                                anim: 6,
                                area:['420px']
                            });
                            return false;
                        }

                        layer.msg(k+' : '+tip, {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                        return false;
                    }
                });
                // $(dom).find(':input').val('');
                // $(dom).find('span.tips').text('')
                // $(dom).find(':input').next('i').addClass('hide')
            }
            
        });
        
    },
    showResult: function(status,ele,msg) {
        $(this.box).addClass('hide')
        $(ele).removeClass('hide');
        if(status == 'true' || status == 1){
            //正确信息
            $(ele).find('i').removeClass().addClass('iconfont icon-dui')
        }else{
            //错误信息
            $(ele).find('i').removeClass().addClass('iconfont icon-open-warn')
        }
        $(ele).find('p').text(msg);

        setTimeout(function() {
            $(ele).addClass('hide');
        }, 4000);
    },
    idCard: function(formid,id1,id2,id3){
        // idCard
        $(formid).find(':input').blur(function(){
            $(this).next('span.tips').removeClass("error").text("");
            if( $(this).is("#fname") ){
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter fist name'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            if( $(this).is("#lname") ){
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter last name'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            
            if( $(this).is("#idnum") ){
                var idnum = $(this).val();
                var regExp = /^[0-9A-Za-z]{8,30}$/
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter ID Number'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }else if( !regExp.test(idnum) ){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter ID Number'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            // /^[0-9A-Za-z]+$/
            if( $(this).is("#raddress") ){
                
                
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter residential address'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            
            if( $(this).is("#phone") ){
                var tel = $(this).val();
                var regExp = /^\d{6,18}$/
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter phone'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }else if( !regExp.test(tel) ){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter the correct mobile number'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            if($('#raddress').val() != '' && $('#phone').val() != '' && $('#idnum').val() != ''){
                $('.saveIdCard').removeClass('notPoint')
            }else{
                $('.saveIdCard').addClass('notPoint')
            }
        }).keyup(function()
        {
            $(this).triggerHandler("blur");
        });
        $(".saveIdCard").on("click", function() {
            $(formid).find(':input').trigger("blur");
            if($(id1).val() == '' || $(id2).val() == '' || $(id3).val() == '' ){
                layer.msg("<span style='color:#fff;'>"+jsonData['Please upload photos of your ID card']+"</span>",{icon:5,area: ['420px','']})
                return
            }
            var numError = $(formid).find('.error').length;
            if(numError){
                return false;
            }
            var formData = new FormData();
            formData.append('first_name', $("input[ name='fname' ]").val());
            formData.append('last_name', $("input[ name='lname' ]").val());
            formData.append('nationality', $("select[ name='nation' ]").val());
            formData.append('card_number', $("input[ name='idNum' ]").val());
            formData.append('year', $("select[ name='selectYear' ]").val());
            formData.append('month', $("select[ name='selectMonth' ]").val());
            formData.append('day', $("select[ name='selectDay' ]").val());
            formData.append('residential_address', $("input[ name='raddress' ]").val());
            formData.append('region_ode', $("select[ name='idCode' ]").val());
            formData.append('phone', $("input[ name='idPhone' ]").val());
            formData.append('img_front', $('input[name=idfile1]')[0].files[0]);
            formData.append('pFrontBin', $("input[name='card1']").val());
            formData.append('img_back', $('input[name=idfile2]')[0].files[0]);
            formData.append('pRearBin', $("input[ name='card2']").val());
            formData.append('img_hand', $('input[name=idfile3]')[0].files[0]);
            formData.append('pHandBin', $("input[ name='card3']").val());
            var index = layer.load(0, {shade: [0.5,'#2c3557 ']})
            $.ajax({
                url:'authIdCard'
                ,data:formData
                ,type:'POST'
                ,contentType: false
                ,processData: false
                ,success:function (msg) {
                    if (msg.status === 1){
                        setTimeout(function () {
                            layer.close(index);
                            window.location.reload();
                        },800);
                    }else {
                        layer.close(index);
                        layer.msg(msg.message, {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                    }
                }
                ,error:function (msg) {
                    layer.close(index);
                    var mg = JSON.parse(msg.responseText);
                    for (var k in mg.errors){
                        var tip = mg.errors[k][0];
                    }
                    if (k===undefined){
                        layer.msg(jsonData['systemError'], {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                        return false;
                    }

                    layer.msg(k+' : '+tip, {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                    return false;
                }
            });
        });
    },
    passPort: function(dom,val1,val2){
        $(dom).find(':input').blur(function(){
            $(this).next('span.tips').removeClass("error").text("");
            if( $(this).is("#fname2")){
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter fist name'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            if( $(this).is("#lname2") ){
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter last name'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            if( $(this).is("#raddress2") ){


                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter residential address'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            if( $(this).is("#phone2") ){
                var tel = $(this).val();
                var regExp = /^\d{6,18}$/
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter phone'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }else if( !regExp.test(tel) ){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter the correct mobile number'];
                    $(this).next('span.tips').addClass("error").html(onMessage);
                }
            }
            if( $(this).is(".portid") ){
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter your license number'];
                    $(this).next().addClass("error").html(onMessage);
                    $('.licenseId').addClass('notPoint')
                }else{
                    $('.licenseId').removeClass('notPoint')
                }
            }
            if($('#raddress2').val() != '' && $('#phone2').val() != '' && $('.portid').val() != ''){
                $('.licenseId').removeClass('notPoint')//改变按钮颜色状态
            }else{
                $('.licenseId').addClass('notPoint')
            }
            
        }).keyup(function()
        {
            $(this).triggerHandler("blur");
        });
        $(".licenseId").on("click", function(){
            $(dom).find(':input').trigger("blur");
            if($(val1).val() == '' || $(val2).val() == ''){
                layer.msg('<span style="color:#fff;">'+jsonData['Please upload your passport']+'</span>',{icon:5})
                return
            }
            var numError = $(dom).find('.error').length;
            if(numError){
                return false;
            }
            var id = $('.portid').val();
            var img1 = $(val1).val();
            var img2 = $(val2).val();

            var formData = new FormData();
            formData.append('first_name', $("input[ name='fname2' ]").val());
            formData.append('last_name', $("input[ name='lname2' ]").val());
            formData.append('nationality', $("select[ name='nation2' ]").val());
            formData.append('passport_number', $("input[ name='passNum' ]").val());
            formData.append('year', $("select[ name='selectYear2' ]").val());
            formData.append('month', $("select[ name='selectMonth2' ]").val());
            formData.append('day', $("select[ name='selectDay2' ]").val());
            formData.append('residential_address', $("input[ name='raddress2' ]").val());
            formData.append('region_ode', $("select[ name='idCode2' ]").val());
            formData.append('phone', $("input[ name='phone2' ]").val());
            formData.append('img_front', $('input[name=prFile1]')[0].files[0]);
            formData.append('pFrontBin', $("input[name='prFileBin1']").val());
            formData.append('img_back', $('input[name=prFile2]')[0].files[0]);
            formData.append('pRearBin', $("input[ name='prFileBin2']").val());
            var index = layer.load(0, {shade: [0.5,'#2c3557 ']})
            $.ajax({
                url:'authPassport'
                ,data:formData
                ,type:'POST'
                ,contentType: false
                ,processData: false
                ,success:function (msg) {
                    if (msg.status === 1){
                        setTimeout(function () {
                            layer.close(index);
                            window.location.reload();
                        },800);
                    } else {
                        layer.close(index);
                        layer.msg(msg.message, {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                    }
                }
                ,error:function (msg) {
                    layer.close(index);
                    var mg = JSON.parse(msg.responseText);
                    for (var k in mg.errors){
                        var tip = mg.errors[k][0];
                    }

                    if (k===undefined){
                        layer.msg(jsonData['systemError'], {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                        return false;
                    }

                    layer.msg(k+' : '+tip, {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                    return false;
                }
            });
        });
    },
    security: function(form,btn,type){
        $(form).find(':input').blur(function(){
            $(this).parent().next().removeClass("error").text("");
            if( $(this).is(".verifyPhone") ){
                var tel = $(this).val();
                var regExp = /^\d{6,18}$/
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter your phone number'];
                    $(this).parent().next().addClass("error").html(onMessage);
                }else if( !regExp.test(tel) ){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['The phone number is incorrect'];
                    $(this).parent().next().addClass("error").html(onMessage);
                }
            }
            
            if( $(this).is(".authEmail") ){
                var email = $(this).val();
                var regExp = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ;
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Enter the email'];

                    $(this).parent().next().addClass("error").html(onMessage);
                }else if( !regExp.test(email) ){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter the correct email address'];
                    $(this).parent().next().addClass("error").html(onMessage);
                }
            }
            if( $(this).is(".verifyCode") ){
                var regExp = /^\d{4,6}$/;
                var code = $(this).val();
                
                if( this.value == ""){
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Enter the code'];
                    $(this).parent().next('span.tips').addClass("error").html(onMessage);
                }else if( !regExp.test(code) ){
                    var onMessage = 'Please enter 4-6 bit verification code';
                    $(this).parent().next('span.tips').addClass("error").html(onMessage);
                }
            }

            if($('.verifyPhone').val() == '' || $('.verifyCode').val() == ''){
                $(btn).addClass('notPoint')
            }else{
                $(btn).removeClass('notPoint')
            }

        }).keyup(function()
        {
            $(this).triggerHandler("blur");
        });
        $(btn).on("click", function()
        {
            $(form).find(':input').trigger("blur");
            var numError = $(form).find('.error').length;
            if(numError){
                return false;
            }
            if(type=='email'){
                var area = $(".verifyArea1 option:selected").val();
                var iphone = $(".authPnum").val();
                var authemail = $('.authEmail').val();
                var code = $('.codePhone').val();
                console.log(area,iphone,authemail,code)
                // $.ajax({
                //     type: "POST",
                //     url: 'url',
                //     data: {},
                //     dataType: "json",
                //     async: true,
                //     success: function(arr)
                //     {
                //         //注册成功
                //     }
                // });
            }
            if(type=='sms'){
                var area = $(".verifyArea2 option:selected").val();
                var iphone = $(".authPhone").val();
                var code = $('.codeSMS').val();
                console.log(area,iphone,code)
                // $.ajax({
                //     type: "POST",
                //     url: 'url',
                //     dataType: "json",
                //     data: {},
                //     async: true,
                //     success: function(arr)
                //     {
                //         //注册成功
                //     }
                // });
            }

        });
    },
    gooleVer: function(dom,btn){
        $(dom).find(':input').blur(function(){
            $(this).parent().next().removeClass("error").html("");
            if( $(this).is(".gooleCode") ){
                var code = $(this).val();
                var regExp = /^\d{6}$/;
                if( this.value == "")
                {
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['Please enter Google Verification Code'];
                    $(this).parent().next().addClass("error").html(onMessage);
                    $(btn).addClass('notPoint');
                }
                else if( !regExp.test(code) )
                {
                    var onMessage = '<i class="iconfont icon-cuo"></i>'+jsonData['The Google Verification Code is incorrect'];
                    $(this).parent().next().addClass("error").html(onMessage);
                    $(btn).addClass('notPoint');
                }else{
                    $(btn).removeClass('notPoint');
                }
            }
        }).keyup(function()
        {
            $(this).triggerHandler("blur");
        });
        $(btn).on("click", function()
        {
            $(dom).find(':input').trigger("blur");
            var numError = $(dom).find('.error').length;
            if(numError){
                return false;
            }
            var goolecode = $(".gooleCode").val();
            var secret = $("#keySecret").html();
            $.ajax({
                type: "POST",
                url: '/user/googleAuth',
                dataType: "json",
                data: {code:goolecode,secret:secret},
                async: true,
                success: function(data)
                {
                    console.log(data.status);
                    layer.msg(data.message, {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                },
                error:function (data) {
                    var mg = JSON.parse(data.responseText);
                    for (var k in mg.errors){
                        var tip = mg.errors[k][0];
                    }

                    if (k===undefined){
                        layer.msg(jsonData['systemError'], {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                        return false;
                    }

                    layer.msg(k+' : '+tip, {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                    return false;
                }
            });
        });
    }
}

$(function(){
    /*change img*/
    upload('#idfile1','#card1')
    upload('#idfile2','#card2')
    upload('#idfile3','#card3')
    upload('#port1','#protImg1')
    upload('#port2','#protImg2')
    /*change img*/
})

function upload(dom,input) {
    $(dom).on("change",function(){
        console.log(1)
        var imgFile = $(dom)[0].files[0];
        var reader = new FileReader();
        var $this = $(this)
        console.log($this)
        reader.readAsDataURL(imgFile)
        reader.onload = function(e){
            $this.parent().parent().find('img').attr('src',this.result)
            $(input).val(this.result)
        }
    })
}


//发送短信验证码
var GetCode = {
    sendEmailCode: function(input,button){
        var regExp = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ;
        var $input = $(input);
        var count = 60;

        $(button).on('click',function(){
            if($input.val() == ''){
                layer.msg('<span style="color:#fff">'+jsonData['The mailbox address cannot be empty']+'</span>',{icon:5});
                return;
            }else if(!(regExp.test($input.val()))){
                layer.msg('<span style="color:#fff">'+jsonData['Please enter the correct email address']+'</span>',{icon:5});
                return;
            }else{

                $(button).attr('disabled','disabled');
                layer.msg('<span style="color:#fff">'+jsonData['Send success']+'</span>',{icon:6});
                var getMessage;
                getMessage = setInterval(function(){
                    count--;
                    if(count == 0){
                        clearInterval(getMessage);
                        $(button).removeAttr('disabled')
                        $(button).removeClass('undisabled').find('span').text('Send');
                        count = 60
                        return;
                    }
                    $(button).addClass('undisabled').find('span').text('resend('+count+')');

                },1000)
                /*
                $.ajax({
                    url: 'xxx',
                    type: 'post',
                    data: {
                        mobile: $input.val()
                    },
                    success: function(data){
                        console.log(data)
    
                        //倒计时
                        getMessage = setInterval(function(){
                            count--;
                            if(count == 0){
                                clearInterval(getMessage);
                                $(button).removeAttr('disabled')
                                $(button).removeClass('ftgray').find('span').text('Send');
                                count = 60
                                return;
                            }
                            $(button).addClass('ftgray').find('span').text('resend('+count+')');
                            
                        },1000)
    
                    },
                    error: function(data){
                        console.log(data)
                    }
                })
                */
            }
        })
    },
    sendPhoneCode: function(input,button){
        var regExp = /^1[3|4|5|7|8]\d{9}$/;

        var $input = $(input);

        var count = 60;

        $(button).on('click',function(){
            if($input.val() == ''){
                layer.msg('<span style="color:#fff">'+jsonData['The phone number can\'t be empty']+'</span>',{icon:5});
                return;
            }else if(!(regExp.test($input.val()))){
                layer.msg('<span style="color:#fff">'+jsonData['Please fill in the correct cell phone number']+'</span>',{icon:5});
                return;
            }else{
                $(button).attr('disabled','disabled');
                layer.msg('<span style="color:#fff">'+jsonData['Send success']+'</span>',{icon:6});
                var getMessage;
                getMessage = setInterval(function(){
                    count--;
                    if(count == 0){
                        clearInterval(getMessage);
                        $(button).removeAttr('disabled')
                        $(button).removeClass('undisabled').find('span').text('Send');
                        count = 60
                        return;
                    }
                    $(button).addClass('undisabled').find('span').text('resend('+count+')');

                },1000)
                /*
                $.ajax({
                    url: 'xxx',
                    type: 'post',
                    data: {
                        mobile: $input.val()
                    },
                    success: function(data){
                        console.log(data)
    
                        //倒计时
                        getMessage = setInterval(function(){
                            count--;
                            if(count == 0){
                                clearInterval(getMessage);
                                $(button).removeAttr('disabled')
                                $(button).removeClass('notPoint').find('span').text('Send');
                                count = 60
                                return;
                            }
                            $(button).addClass('notPoint').find('span').text('resend('+count+')');
                            
                        },1000)
    
                    },
                    error: function(data){
                        console.log(data)
                    }
                })
                */
            }
        })
    }
}

GetCode.sendEmailCode('.authEmail','.sendAuthEmail')
GetCode.sendPhoneCode('.authPhone','#sendAuthPhone')

function keyFun(input,num){
    $(input).find('input').each(function (r, a) {
        $(a).on("focus", function (e) {
            $(e.target).val("");
            $('#verifyGoole').attr('disabled','disabled')
            $('#verifyGoole').addClass('notPoint');
        })
        $(a).on("keydown", function () {
            return !1
        })
        $(a).on("keyup", function (a) {
            if (a.keyCode >= 96 && a.keyCode <= 105 || a.keyCode >= 48 && a.keyCode <= 57) {
                if (num != (r+1)) {
                    $(this).val(a.key);
                    $(input).find("input")[r + 1].focus();
                    
                    
                } else {
                    $(this).val(a.key);
                    $(this).blur();
                    
                    $('#verifyGoole').removeAttr('disabled');
                    $('#verifyGoole').removeClass('notPoint');
                    
                }
            }
            if (8 !== a.keyCode) {
                return !1;
            } else {
                if (0 !== r) {
                    $(input).find("input")[r-1].focus();
                }
            }
        })
        

    })
}

function enable(btn) {
    $(btn).on('click',function(){
        var result = ''
        $('#authy-code input').each(function(){
            result=result+ $(this).val();
        });
        console.log(result)
    })
}

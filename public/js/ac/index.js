var mypage=1;
var marketpage=1;
var openpage=1;
var historypage=1;

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
//算法面向对象
Number.prototype.add = function (arg) {
    var r1,r2,m;
    try {r1=this.toString().split(".")[1].length}catch (e){r1=0;}
    try {r2=arg.toString().split(".")[1].length}catch (e){r2=0;}
    m = Math.pow(10,Math.max(r1,r2));
    return (this*m+arg*m)/m;
};
Number.prototype.sub = function (arg) {
    var r1,r2,m;
    try {r1=this.toString().split(".")[1].length}catch (e){r1=0;}
    try {r2=arg.toString().split(".")[1].length}catch (e){r2=0;}
    m = Math.pow(10,Math.max(r1,r2));
    return (this*m-arg*m)/m;
};
Number.prototype.mul = function (arg) {
    var m=0,s1=this.toString(),s2=arg.toString();
    try {m += s1.split(".")[1].length}catch (e){}
    try {m += s2.split(".")[1].length}catch (e){}
    return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
};
Number.prototype.div = function (arg) {
    var t1 =0,t2=0,r1,r2;
    try {t1=this.toString().split(".")[1].length}catch (e){}
    try {t2=arg.toString().split(".")[1].length}catch (e){}
    with (Math){
        r1 = Number(this.toString().replace(".",""));
        r2 = Number(arg.toString().replace(".",""));
        return (r1/r2)*pow(10,t2-t1);
    }
};
//var test = new Number('12.2354');
//var test1 = test.add('123.001455440124');
//console.log(test1);
//发送邮箱
function sendEmail(){
    $.ajax({
        url:'/user/pinForgetSendEmail'
        ,type:"POST"
        ,success:function (data) {
            console.log(data.message);
        }
        ,error:function (msg){
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
}
//user重置pin
function newPin(PinNew,Pinagain,pincode){
    console.log('PinNew=>'+PinNew+'====='+'PinNew=>'+Pinagain+'====='+'PinNew=>'+pincode);
    $.ajax({
        url:'/user/resetPin'
        ,type:"POST"
        ,data:{pin:PinNew,pin_confirmation:Pinagain,code:pincode}
        ,success:function (data) {
            //user
            console.log(data);
            if (data.status == 1){
                //清changePin 的val输入框
                $('#newpin').val('');
                $('#againpin').val('');
                //清resetPin 的val输入框
                $('.sheet .rank .rank-row input[name="pinNew"]').val('');
                $('.sheet .rank .rank-row input[name="pinNew_confirmation"]').val('');
                $('.sheet .rank .rank-row input[name="pinCode"]').val('');

                $('.dialog').addClass('hide')
                $('body').removeClass('forbidden');
                //cancel按钮
                $('.forgetPin2').hide();
                successfully(data.message);
            }else {
                error(data.message);
            }
        }
        ,error:function (msg){
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

            error(k+' : '+tip);
            return false;
        }
    });
}

//成功提示
function successfully(data){
    clearTimeout();

    var data=data;

    $('.successbox').stop().fadeToggle(500);
    setTimeout(function(){
        $('.successbox').stop().fadeToggle(500);
    },2000);

    $('.successbox .success_tips').html(data);

}
//错误提示
function error(data){
    clearTimeout();

    var data=data;

    $('.errorbox').stop().fadeToggle(500);
    setTimeout(function(){
        $('.errorbox').stop().fadeToggle(500);
    },2000);

    $('.errorbox .error_tips').html(data);

}

//复制功能
function jsCopy(id) {
    var Url = document.getElementById(id);
    Url.select();
    document.execCommand("Copy");
}

//切换买，卖窗口显示和隐藏buy 0/sell 1
function buyAndSell(index){
    $('#trade .content-right .order0 .select0').children('span').eq(index).addClass('acty').siblings().removeClass('acty');
    $('#trade .content-right .order0 .bigbox').children('div').eq(index).css('display','block').siblings().css('display','none');

}

function activ(){
    //buy/sell=price
    var price2=$('#trade .market0 .lists-bit ul li.activ-bit').find('div').eq(1).html();
    $('.order0 .bigbox .orderlist input[name="price"]').val(price2);
    //默认币种
    var bit2=$('#trade .market0 .lists-bit ul li.activ-bit').find('div').eq(0).find('span').text().toLowerCase();
}
//获取货币全称和简称
function getFullName(currAbb,currency){
    $.ajax({
        url:'/wallet/getFullName'
        ,type:'POST'
        ,data:{currAbb:currAbb,currency:currency}
        ,success:function (data) {
            $('.title0 .bit-2').html(data.data.currName+" ("+currAbb+")");
            $('.title0 .bit-1').html(data.data.currencyName+" ("+currency+")");

        }
    });


}
//判断哪个币
function bits(){
    var bit=$('#trade .market0 .lists-bit ul li.activ-bit').find('div').eq(0).find('span').text().toLowerCase();
    return bit;
}
//获取market的volume值
function volume(){
    var volume=$('#trade .market0 .lists-bit ul li.activ-bit').find('div').eq(3).html();
    return volume;

}
//获取market的index值
function index(){
    var index=$('#trade .market0 .lists-bit ul li.activ-bit').index();
    return index;

}
//判断哪个币2usd/btc/..
function bits2(){
    var bit2=$('#trade .market0 .manu0 li.act2').text().toLowerCase();
    return bit2;
}
//保留小数点后8位，不四舍五入
function  numb(value){
    var num=8;
    var a, b, c, i;
    a = value.toString();
    b = a.indexOf(".");
    c = a.length;
    if (num == 0) {
        if (b != -1) {
            a = a.substring(0, b);
        }
    } else {//如果没有小数点
        if (b == -1) {
            a = a + ".";
            for (i = 1; i <= num; i++) {
                a = a + "0";
            }
        } else {//有小数点，超出位数自动截取，否则补0
            a = a.substring(0, b + num + 1);
            for (i = c; i <= b + num; i++) {
                a = a + "0";
            }
        }
    }
    return a;

}
//判断买or卖
function buyorsell(){
    var type0=$('.order0 .select0 .acty').html();
    var num=10;
    if(type0 == 'Buy'|| type0=='买' || type0 =='買'){
        num=10;

    }else if(type0=='Sell'|| type0=='卖' || type0 =='賣'){
        num=20
    }
    return num
}
//24小时最高最低统计
function  tranSummary(currAbb,currency){
    $.ajax({
        url:'/trade/tranSummary',
        type:'POST',
        data:{currAbb:currAbb,currency:currency},
        success:function(data){
            $('#trade .details strong.lastPrice').html(data.data.lastPrice);
            $('#trade .details strong.low').html(data.data.low);
            $('#trade .details strong.high').html(data.data.high);
            $('#trade .details strong b.change').html(data.data.change);
            $('#trade .details strong b.volume').html(data.data.volume);

            $('#trade .details strong a.curr').html(data.data.currAbb);
            var classNo=data.data.status=='down'?'up':'down';
            var status=data.data.status;
            $('#trade .details strong.status').addClass(status).removeClass(classNo);


        },error:function(){
            console.log('请求失败2222');
        }

    })



}
//Pin接口传pin给后台
function pin(id){
    $.ajax({
        url:'/order/cancelOrder',
        type:'POST',
        data:{id:id},
        success:function(data){
            //关闭窗口
            if (data.status){
                successfully(data.message);
                //$('.modelPin').fadeToggle();
                //取消成功，表格数据重新加载
                var tradeCurr=bits();
                var curr=bits2();

                $('.sellorbuy .sell2 table tbody.sellOrders').html('');//清空
                sellOrders('1', tradeCurr,'20',curr);

                $('.sellorbuy .buy2 table tbody.buyOrders').html('');//清空
                buyOrders('1',tradeCurr,'10',curr);

                $('.addtable .buy2 table tbody.marketHistory').html('');//清空
                marketHistory('1',tradeCurr,curr);

                $('.addtable .sell2 table tbody').html('');//清空
                open('1',tradeCurr,curr);

                $('.history_table tbody.history_body').html('')//清空
                orderHistory('1',false,curr);

                ////清空输入框pin
                //$('.modelPin .modal2 .bodyPin div input').val('');
                //balance();

                //Order页面刷新回到指定Market币种
                var currAbb=$('.order_head th.openbtn>span').html().toLowerCase();
                $('.bigbox_in_order .order_table tbody.order_body').html('')//清空
                if(currAbb === 'All'){
                    openOrders('1',false,curr);
                }else {
                    openOrders('1',currAbb,curr);
                }
            }else {
                error(data.message);
            }
        },error:function(){
            console.log('请求pin失败');
        }

    })

}
//获取不同的price
function price(type,currAbb){
    $.ajax({
        url:'/trade/price',
        type:'POST',
        data:{type:type,currAbb:currAbb},
        success:function(data){
            console.log('price:'+data)
            var bos=$('.order0 .select0 .acty').html();
            var price=data.data.price;
            console.log('后台获取的price:'+price);
            if(bos == 'Buy'|| bos=='买'|| bos=='買'){
                console.log('buy的price:'+price);
                var amount=$('.buybox .casket input[name="amount"]').val();
                var amount0=new Number(amount);
                var payment0=amount0.mul(price);
                //如果是科学技术法转为数字
                if ((payment0.toString().indexOf('E') != -1) || (payment0.toString().indexOf('e') != -1)) {
                    payment0=payment0.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var payment=numb(payment0);

                var rep=$('#trade .order0 .bigbox .orderlist .order_fee .buyfee0').html();
                var feeRate=Number(rep.replace(/\%/g,''));

                var feeA=amount0.mul(feeRate);
                //如果是科学技术法转为数字
                if ((feeA.toString().indexOf('E') != -1) || (feeA.toString().indexOf('e') != -1)) {
                    feeA=feeA.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var feeB=new Number(feeA);
                var feeC=feeB.mul('0.01');
                //如果是科学技术法转为数字
                if ((feeC.toString().indexOf('E') != -1) || (feeC.toString().indexOf('e') != -1)) {
                    feeC=feeC.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var fee=numb(feeC);


                //获取 Received=(1-feeRate)*payment
                var num= feeRate.mul(0.01);
                var num2=new Number(num);
                var num3=Number(1);
                var numall=num3.sub(num2);
                var paymentend=amount0.mul(numall);
                //如果是科学技术法转为数字
                if ((paymentend.toString().indexOf('E') != -1) || (paymentend.toString().indexOf('e') != -1)) {
                    paymentend=paymentend.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var received=numb(paymentend);

                //buy对应输出
                $('.buybox .casket input[name="price"]').val(price);
                $('.buybox .casket input[name="payment"]').val(payment);
                $('.buybox .casket input[name="fee"]').val(fee);
                $('.buybox .casket input[name="received"]').val(received);

            }
            else if(bos=='Sell' || bos== '卖' ||bos=='賣'){
                console.log('sell的price:'+price);

                var amount=$('.sellbox .casket input[name="amount"]').val();
                var amount0=new Number(amount);
                var total0=amount0.mul(price);
                //如果是科学技术法转为数字
                if ((total0.toString().indexOf('E') != -1) || (total0.toString().indexOf('e') != -1)) {
                    total0=total0.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var total=numb(total0);

                var rep=$('#trade .order0 .orderlist .sell_fee .sellfee0').html();
                var feeRate=Number(rep.replace(/\%/g,''));
                var feeRate0=new Number(feeRate);
                var fee0=feeRate0.mul(total);
                //如果是科学技术法转为数字
                if ((fee0.toString().indexOf('E') != -1) || (fee0.toString().indexOf('e') != -1)) {
                    fee0=fee0.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var fee2=new Number(fee0);
                var fee3=fee2.mul('0.01');
                //如果是科学技术法转为数字
                if ((fee3.toString().indexOf('E') != -1) || (fee3.toString().indexOf('e') != -1)) {
                    fee3=fee3.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var fee=numb(fee3);

                var num= feeRate0.mul(0.01);
                //如果是科学技术法转为数字
                if ((num.toString().indexOf('E') != -1) || (num.toString().indexOf('e') != -1)) {
                    num=num.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var num2=new Number(num);
                var num3=Number(1);
                var numall=num3.sub(num2);

                //var netTotal=new Number(total);
                var netTotal=new Number(total0); //--修改

                var netTotal2=netTotal.mul(numall);
                //如果是科学技术法转为数字
                if ((netTotal2.toString().indexOf('E') != -1) || (netTotal2.toString().indexOf('e') != -1)) {
                    netTotal2=netTotal2.toFixed(12);
                }
                //如果是科学技术法转为数字结束
                var netTotal3=numb(netTotal2);

                //sell对应输出
                $('.sellbox .casket input[name="price"]').val(price);
                $('.sellbox .casket input[name="total"]').val(total);
                $('.sellbox .casket input[name="fee"]').val(fee);
                $('.sellbox .casket input[name="netTotal"]').val(netTotal3);

            }

        },
        error:function(){

        }

    })

}

// 第四屏--动画散开
function Animat(num){
    $('span.img > div').animate({//显示虚线
        width:'show'
    },num,function () {
        $('span.img > img').show(200,function () {//显示图片
            $('.small-row .wen').css('visibility','visible');//显示文字
            $('.small-row .wen p').show(200);//显示文字段落
        });
    })
}
//第四屏--动画收起
function AnimatHide() {// 收起
    $('.small-row .wen').css('visibility',' hidden');
    $('.small-row .wen p').hide(100);
    $('span.img > img').hide(100);
    $('span.img > div').animate({
        width:'hide'
    },100)
}
//图表动画函数
function addEchart(env) {
    //判断屏幕宽度(判断是PC还是移动端)
    var count=$(document).width();
    if (count<=1024){
        $('.echar').html('<img src="'+env+'/img.png" style="width:100%;height: auto;position: absolute;left: 50%;top: 50%;transform: translate(-50%,-50%);">');
    }else if(count>1024){
        $('.echar').html('<video src="'+env+'/echerts.mp4" height="100%" width="100%" autoplay="autoplay"></video>');
    }
}
function removeEchart() {
    $('.echar').html('');
}
//渲染数据方法
function market(cur,act){
    var env = $("input[name='env']").val();
    if (env === undefined){
        env = imgUrl
    }

    $.ajax({
        url:'/wallet/market'
        ,type:'POST'
        ,data:{currency:cur}
        ,success:function (data) {

            //清空
            $('.market0 .list2').html('');
            //$('.marketSelect ul').html('');

            for(var i=0;i<data.data.length;i++){
                var flag=data.data[i].flag;
                var dowup='up';
                if(flag==0){
                    dowup='dow';
                }else if(flag==1){
                    dowup='up';
                }

                var tmp = 'price_'+cur;
                var li=$("<li></li>");
                if(i==act){
                    li=$("<li class='activ-bit'></li>");
                }
                /*<i><img src="+env+"/"+data.data[i].curr_abb+".png /></i>*/
                var div0=$("<div><span>" + data.data[i].curr_abb +"</span></div>");
                var div1=$(" <div>"+data.data[i][tmp]+"</div>");
                var div2=$("<div class='"+dowup+"2'><span>"+data.data[i].percent_change_24h+"</span></div>");
                var div3=$("<div>"+data.data[i].volume_btc_24h+"</div>");
                var cody=li.append(div0).append(div1).append(div2).append(div3);
                $('.market0 .list2').append(cody);

                console.log('ul===='+cody);

                //买卖默认值
                activ();
                //orders页面market选项
                //$('.marketSelect ul').append("<li>" + data.data[i].curr_abb +"</li>")
            }

        }
    });
}

//专门给定时器刷新页面调用的方法
function market2(cur,act){
    var env = $("input[name='env']").val();
    if (env === undefined){
        env = imgUrl
    }

    $.ajax({
        url:' /wallet/market'
        ,type:'POST'
        ,data:{currency:cur}
        ,success:function (data) {
            $('.market0 .list2').html('');
            for(var i=0;i<data.data.length;i++){
                var flag=data.data[i].flag;
                var dowup='up';
                if(flag==0){
                    dowup='dow';
                }else if(flag==1){
                    dowup='up';
                }

                var tmp = 'price_'+cur;
                var li=$("<li></li>");
                if(i==act){
                    li=$("<li class='activ-bit'></li>");
                }
                //var div0=$("<div><i><img src="+env+"/"+data.data[i].curr_abb+".png /></i><span>" + data.data[i].curr_abb +"</span></div>");
                var div0=$("<div><span>" + data.data[i].curr_abb +"</span></div>");
                var div1=$(" <div>"+data.data[i][tmp]+"</div>");
                var div2=$("<div class='"+dowup+"2'><span>"+data.data[i].percent_change_24h+"</span></div>");
                var div3=$("<div>"+data.data[i].volume_btc_24h+"</div>");
                var cody=li.append(div0).append(div1).append(div2).append(div3);
                $('.market0 .list2').append(cody);

                ////买卖默认值
                //activ();
            }

        }
    });
}

//时间戳转为日期
function format(shijianchuo)
{
    shijianchuo=shijianchuo*1000
//shijianchuo是整数，否则要parseInt转换
    var time = new Date(shijianchuo);
    var y = time.getFullYear();
    var m = time.getMonth()+1;
    var d = time.getDate();
    var h = time.getHours();
    var mm = time.getMinutes();
    var s = time.getSeconds();
    //return y+'-'+add0(m)+'-'+add0(d)+' '+add0(h)+':'+add0(mm)+':'+add0(s);
    return add0(d)+'-'+add0(m)+'-'+y+' '+add0(h)+':'+add0(mm)+':'+add0(s);
}
function add0(m){return m<10?'0'+m:m }

function open(page,tradeCurr,currency){
    $.ajax({
        url:'/trade/myOpenOrder'
        ,type:'Get'
        ,data:{page:page,tradeCurr:tradeCurr,currency:currency}
        ,success:function (data) {
            //console.log(data);
            var result = data['data']['data'];
            var lastpage=data['data'].last_page;
            var currentpage=data['data'].current_page;
            //console.log('当前页面page为：'+page +'=='+currentpage);
            if(currentpage<=lastpage){

                if(data.data.data === undefined) return;

                for(var i=0;i<result.length;i++){
                    // var trade_type=result[i].trade_type==20 ? 'Sell' : 'Buy';
                    // var operation= result[i].operation==10 ? 'Cancel' : 'Cancel';
                    var class0= result[i].trade_type==10 ? 'buycolor' : 'sellcolor';
                    var tr=$(" <tr></tr>")
                        .append("<td>"+format(result[i].add_time)+"</td>")
                        .append("<td>"+result[i].net_volume+"</td>")
                        .append("<td>"+result[i].residual_num+"</td>")
                        .append("<td class='"+class0+"'>"+ result[i].trade +"</td>")
                        .append("<td class='"+class0+"'>"+result[i].price_btc+"</td>")
                        .append("<td class='cancel2'>"+ result[i].operation +"<input type='hidden' value="+result[i].id+" ></td>");
                    $('.addtable .sell2 table tbody').append(tr);
                }

            }
        }
        ,error:function(){
            console.log('open over');
        }
    });

}

//sellOrders 表格默认
function sellOrders(page,curr_abb,tradeType,currency){
    $.ajax({
        url:'/trade/tradeOrder'
        ,type:'GET'
        ,data:{page:page,curr_abb:curr_abb,tradeType:tradeType,currency:currency}
        ,success:function(data){
            if(page >  data.data.lastPage){
                return;
            }
            var result = data['data']['data'];
            $('.sellorbuy .sell2 .curr_abb').html(curr_abb.toUpperCase());//币种类型
            var tmp = 0;
            if (page != 1){
                var allnum=(page-1)*10-1;
                tmp=Number($('.sellorbuy .sell2 table tbody.sellOrders').children().eq(allnum).children().eq(3).html());
            }

            //totalNum

            $('#trade .sellorbuy .sell2 div .hand3 .total2 .totalNum').html('');//默认空格
            $('#trade .sellorbuy .sell2 div .hand3 .total2 .totalNum').html(data.data.totalOne);


            console.log("Line: 577 totalNum: "+data.data.totalNum);

            if(data.data.data === undefined) return;

            for(var i=0;i<result.length;i++){
                tmp +=Number( result[i].value);
                var tr=$(" <tr></tr>")
                    .append("<td>"+result[i].price_btc+"</td>")
                    .append("<td>"+result[i].residual_num+"</td>")
                    .append("<td>"+result[i].value+"</td>")
                    .append("<td>"+tmp.toFixed(8)+"</td>");
                $('.sellorbuy .sell2 table tbody.sellOrders').append(tr);
            }
        }
        ,error:function(){
            console.log('sellorder soso');
        }

    })
}
//buyOrders 表格默认
function buyOrders(page,curr_abb,tradeType,currency){
    $.ajax({
        url:'/trade/tradeOrder'
        ,type:'GET'
        ,data:{page:page,curr_abb:curr_abb,tradeType:tradeType,currency:currency}
        ,success:function(data){
            if(page >  data.data.lastPage){
                return;
            }
            var result = data['data']['data'];
            $('.sellorbuy .buy2 .curr_abb').html(curr_abb.toUpperCase());//币种类型
            var tmp = 0;
            if (page != 1){
                var allnum=(page-1)*10-1;
                tmp=Number($('.sellorbuy .buy2 table tbody.buyOrders').children().eq(allnum).children().eq(3).html());
            }

            //totalNum
            $('#trade .sellorbuy .buy2 div .hand3 .total2 .totalNum').html('');//默认空格
            $('#trade .sellorbuy .buy2 div .hand3 .total2 .totalNum').html(data.data.totalOne);

            console.log("Line: 620 totalNum: "+data.data.totalNum);
            console.log("Line: 621 totalNum: "+ data['data']['totalNum']);

            if(data.data.data === undefined) return;

            for(var i=0;i<result.length;i++){
                tmp +=Number( result[i].value);
                var tr=$(" <tr></tr>")
                    .append("<td>"+result[i].price_btc+"</td>")
                    .append("<td>"+result[i].residual_num+"</td>")
                    .append("<td>"+result[i].value+"</td>")
                    .append("<td>"+tmp.toFixed(8)+"</td>");
                $('.sellorbuy .buy2 table tbody.buyOrders').append(tr);
            }
        }
        ,error:function(){
            console.log('buyOrders soso');
        }

    })
}
//Market history 历史表格
function marketHistory( page,curr_abb,currency){
    $.ajax({
        url:'/trade/marketHistory'
        ,type:'Get'
        ,data:{page:page,curr_abb:curr_abb,currency:currency}
        ,success:function (data) {
            console.log(data);

            if(data.data.data === undefined) return;

            //console.log(data);
            $('.sellorbuy .buy2 .curr_abb').html(curr_abb.toUpperCase());//币种类型

            var result = data['data']['data'];
            var lastpage=data['data'].last_page;
            var currentpage=data['data'].current_page;
            //console.log('当前页面page为：'+page +'=='+currentpage);
            if(currentpage<=lastpage){

                for(var i=0;i<result.length;i++){
                    // var trade_type=result[i].trade_type==20 ? 'Sell' : 'Buy';
                    var class2=result[i].trade_type==20 ? 'sellcolor' : 'buycolor';
                    var tr=$(" <tr></tr>")
                        .append("<td>"+format(result[i].add_time)+"</td>")
                        // .append("<td class='"+class2+"'>"+ result[i].trade+"</td>")
                        .append("<td class='"+class2+"'>"+result[i].price_btc+"</td>")
                        .append("<td>"+result[i].initial_mun+"</td>")
                        .append("<td>"+ result[i].value+"</td>")
                        .append("<td>"+result[i].value+"</td>");//price_btc
                    $('.addtable .buy2 table tbody.marketHistory').append(tr);
                }

            }
        }
        ,error:function(){
            console.log('marketHistory soso');
        }
    });

}
//Deposit传出bit
function deposit(currAbb){
    $.ajax({
        url:'/trade/deposit',
        type:'POST',
        data:{currAbb:currAbb},
        success:function(data){
            //小Deposit
            $('#trade .conten2 .copy0 .addre').val(data.data.address);
            $('#trade .conten2 .code0 .iconcode0 .code2').attr('src',data.data.qcode);

            //wallte下的Deposit
            $('#trade .deposit2 .boxlist .currency label').html(data.data.currName+' ('+data.data.currAbb+')');
            $('.wallet-left .center2 .withdraw-box .change_bottom .add_new div.addrnew').html(data.data.address);
            $('.wallet-left .center2 .withdraw-box .change_bottom .add_new .newtop .bitt').html(data.data.currAbb);
            $('#trade .withdraw-box .boxlist .copyrow input').val(data.data.address);
            $('#trade .deposit2 .boxlist>div.code2').html(data.data.address);
            $('#trade .deposit2 .withdraw-box .code3 img').attr('src',data.data.qcode);





        },
        error:function(){
            console.log('deposit is error');
        }
    })


}

//Withdraw传出bit
function withdraw(currAbb){
    $.ajax({
        url:'/wallet/maxWithdraw',
        type:'POST',
        data:{curr:currAbb},
        success:function(data){
            console.log(data);
            $('#trade .withdraw2 .withdraw-box .boxlist .curr label').html(data.data.currName+' ('+data.data.currAbb+')');
            $('#trade .withdraw2 .boxlist .amount .maxNum').html(data.data.getBalance);
            $('#trade .withdraw-box .boxlist .fee input').val(data.data.withdraw_fee);
        },
        error:function(){
            console.log('deposit is error');
        }
    })

}

//Balance渲染数据
function balance(){
    $.ajax({
        url:'/wallet/assets',
        type:'POST',
        data:'',
        success:function(data){
            $('#trade .Balance0 .lists-bit ul').html("");
            $('.wallet2 .hand4 .handl-right .total2 span').html(data.total);

            for(var i=0;i<data.data.length;i++){
                //console.log('balance: '+data);
                var li=$("<li></li>");
                var div1=$("<div><i><img src=' " +data.data[i].curr_img+ " '></i><span>"+data.data[i].curr_abb+"</span></div>");
                var div2=$("<div>"+data.data[i].in_trade+"</div>");
                var div3=$("<div>"+data.data[i].balance+"</div>");
                var lists=li.append(div1).append(div2).append(div3);
                $('#trade .Balance0 .lists-bit ul').append(lists);

            }


        }
    })

}

//orders页面表格open orders
function openOrders(page,currAbb,currency){
    var postData;
    if (false===currAbb){
        postData = {page:page,currency:currency}
    }else {
        postData = {page:page,currAbb:currAbb,currency:currency}
    }
    $.ajax({
        url:'/order/openOrder',
        type:'GET',
        data:postData,
        success:function(data){
            var result = data.data;
            var lastpage=result.last_page;
            var currentpage=result.current_page;
            if(currentpage<=lastpage){
                if(data.data.data === undefined) return;
                for(var i=0;i<result.data.length;i++){
                    var class0=result.data[i].trade_type==10 ? 'buy' : 'sell';
                    // var type0=result.data[i].trade_type==10 ? 'Buy' : 'Sell';
                    var tr=$(" <tr></tr>")
                        .append("<td>"+format(result.data[i].add_time)+"</td>")
                        .append("<td><i><img src=' " +result.data[i].img+ " '></i><span>" +result.data[i].curr_abb+"</span></td>")
                        //.append("<td>"+result.data[i].net_volume+"</td>")
                        .append("<td>"+result.data[i].net_volume+"</td>")//volume_btc
                        .append("<td>"+result.data[i].residual_num+"</td>")//residual_volume
                        .append("<td class='type_"+class0+"'>"+result.data[i].trade+"</td>")
                        .append("<td class='price_"+class0+"'>"+result.data[i].price_btc+"</td>")
                        .append("<td class='cancel2'>"+result.data[i].operation+"<input type='hidden' value="+result.data[i].id+" ></td>");
                    $('.bigbox_in_order .order_table tbody.order_body').append(tr);
                }

            }


        },
        error:function(){
            console.log('openOrder ask error!')
        }

    })

}
//orders页面表格order history
function orderHistory(page,currAbb,currency){

    var postData;
    if (false===currAbb){
        postData = {page:page,currency:currency}
    }else {
        postData = {page:page,currAbb:currAbb,currency:currency}
    }

    $.ajax({
        url:'/order/orderHistory',
        type:'GET',
        data:postData,
        success:function(data){

            var result = data.data;
            var lastpage=result.last_page;
            var currentpage=result.current_page;


            if(data.data.data === undefined) return;

            if(currentpage<=lastpage && result.data.length!=='undefined'){

                for(var i=0;i<result.data.length;i++){

                    var class0=result.data[i].trade_type==20 ? 'sell' : 'buy';
                    // var type0=result.data[i].trade_type==20 ? 'Sell' : 'Buy';
                    //var isSuccess=result.data[i].operation==1?'Completed':'Unfinished';

                    var tr=$(" <tr></tr>")
                        .append("<td>"+format(result.data[i].last_time)+"</td>")
                        .append("<td><i><img src=' " +result.data[i].img+ " '></i><span>" +result.data[i].curr_abb+"</span></td>")
                        .append("<td>"+result.data[i].initial_volume+"</td>")
                        .append("<td>"+result.data[i].residual_num+"</td>")
                        .append("<td class='type_"+class0+"'>"+result.data[i].trade+"</td>")
                        .append("<td class='price_"+class0+"'>"+result.data[i].price_btc+"</td>")
                        .append("<td>"+result.data[i].fee_money+'/'+result.data[i].curre+"</td>")
                        .append("<td>"+result.data[i].operation+"</td>");
                    $('.history_table tbody.history_body').append(tr);
                }

            }else {
                console.log('result.data.length == undefined')
            }

        },
        error:function(){
            console.log('orderHistory ask error!')
        }

    })

}

//getFeeRate费率不同币不一样
function getFeeRate(currAbb,currency){
    $.ajax({
        url:'/order/getFeeRate',
        type:'POST',
        data:{currAbb:currAbb,currency:currency},
        success:function(data){
            var result = data.data;
            $('#trade .order0 .bigbox .orderlist .buyfee0').html(result.feeRate);
            $('#trade .order0 .bigbox .orderlist .sellfee0').html(result.feeRate);

        },
        error:function(){
            console.log('getFeeRate error')
        }

    })

}

//买卖传出
function trade(datas){
    $.ajax({
        url:'/wallet/currencyTrade'
        ,type:'POST'
        ,data:datas
        ,success:function (data) {
            //提示提交成功
            if (data.status ==1){
                //toastr.success(data.message);
                successfully(data.message);

                //amount设置为0.00000000
                $('.order0 .casket input[name="amount"]').val('0.00000000');
                var bit=bits();
                price('Last',bit);//2传入Last的price进行计算
                $('.order0 .bigbox .orderlist .bid .chang b').html(jsonData['last']);//3显示选项Last

                //刷新页面的所有数据
                //1点击需要重新加载
                var that=index();
                var curr2=bits2();//btc
                market(curr2,that);
                //2获取货币全称，左上角title
                getFullName(bit,curr2);
                //3判断买or卖
                var type=buyorsell();

                $('.sellorbuy .sell2 table tbody.sellOrders').html('');//清空
                sellOrders('1', bit,'20',curr2);

                $('.sellorbuy .buy2 table tbody.buyOrders').html('');//清空
                buyOrders('1',bit,'10',curr2);

                $('.addtable .buy2 table tbody.marketHistory').html('');//清空
                marketHistory('1',bit,curr2);

                $('.addtable .sell2 table tbody').html('');//清空
                open('1',bit,curr2);

                $('.bigbox_in_order .order_table tbody.order_body').html('')//清空
                openOrders('1',false,curr2);

                $('.history_table tbody.history_body').html('')//清空
                orderHistory('1',false,curr2);

                $('#trade .Balance0 .lists-bit ul').html('')
                balance();

                //24小时最高最低
                tranSummary(bit,curr2);
                //不同的币费率不同
                getFeeRate(bit,curr2);

            }else {
                //toastr.error(data.message)
                error(data.message);
            }
        }
        ,error:function(){
            //toastr.error('Error!')
            error('Error!');
        }
    });
}

//wallet页面Deposit history表格
function depositHistory(){
    $.ajax({
        url:'/wallet/getDepositHistory'
        ,type:'GET'
        ,success:function (data) {
            var result=data.data;

            var undefin=$.inArray("data", result);
            if(undefin == -1) return;

            for(var i=0;i<result.data.length;i++){
                var class2='suce';
                // var html2='Sucessful';
                var html3=result.data[i].confirmations;
                if (result.data[i].status ==1){
                    //已确定
                    class2='suce';
                    // html2='Sucessful';
                    //html3='Sucessful'

                }else {
                    //status=0未确定
                    class2='ongo';
                    // html2='On-going';
                    //  html3='Pending'

                }
                var tr=$("<tr></tr>");
                var td0=$("<td>"+format(result.data[i].add_time)+"</td>");
                var td1=$("<td><i><img src=' " +result.data[i].img+ " '></i><span>" +result.data[i].currency+"</span></td>");
                var td2=$("<td>"+result.data[i].address+"</td>");
                var td3=$("<td>"+result.data[i].txid+"</td>");
                var td4=$("<td>"+result.data[i].amount+"</td>");
                var td5=$("<td class='"+class2+" '>"+result.data[i].statusCp+"</td>");
                var td6=$("<td class='"+class2+" '>"+html3+"</td>");
                var cody=tr.append(td0).append(td1).append(td2).append(td3).append(td4).append(td5).append(td6);
                $('.deposit-history .tablebox tbody').append(cody);

            }

        }
        ,error:function(){
            // error('depositHistory ask Error!');
        }
    });

}

//实时刷新数据
function shauxin_all() {
    openpage=1;
    historypage=1;
    mypage=1;
    marketpage=1;

    console.log("再次启动定时器 shuaxin_all");
    //update_all = setTimeout(shuaxin_all, 20000);
    setTimeout(shauxin_all,20000);



    //console.log("tradeCurr:"+tradeCurr);
    //var page=1;

    var that=$('#trade .market0 .lists-bit ul li.activ-bit').index();

    var curr2=bits2();//btc
    market2(curr2,that);
    var tradeCurr=bits();

    getFullName(tradeCurr,curr2);

    //24小时最高最低
    tranSummary(tradeCurr,curr2);

    //不同的币费率不同
    getFeeRate(tradeCurr,curr2);
    //price('Last',tradeCurr);//2传入Last的price进行计算

    $('.sellorbuy .sell2 table tbody.sellOrders').html('');//清空
    sellOrders('1', tradeCurr.toLowerCase(),'20',curr2);


    $('.sellorbuy .buy2 table tbody.buyOrders').html('');//清空
    buyOrders('1',tradeCurr.toLowerCase(),'10',curr2);

    //$('.addtable .sell2 table tbody').html('');//清空
    //open(1,tradeCurr,curr2);

    //$('.addtable .buy2 table tbody.marketHistory').html('');//清空
    //marketHistory('1',tradeCurr.toLowerCase(),curr2);


}

//order.html页面open order下拉
function marketlist(){
    $.ajax({
        url:'/wallet/orderMarket'
        ,type:'GET'
        ,success:function (data) {
            //清空
            //$('.marketSelect ul').html('');

            for(var i=0;i<data.data.length;i++){
                //orders页面market选项
                $('.marketSelect ul').append("<li>" + data.data[i] +"</li>")
            }

        }
    });


}

//调用数据
$(function(){
    // var currency=bits();//bch/ltc/rpz加载完
    var currency='bch';
    var currency2=bits2();//BTC/...

    //1
    market(currency2,0);//trade
    marketHistory('1',currency,currency2);// trade
    openOrders('1',false,currency2); //--这个是所有的 bch传递无效  order
    orderHistory('1',false,currency2);//--这个是所有的 bch传递无效  order
    deposit(currency2);
    withdraw(currency2);
    //获取最小提现
    minMum();

    sellOrders('1',currency,'20',currency2);
    open('1',currency,currency2);
    buyOrders('1',currency,'10',currency2);

    balance();
    getFullName(currency,currency2);
    tranSummary(currency,currency2);
    getFeeRate(currency,currency2);
    depositHistory();
    marketlist();

    setTimeout(shauxin_all,10000);
    console.log("启动定时器 shuaxin_all");


});
//封装函数结束============


// 适用移动端
$(function () {
    //判断屏幕宽度(判断是PC还是移动端)
    // var count=$(document).width();
    //点击显示移动端导航栏
    $('#hearter-phone .leftbtn>a').click(function () {

        var src=$(this).children('img').attr('src');
        var env = $("input[name='env']").val();
        if (env === undefined){
            env = imgUrl
        }

        if(src=== env+"/left-btn2.png"){

            $(this).children('img').css("height","17");
            $('#hearter-phone .listphon').slideUp(200);
            console.log('收起来');

            src=$(this).children('img').attr('src').replace('left-btn2.png','left-btn.png');
        }else {

            $(this).children('img').css("height","22");
            $('#hearter-phone .listphon').slideDown(200);
            console.log('打开');
            src=$(this).children('img').attr('src').replace('left-btn.png','left-btn2.png');

        }
        $(this).children('img').attr('src',src);
    })
    //点击显示移动端导航栏结束！
    //鼠标点击改变导航条样式
    $('#hearter-phone .listphon li').click(function () {

        $(this).addClass('activer').siblings().removeClass('activer');

    });
})
// 适用移动端结束


// PC头部
$(function () {
    $('.head-row span').click(function () {
        $('.head-row span .lang').stop().slideToggle(100)
    });

    $('.head-row span ul li').click(function () {
        $('.head-row span label').html($(this).find('a').html());//点击哪个语言则显示哪语言
        $('.head-row span .lang').stop().slideUp(100);
    });

    // $('.logout').click(function ( ) {
    //     var mymessage=confirm(jsonData['Do you decide to quit logon']);
    //     if(mymessage===true){
    //         $.ajax({
    //             url:'index/logOut'
    //             ,type:'GET'
    //             ,success:function (data) {
    //                 location.reload();
    //             }
    //         });
    //     }
    //     else if(mymessage===false) {
    //        return;
    //     }
    // });


//点击按钮显示隐藏菜单栏
    $('.login-right strong.index0').click(function () {//菜单栏出现
        $(this).animate({marginLeft:'160px'},300);
        $('#menu').animate({width:'show'},300);
        $('.colu-menu .btnn').show(300);
        $('.colu-menu ul').slideDown(100);
    });
    $('.colu-menu .btnn').click(function () {//菜单栏隐藏
        $('.login-right strong').animate({marginLeft:'60px'},300);
        $('#menu').animate({width:'hide'},300);
        $('.colu-menu .btnn').hide(200);
        $('.colu-menu ul').slideUp(100);
        //子页右边空出部分去掉
        //判断屏幕宽度(判断屏幕宽)
        var num=$(document).width();
        var paddingRight=40;
        if (num<=1300){
            paddingRight=10;
        }
        $('#trade .content0').animate({paddingRight:paddingRight+'px'},200);
        $('.user .user-content').animate({paddingRight:paddingRight+'px'},200)

    })
})
//第四屏
$(function () {
    $('.positioning .middlebtn').mouseenter(function () {
        return AnimatHide();
    }).mouseleave(function () {
        return Animat(100);
    })
    // login 切换部分
    $(".main-login .login-left div").click(function(){
        var index = $(this).index();
        $(this).addClass("current").siblings().removeClass("current");
        $(".login-right>div").eq(index).show().siblings().hide();

        $('.steps .stepItem').hide();
        $(".step2").hide();
        console.log('怎么不出来！！！！');
    })
    // 忘记密码 部
    $(".main-login .forgotPass").click(function(){
        // var mymessage=confirm("找回密码？");
        $(".signIn").hide();
        $(".step2").hide();

        $('.forgetPassStep').show();
        $(".step1").show();
    });

    $(".nextTo2").click(function(){
        var email = $("input[ name='restEmail' ]").val();
        var emailReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        if (!email || !emailReg.test(email)){
            layer.msg(jsonData['Mailbox Account Format Error'],{
                offset: 'auto',
                anim: 6,
                area:['350px']
            });
            return false;
        }
        $.ajax({
            url:'restPrdOne'
            ,type:'POST'
            ,data:{email:email,step:1}
            ,success:function (data) {
                console.log(data);
                $(".Ebox").html(data.email);
                $(".leftStep").html('2');
                $(".step1").hide();
                $(".step2").show();
                return false;
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
        return false;
    });

    $(".nextTo3").click(function(){
        var code = $("input[ name='restCode' ]").val();
        var codeReg = /^\d{6}$/;
        if (!code || !codeReg.test(code)){
            layer.msg(jsonData['Code Format Error'],{
                offset: 'auto',
                anim: 6,
                area:['350px']
            });
            return false;
        }
        $.ajax({
            url:'restPrdTwo'
            ,type:'POST'
            ,data:{code:code,step:2}
            ,success:function (data) {
                if (data.status === 1){
                    $(".leftStep").html('3');
                    $(".step1").hide();
                    $(".step2").hide();
                    $(".step3").show();
                }else {
                    layer.msg(data.message, {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                }
                return false;
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
        return false;
    });

    $(".nextToLogin").click(function(){
        var prd = $("input[ name='spPass' ]").val();
        var prdAgain = $("input[ name='spPassAgain' ]").val();
        var prdReg = /([a-zA-Z0-9!@#$%^&*()_?<>{}]){7,18}/;
        if (!prd || !prdReg.test(prd) || prd !== prdAgain){
            layer.msg(jsonData['Please set the password carefully'],{
                offset: 'auto',
                anim: 6,
                area:['350px']
            });
            return false;
        }

        $.ajax({
            url:'restPrdThree'
            ,type:'POST'
            ,data:{password:prd,password_confirmation:prdAgain,step:3}
            ,success:function (data) {
                if (data.status === 1){
                    window.location.reload();
                }else {
                    return false;
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
    })
});

//trade.html
$(function () {
    //打开页面就初始化动画
    wow = new WOW({  animateClass: 'animated'});
    wow.init();

    $('#trade .handl-right ul li').click(function () {//chart栏目
        $(this).addClass('activ0').siblings().removeClass('activ0');
    });

    $('.handl-btn i').click(function () {//栏目的显示隐藏动画
        $(this).parents('.handler').siblings('.bigbox').slideToggle();
        var clss=$(this).attr('class')
        // 切换图标
        if(clss=='iconfont icon-less'){
            $(this).removeClass('icon-less').addClass('icon-moreunfold');

        }else if (clss=='iconfont icon-moreunfold'){
            $(this).removeClass('icon-moreunfold').addClass('icon-less');

        }

    });

    $('#trade .market0 .manu0 li').click(function () {//market栏目USD/BTC切换

        $(this).addClass('act2').siblings().removeClass('act2');
        var index =$('#trade .market0 .lists-bit ul li.activ-bit').index();

        var tradeCurr=$(this).html();
        $('#trade .content0 .content-right .order0 span.tradeCurr').html(tradeCurr);//买卖切换货币单位

        $('.sellorbuy .handl-right .total2 a.tradeCurr').html(tradeCurr);//total


        var currAbb= bits();//bch..
        var curr= bits2();//btc usd
        market(curr,index);

        openpage=1;
        historypage=1;
        mypage=1;
        marketpage=1;
        $('.bigbox_in_order .order_table tbody.order_body').html('');//清空
        openOrders('1',false,curr);

        $('.history_table tbody.history_body').html('');//清空
        orderHistory('1',false,curr);

        $('.sellorbuy .sell2 table tbody.sellOrders').html('');//清空
        sellOrders('1', currAbb,'20',curr);

        $('.sellorbuy .buy2 table tbody.buyOrders').html('');//清空
        buyOrders('1',currAbb,'10',curr);

        $('.addtable .buy2 table tbody.marketHistory').html('');//清空
        marketHistory('1',currAbb,curr);


        $('.addtable .sell2 table tbody').html('');//清空
        open('1',currAbb,curr);

        getFullName(currAbb,curr);//全拼
        tranSummary(currAbb,curr);//统计
        //不同的币费率不同
        getFeeRate(currAbb,curr);


    });
    //market加载出来的子元素需要委托
    $('.market0.marketAsk .lists-bit ul').on("click","li",function(){


        var that=$(this).index();

        //点击需要重新加载
        var curr= bits2();//btc
        market(curr,that);

        var market_coin=$(this).find('div').eq(0).find('span').text();
        var tradeTitle2='';
        //if(market_coin=='btc'){
        //    return;
        //}

        $(this).addClass('activ-bit').siblings().removeClass('activ-bit');

        $('.title0 .bit-2').html(tradeTitle2);//第二个数据
        $('.buybox .orderlist div').find('.casket span.currency').html(market_coin);
        $('.sellbox .orderlist div').find('.casket span.currency').html(market_coin);

        var type0=$('.order0 .select0 .acty').html();
        var currency=$('.market0 .manu0 li.act2').html();
        var num=10;
        if(type0 == 'Buy'|| type0=='买' || type0 =='買'){
            num=10;

        }else if(type0=='Sell'||type0=='卖'|| type0 =='賣'){
            num=20;
        }
//获取货币全称，左上角title
        getFullName(market_coin,curr);

        $('.sellorbuy .sell2 table tbody.sellOrders').html('');//清空
        sellOrders('1', market_coin.toLowerCase(),'20',curr);

        $('.sellorbuy .buy2 table tbody.buyOrders').html('');//清空
        buyOrders('1',market_coin.toLowerCase(),'10',curr);

        $('.addtable .buy2 table tbody.marketHistory').html('');//清空
        marketHistory('1',market_coin.toLowerCase(),curr);

        var tradeCurr=bits();
        var page=1;
        $('.addtable .sell2 table tbody').html('');//清空
        open(page,tradeCurr,curr);

        //买卖默认值==num0
        activ();
        //24小时最高最低
        tranSummary(tradeCurr,curr);

        //不同的币费率不同
        getFeeRate(tradeCurr,curr);

        //volume值填入buy or sell==num0
        // var vol2=volume();
        //amount默认0
        $('.order0 .casket input[name="amount"]').val(numb(0));//1,volume值填入amount
        price('Last',tradeCurr);//2传入Last的price进行计算

        var Last;
        if (type0=='Sell' || type0 == 'Buy' ){
            Last = 'Last';
        }else if (type0=='卖' || type0 =='买'){
            Last = '价格';
        }else {
            Last = '價格';
        }
        $('.order0 .bigbox .orderlist .bid .chang b').html(Last);//3显示选项Last


    });


    function tradeTran(type) {
        var data;
        $.ajax({
            url:'/trade/tradeTran'
            ,type:'POST'
            ,async:false
            ,data:{type:type}
            ,success:function (res) {
                data = res;
            }
        });
        return data;
    }

    //买buy传出参数
    $('#trade .orderbtn div a.buy0').click(function(){
        //显示弹框
        $('.tankuang').fadeToggle();
        //弹框渲染内容
        var bit2=bits();
        var price2=$('.buybox input[name="price"]').val();
        var amount2 = $('.buybox input[name="amount"]').val();
        var fee2=$('.buybox input[name="fee"]').val();
        var payment2=$('.buybox input[name="payment"]').val();

        var data = tradeTran(1);
        console.log(data);
        $('.tankuang .box2 .bigbox2 .orderlist2 .type2 span').html(data.lo);
        $('.tankuang .box2 .bigbox2 .orderlist2 .market2 span').html(bit2+'/BTC');
        $('.tankuang .box2 .bigbox2 .orderlist2 .price2 input').val(price2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .amount2 input').val(amount2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .amount2 .bit2').html(bit2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .fee2 input').val(fee2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .fee2 .bit2').html(bit2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .total2 input').val(payment2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .paymentOrTotal').html(data.payment);

        $('.tankuang .disc2 .typeing').html(data.upp);
        $('.tankuang .disc2 .amount3').html(amount2);
        $('.tankuang .disc2 .bit3').html(bit2);
        $('.tankuang .disc2 .total3').html(payment2);




    });
    //卖sell传出参数
    $('#trade .orderbtn div a.sell0').click(function(){
        //显示弹框
        $('.tankuang').fadeToggle();
        //弹框渲染内容
        var bit2=bits();
        var price2=$('.sellbox input[name="price"]').val();
        var amount2 =$('.sellbox input[name="amount"]').val();
        var fee2=$('.sellbox input[name="fee"]').val();
        var total2=$('.sellbox input[name="total"]').val();

        var data = tradeTran(2);
        console.log(data);

        $('.tankuang .box2 .bigbox2 .orderlist2 .type2 span').html(data.lo);
        $('.tankuang .box2 .bigbox2 .orderlist2 .market2 span').html(bit2+'/BTC');
        $('.tankuang .box2 .bigbox2 .orderlist2 .price2 input').val(price2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .amount2 input').val(amount2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .amount2 .bit2').html(bit2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .fee2 input').val(fee2);
        //$('.tankuang .box2 .bigbox2 .orderlist2 .fee2 .bit2').html(bit2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .total2 input').val(total2);
        $('.tankuang .box2 .bigbox2 .orderlist2 .paymentOrTotal').html(data.payment);

        $('.tankuang .disc2 .typeing').html(data.upp);
        $('.tankuang .disc2 .amount3').html(amount2);
        $('.tankuang .disc2 .bit3').html(bit2);
        $('.tankuang .disc2 .total3').html(total2);


    });
    //  所有子页点击头部菜单
    $('.tradebody .login-right strong.trade0').click(function () {//菜单栏出现
        $(this).animate({marginLeft:'100px'},300);
        $('#menu').animate({width:'show'},300);
        $('.colu-menu .btnn').show(300);
        $('.colu-menu ul').slideDown(100);

        //子页右边空出部分去掉
        $('.tradebody #trade .content0').animate({paddingRight:"90px"},200);
        $('.tradebody .user .user-content').animate({paddingRight:'100px'},200)
    });

    //右边买卖
    $('.order0 .select0>span').click(function () {
        $(this).addClass('acty').siblings().removeClass('acty');
        var bit=bits();
        var bit2=bits2();
        console.log(bit,bit2);


        if($(this).index()==0){
            $('.bigbox .sellbox').css('display','none');
            $('.bigbox .buybox').css('display','block');

        }else if($(this).index()==1){
            $('.bigbox .sellbox').css('display','block');
            $('.bigbox .buybox').css('display','none');

        }
    });
    //买卖弹框确定

    $('.tankuang .box2 .cont2 .btns2 a.confirm').click(function(){
        var type2=$('#trade .content-right .order0 .select0>span.acty').html();
        if(type2=='Buy'|| type2=='买' || type2 =='買'){

            //买buy提交数据
            var amount = $('.buybox input[name="amount"]').val();
            var price=$('.buybox input[name="price"]').val();
            var payment=$('.buybox input[name="payment"]').val();
            var received=$('.buybox input[name="received"]').val();
            var feeRate=$('.buybox .order_fee i').html();
            var fee=$('.buybox input[name="fee"]').val();
            var type=10;
            var currency=bits2();
            //var currency='btc';
            var tradeCurr=bits();

            var databuy = {
                amount:amount,
                price:price,
                total:payment,
                netTotal:received,
                feeRate:feeRate,
                fee:fee,
                type:type,
                currency:currency,
                tradeCurr:tradeCurr
            };
            console.log('price:'+price);
            console.log('amount:'+amount);

            if(price=='0.00000000' || amount=='0.00000000'){
                //toastr.error('The Total must be at least 0.00000001.');
                error(jsonData.totalMustBe);
            }else{
                trade(databuy);
                //隐藏弹框
                $('.tankuang').fadeToggle();

            }

        }else if(type2=='Sell'||type2=='卖'|| type2 =='賣'){

            //sell卖传出数据
            var amount = $('.sellbox input[name="amount"]').val();
            var price=$('.sellbox input[name="price"]').val();
            var total=$('.sellbox input[name="total"]').val();
            var netTotal=$('.sellbox input[name="netTotal"]').val();
            var feeRate=$('.sellbox .sell_fee i').html();
            var fee=$('.sellbox input[name="fee"]').val();
            var type=20;
            var currency=bits2();
            //var currency='btc';
            var tradeCurr=bits();
            var datasell= {
                amount:amount,
                price:price,
                total:total,
                netTotal:netTotal,
                feeRate:feeRate,
                fee:fee,
                type:type,
                currency:currency,
                tradeCurr:tradeCurr
            };
            if(price=='0.00000000' || amount=='0.00000000'){
                error(jsonData.totalMustBe);
            }else{
                trade(datasell);
                //隐藏弹框
                $('.tankuang').fadeToggle();
            }


        }else {
            console.log('判断不出买/卖');
            error('error!');

        }

    });
    //买卖弹框取消
    $('.tankuang .box2 .cancel').click(function(){
        //隐藏弹框
        $('.tankuang').fadeToggle();

    });


    $('.Balance0 .handler .handl-right>div').click(function () {//点击替换文字并bigbox显示切换
        var clickhtml=$(this).html();
        var bightml=$('.Balance0 .handl-btn span').html();
        $(this).html(bightml);
        $('.Balance0 .handl-btn span').html(clickhtml);

        if(clickhtml== 'Balance'||clickhtml== 'balance'||clickhtml== '余额'||clickhtml== '餘額'){
            $('.Balance0 .bigbox>div:first-child').css('display','block').siblings().css('display','none');
        } else if(clickhtml=='deposit'||clickhtml== 'Deposit'||clickhtml== '充值'){
            $('.Balance0 .bigbox>div:nth-child(2)').css('display','block').siblings().css('display','none');

        }
        //暂时不要Withdraw功能
        //else {
        //    $('.Balance0 .bigbox>div:nth-child(3)').css('display','block').siblings().css('display','none')
        //}
    });
    $('#trade .manu2 .btn2').click(function () { //Deposit下拉
        $(this).find('.dowlist').slideToggle(200);
    });


    $('#trade .deposit2 .manu2 .lists0 ul li').click(function () { //Deposit栏目分开
        $(this).addClass('act3').siblings().removeClass('act3');
        // 选bit单位
        var valData = $(this).html();

        deposit(valData.toLowerCase());
    });



    $('#trade .withdraw2 .manu2 .lists0 ul li').click(function () { //withdraw分开
        $(this).addClass('act3').siblings().removeClass('act3');
        // 选bit单位
        var valData = $(this).html();

        withdraw(valData.toLowerCase());
        //获取最小提现
        minMum();
        console.log(34354354)

        //amount清0
        $('#trade .withdraw-box .boxlist .amount input.withdraw_amount').val('');
        //address清空
        $('#trade .withdraw-box .boxlist .address input.withdraw_address').val('');
        //netTotal清空
        $('#trade .withdraw2 .withdraw-box .boxlist .netTotal input.withdraw_total').val('')

    });


    $('#trade .manu2 .dowlist ul li').click(function () { //Deposit下拉获取单位
        // 选bit单位
        var valData = $(this).find('span').html();
        deposit(valData.toLowerCase());

    });
    // 右边菜单部分手机端显示隐藏--按钮移动端才显示
    $('#caidan').click(function () {
        $('#trade .content0 .content-right').slideToggle(200);

    });

    //Deposit二维码点击变大缩小
    $('#trade .conten2 .code0 .iconcode0 img').click(function(){
        $(this).parents('.iconcode0').toggleClass('biger');

    });
    //Deposit复制地址
    $('#trade .conten2 .copy0 .copybtn').click(function(){
        jsCopy('copy0');
        successfully('Already replicated address');

    });
    //wallet下的Deposit复制地址
    $('#trade .wallet-left .center2 .copyrow .copybtn2').click(function(){
        jsCopy('copy2');
        successfully(jsonData['Success']);
    });

    //Cancel取消按钮验证Pin弹框
    var id=0;
    $('.bigbox tbody').on('click','td.cancel2',function (){
        id=$(this).children('input').val();
        //点击显示弹框
        //不需要输入pin,直接删除
        //$('.modelPin').fadeToggle();
        pin(id);




    });
    //取消按钮close
    $('.modelPin .modal2 .close0').click(function(){
        $('.modelPin').fadeToggle();
        $('.modelPin .modal2 .bodyPin div input').val('')

    });
    //确定按钮Ok
    $('.modelPin .modal2 .ok0').click(function(){
        var pin0=$('.modelPin .modal2 .bodyPin input').val();
        //pin(id,pin0);

    });
    //点击Forget Pin按钮
    $('.modelPin .modal2 .bodyPin .forget a').click(function(){
        sendEmail();
        //清空输入框pin
        $('.modelPin .modal2 .bodyPin div input').val('');
        $('.modelPin').fadeToggle();

        $('.forgetPin2').fadeToggle();

    })
    //关闭Forget Pin弹框
    $('.forgetPin2 .dialog-header2 i.closebtn2').click(function(){
        $('.forgetPin2').fadeOut();

    });


})
//wallet.html
$(function () {
    $('.history0 .hand4 .handl-btn a>span').click(function () {
        var index=$(this).index();
        $(this).addClass('active-name').siblings().removeClass('active-name');
        if(index==0){
            $('.history0 .bigbox>div.deposit-history').css('display','block').siblings().css('display','none')
        }else if(index==1){
            $('.history0 .bigbox>div.withdrawal-history').css('display','block').siblings().css('display','none')
        }

    });

//点击二维码把图片放大
    $('.wallet-left .center2 .withdraw-box .code3 img').click(function(){
        $(this).parents('.code3').toggleClass('big');

    });

    //点击max按钮
    $('#trade .wallet-left .right2 .boxlist .amount a').click(function(){
        var maxnum=$(this).find('.maxNum').html();

        if(maxnum<0){
            $('#trade .withdraw-box .boxlist .amount input.withdraw_amount').val(numb(0.00000000));
        }else {
            $('#trade .withdraw-box .boxlist .amount input.withdraw_amount').val(numb(maxnum));
        }


        var amount=numb(maxnum);
        var fees=$('#trade .withdraw2 .withdraw-box .boxlist .fee input.withdraw_fee').val();

        var netTotal=Number(amount)-Number(fees);

        if(netTotal < 0){

            netTotal=0.00000000;

        }
        //var indexof=netTotal.toString().indexOf('.');
        //if(indexof > -1 && netTotal.substr(indexof).length > 8) {
        //
        //    netTotal = netTotal.toFixed(8);
        //
        //}

        $('#trade .withdraw2 .withdraw-box .boxlist .netTotal input.withdraw_total').val(netTotal);




    })

    //添加NetTotal更新
    $('#trade .withdraw2 .withdraw-box .boxlist .amount input').keyup(function(){
        var amount=$(this).val();
        var fees=$('#trade .withdraw2 .withdraw-box .boxlist .fee input.withdraw_fee').val();

        var netTotal=Number(amount)-Number(fees);


        //var indexof=netTotal.toString().indexOf('.');

       if(netTotal < 0){

            netTotal=0.00000000;

        }
       //if(indexof > -1 && netTotal.substr(indexof).length > 8) {
       //
       //    netTotal = netTotal.toFixed(8);
       //
       //}

        $('#trade .withdraw2 .withdraw-box .boxlist .netTotal input.withdraw_total').val(netTotal);

    })


    //Amount只允许输入小数点后6位
    $('.withdraw2 .withdraw_amount').keypress(function(){

        var valnum=$(this).val();

        var indexof=valnum.lastIndexOf(".");
        var length=valnum.length;

        if(indexof !==-1){
            var number=length-indexof;


            if(number>6){
                $(this).blur();
                $('.withdraw2 .withdraw_amount').val(valnum);

            }else {
                return;
            }

        }else {
            return;
        }





    });





});

//Order.html
$(function (){
    //1选择Open Orders下拉
    $('.order_head th.openbtn').click(function(){
        $(this).find('.marketSelect').slideToggle();
    });
    //1market选择==Open Orders分开
    $('.order_head th.openbtn .marketSelect ul').on('click','li',function(){
        var markettype=$(this).html();
        $('.order_head th.openbtn>span').html(markettype.toUpperCase());
        var currAbb=markettype.toLowerCase();
        var currency=bits2();
        $('.bigbox_in_order .order_table tbody.order_body').html('')//清空
        openOrders('1',currAbb,currency)
    });



    //2选择OrderHistory下拉
    $('.history_head th.historybtn').click(function(){
        $(this).find('.marketSelect').slideToggle();
    });
    //2market选择==OrderHistory分开
    $('.history_head th.historybtn .marketSelect ul').on('click','li',function(){
        var markettype=$(this).html();
        $('.history_head th.historybtn>span').html(markettype.toUpperCase());

        var currAbb=markettype.toLowerCase();
        var currency=bits2();

        $('.history_table tbody.history_body').html('');//清空
        orderHistory('1',currAbb,currency);
    });

});

//Buy键盘事件
$(function(){
//1,如果修改amount
    $('.order0 .buybox .orderlist input[name="amount"]').keyup(function(){
        //amount能得pay
        var amount=$(this).val()==''? 0.00000000 : $(this).val();
        var indexof=amount.toString().indexOf('.');

        if(indexof > -1 && amount.substr(indexof).length > 8) {
            amount = Number(amount).toFixed(8);
            $(this).val(amount);
        }

        var price=$('.buybox .orderlist').find('input[name="price"]').val();
        var amount0=new Number(amount);
        var payment0=amount0.mul(price);//存在科学计算法

        //如果是科学技术法转为数字
        if ((payment0.toString().indexOf('E') != -1) || (payment0.toString().indexOf('e') != -1)) {
            payment0=payment0.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var payment=numb(payment0);

        $('.buybox .orderlist').find('input[name="payment"]').val(payment);

        //Amount能得fee,
        var rep=$('.buybox .orderlist').find('div label i').html();
        var feeRate=Number(rep.replace(/\%/g,''));

        var feeA=amount0.mul(feeRate);//存在科学计算法

        //如果是科学技术法转为数字
        if ((feeA.toString().indexOf('E') != -1) || (feeA.toString().indexOf('e') != -1)) {
            feeA=feeA.toFixed(12);
        }
        //如果是科学技术法转为数字结束


        var feeB=new Number(feeA);
        var feeC=feeB.mul('0.01');

        //如果是科学技术法转为数字
        if ((feeC.toString().indexOf('E') != -1) || (feeC.toString().indexOf('e') != -1)) {
            feeC=feeC.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var fee=numb(feeC);

        $('.buybox .orderlist').find('input[name="fee"]').val(fee);


//获取 Received=(1-feeRate)*amount
        var num= feeRate.mul(0.01);
        var num2=new Number(num);
        var num3=Number(1);
        var numall=num3.sub(num2);

        var paymentend=amount0.mul(numall);

        //如果是科学技术法转为数字
        if ((paymentend.toString().indexOf('E') != -1) || (paymentend.toString().indexOf('e') != -1)) {
            paymentend=paymentend.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var received=numb(paymentend);

        $('.buybox .orderlist').find('input[name="received"]').val(numb(received));


    });

    //2修改Price
    $('.order0 .buybox .orderlist input[name="price"]').keyup(function(){

        //price能得total(payment)
        var price=$(this).val()==''? 0.00000000 :$(this).val();
        var indexof=price.toString().indexOf('.');
        if(indexof > -1 && price.substr(indexof).length > 8) {
            price = Number(price).toFixed(8);
            $(this).val(price);
        }
        var amount=$('.buybox .orderlist').find('input[name="amount"]').val();

        var amount0=new Number(amount);
        var payment0=amount0.mul(price);
        //如果是科学技术法转为数字
        if ((payment0.toString().indexOf('E') != -1) || (payment0.toString().indexOf('e') != -1)) {
            payment0=payment0.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var payment=numb(payment0);

        $('.buybox .orderlist').find('input[name="payment"]').val(payment);
        //Amount能得fee,
        var rep=$('.buybox .orderlist').find('div label i').html();
        var feeRate=Number(rep.replace(/\%/g,''));

        var feeA=amount0.mul(feeRate);
        var feeB=new Number(feeA);
        var feeC=feeB.mul('0.01');

        //如果是科学技术法转为数字
        if ((feeC.toString().indexOf('E') != -1) || (feeC.toString().indexOf('e') != -1)) {
            feeC=feeC.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var fee=numb(feeC);
        $('.buybox .orderlist').find('input[name="fee"]').val(fee);

        //获取 Received=(1-feeRate)*payment
        var num= feeRate.mul(0.01);
        var num2=new Number(num);
        var num3=Number(1);
        var numall=num3.sub(num2);


        var paymentend=amount0.mul(numall);

        //如果是科学技术法转为数字
        if ((paymentend.toString().indexOf('E') != -1) || (paymentend.toString().indexOf('e') != -1)) {
            paymentend=paymentend.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var received=numb(paymentend);

        $('.buybox .orderlist').find('input[name="received"]').val(numb(received));


    });


});

//Sell键盘事件
$(function(){
//1,如果修改amount
    $('.order0 .sellbox .orderlist input[name="amount"]').keyup(function(){
        //amount能得total，
        var amount=$(this).val()==''? 0.00000000 : $(this).val();
        var indexof=amount.toString().indexOf('.');

        if(indexof > -1 && amount.substr(indexof).length > 8) {
            amount = Number(amount).toFixed(8);
            $(this).val(amount);
        }

        var price=$('.sellbox .orderlist').find('input[name="price"]').val();
        var amount0=new Number(amount);
        var total0=amount0.mul(price);
        //如果是科学技术法转为数字
        if ((total0.toString().indexOf('E') != -1) || (total0.toString().indexOf('e') != -1)) {
            total0=total0.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var total=numb(total0);

        $('.sellbox .orderlist').find('input[name="total"]').val(total);
        //total能得fee,
        var rep=$('.sellbox .orderlist').find('div label i').html();
        var feeRate=Number(rep.replace(/\%/g,''));
        var feeRate0=new Number(feeRate);
        var fee0=feeRate0.mul(total);
        var fee2=new Number(fee0);
        var fee3=fee2.mul('0.01');

        //如果是科学技术法转为数字
        if ((fee3.toString().indexOf('E') != -1) || (fee3.toString().indexOf('e') != -1)) {
            fee3=fee3.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var fee=numb(fee3);
        $('.sellbox .orderlist').find('input[name="fee"]').val(fee);

//获取 netTotal
        var num= feeRate0.mul(0.01);
        var num2=new Number(num);
        var num3=Number(1);
        var numall=num3.sub(num2);

        //var netTotal=new Number(total);
        var netTotal=new Number(total0); //--修改

        var netTotal2=netTotal.mul(numall);

        //如果是科学技术法转为数字
        if ((netTotal2.toString().indexOf('E') != -1) || (netTotal2.toString().indexOf('e') != -1)) {
            netTotal2=netTotal2.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var netTotal3=numb(netTotal2);

        $('.sellbox .orderlist').find('input[name="netTotal"]').val(netTotal3);

    });

    //2修改Price
    $('.order0 .sellbox .orderlist input[name="price"]').keyup(function(){
        //price能得total
        var price=$(this).val()==''? 0.00000000 :$(this).val();

        var indexof=price.toString().indexOf('.');
        if(indexof > -1 && price.substr(indexof).length > 8) {
            price = Number(price).toFixed(8);
            $(this).val(price);
        }

        var amount=$('.sellbox .orderlist').find('input[name="amount"]').val();

        var amount0=new Number(amount);
        var total0=amount0.mul(price);

        //如果是科学技术法转为数字
        if ((total0.toString().indexOf('E') != -1) || (total0.toString().indexOf('e') != -1)) {
            total0=total0.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var total=numb(total0);

        $('.sellbox .orderlist').find('input[name="total"]').val(total);

        //total能得fee
        var rep=$('.sellbox .orderlist').find('div label i').html();
        var feeRate=Number(rep.replace(/\%/g,''));

        var feeRate0=new Number(feeRate);
        var fee0=feeRate0.mul(total);
        var fee2=new Number(fee0);
        var fee3=fee2.mul('0.01');

        //如果是科学技术法转为数字
        if ((fee3.toString().indexOf('E') != -1) || (fee3.toString().indexOf('e') != -1)) {
            fee3=fee3.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var fee=numb(fee3);

        $('.sellbox .orderlist').find('input[name="fee"]').val(fee);

        //fee能得netTotal
        // netTotal2=Number(total) - Number(fee);
        var num= feeRate0.mul(0.01);
        var num2=new Number(num);
        var num3=Number(1);
        var numall=num3.sub(num2);

        //var netTotal=new Number(total);
        var netTotal=new Number(total0);//--修改


        var netTotal2=netTotal.mul(numall);
        //如果是科学技术法转为数字
        if ((netTotal2.toString().indexOf('E') != -1) || (netTotal2.toString().indexOf('e') != -1)) {
            netTotal2=netTotal2.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var netTotal3=numb(netTotal2);

        $('.sellbox .orderlist').find('input[name="netTotal"]').val(netTotal3);



    });

});


//点击事件,数据关联
$(function(){
    //trade页面Buy sellorders列表点击
    $('.sellorbuy .bigbox table tbody.buyOrders').on("click","tr",function(){
        //1切换到Sell
        buyAndSell(1);
        //2添加背景色
        $(this).addClass('actbuy').siblings().removeClass('actbuy');

        //3把数据相对应放到sell窗口上
        var amount=$(this).children('td').eq(1).html();
        var price=$(this).children('td').eq(0).html();

        var amount0=new Number(amount);
        var total0=amount0.mul(price);
        //如果是科学技术法转为数字
        if (( total0.toString().indexOf('E') != -1) || ( total0.toString().indexOf('e') != -1)) {
            total0= total0.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var total=numb(total0);

        var rep=$('#trade .order0 .orderlist .sell_fee .sellfee0').html();
        var feeRate=Number(rep.replace(/\%/g,''));
        var feeRate0=new Number(feeRate);
        var fee0=feeRate0.mul(total);
        //如果是科学技术法转为数字
        if ((fee0.toString().indexOf('E') != -1) || ( fee0.toString().indexOf('e') != -1)) {
            fee0=fee0.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var fee2=new Number(fee0);
        var fee3=fee2.mul('0.01');
        //如果是科学技术法转为数字
        if ((fee3.toString().indexOf('E') != -1) || ( fee3.toString().indexOf('e') != -1)) {
            fee3=fee3.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var fee=numb(fee3);

        var num= feeRate0.mul(0.01);
        //如果是科学技术法转为数字
        if ((num.toString().indexOf('E') != -1) || ( num.toString().indexOf('e') != -1)) {
            num=num.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var num2=new Number(num);
        var num3=Number(1);
        var numall=num3.sub(num2);

        //var netTotal=new Number(total);
        var netTotal=new Number(total0); //--修改

        var netTotal2=netTotal.mul(numall);
        //如果是科学技术法转为数字
        if ((netTotal2.toString().indexOf('E') != -1) || ( netTotal2.toString().indexOf('e') != -1)) {
            netTotal2=netTotal2.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var netTotal3=numb(netTotal2);
        console.log("total0:"+total0);
        console.log("netTotal:"+netTotal);
        console.log("netTotal2:"+netTotal2);
        console.log("netTotal3:"+netTotal3);

        //对应输出
        $('.sellbox .casket input[name="amount"]').val(amount);
        $('.sellbox .casket input[name="price"]').val(price);
        $('.sellbox .casket input[name="total"]').val(total);
        $('.sellbox .casket input[name="fee"]').val(fee);
        $('.sellbox .casket input[name="netTotal"]').val(netTotal3);

    });

    //trade页面Sell buyorders列表点击
    $('.sellorbuy .bigbox table tbody.sellOrders').on("click","tr",function(){
        //1切换到buy
        buyAndSell(0);
        //2添加背景色
        $(this).addClass('actsell').siblings().removeClass('actsell');
        //3把数据相对应放到buy窗口上
        var amount=$(this).children('td').eq(1).html();
        var price=$(this).children('td').eq(0).html();

        var amount0=new Number(amount);
        var payment0=amount0.mul(price);
        //如果是科学技术法转为数字
        if ((payment0.toString().indexOf('E') != -1) || ( payment0.toString().indexOf('e') != -1)) {
            payment0=payment0.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var payment=numb(payment0);

        var rep=$('#trade .order0 .bigbox .orderlist .order_fee .buyfee0').html();
        var feeRate=Number(rep.replace(/\%/g,''));

        var feeA=amount0.mul(feeRate);
        //如果是科学技术法转为数字
        if ((feeA.toString().indexOf('E') != -1) || ( feeA.toString().indexOf('e') != -1)) {
            feeA=feeA.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var feeB=new Number(feeA);
        var feeC=feeB.mul('0.01');
        //如果是科学技术法转为数字
        if ((feeC.toString().indexOf('E') != -1) || (feeC.toString().indexOf('e') != -1)) {
            feeC=feeC.toFixed(12);
        }
        //如果是科学技术法转为数字结束
        var fee=numb(feeC);

        //获取 Received=(1-feeRate)*payment
        var num= feeRate.mul(0.01);
        var num2=new Number(num);
        var num3=Number(1);
        var numall=num3.sub(num2);
        var paymentend=amount0.mul(numall);
        //如果是科学技术法转为数字
        if ((paymentend.toString().indexOf('E') != -1) || (paymentend.toString().indexOf('e') != -1)) {
            paymentend=paymentend.toFixed(12);
        }
        //如果是科学技术法转为数字结束

        var received=numb(paymentend);

        //对应输出
        $('.buybox .casket input[name="amount"]').val(amount);
        $('.buybox .casket input[name="price"]').val(price);
        $('.buybox .casket input[name="payment"]').val(payment);
        $('.buybox .casket input[name="fee"]').val(fee);
        $('.buybox .casket input[name="received"]').val(received);

    });

    //buy or sell点击price下拉选项
    $('.order0 .bigbox .orderlist .bid .chang').click(function(){
        $('.order0 .bigbox .orderlist .bid .lists2').slideToggle(100);
    });
    //price下拉选项选择获取相应选项
    $('.order0 .bigbox .orderlist .bid .lists2 ul li').click(function(){
        var type=$(this).html();
        var currAbb=bits();
        //$('.order0 .bigbox .orderlist .bid .chang b').html(type);
        //buy sell 分开
        $(this).parents('.bid').find('.chang b').html(type);
        $('.order0 .bigbox .orderlist .bid .lists2').slideToggle(100);

        price(type,currAbb);

    });

    //user页面reset pin键盘事件
    $(function(){
        //1,newpin
        $('.dialog .dialog-container .sheet .rank .rank-row input[name="pinNew"]')
            .focus(function(){
                $(this).parents('.rank').find('.tips').css('visibility','hidden');
                $(this).parents('.rank').find('.ft-green').css('visibility','hidden');

            }).blur(function(){
            console.log('blur');
            var newPin=$(this).val();
            if(newPin==''){
                $(this).parents('.rank').find('.tips').css('visibility','visible');
                $(this).parents('.rank').find('.tips span').html(jsonData['New Pin cannot be empty']);

            }else if (newPin.length<6){
                $(this).parents('.rank').find('.tips').css('visibility','visible');
                $(this).parents('.rank').find('.tips span').html(jsonData['New Pin length must be greater than 6']);
            }else{
                // $(this).parents('.rank').find('.ft-green').css('visibility','visible');

            }
        });
        //2,again
        $('.dialog .dialog-container .sheet .rank .rank-row input[name="pinNew_confirmation"]')
            .focus(function(){
                $(this).parents('.rank').find('.tips').css('visibility','hidden');

            }).blur(function(){
            console.log('blur');
            var newPin=$('.dialog .dialog-container .sheet .rank .rank-row input[name="pinNew"]').val();
            var pinAgain=$(this).val();
            if(pinAgain ==''){
                $(this).parents('.rank').find('.tips').css('visibility','visible');
                $(this).parents('.rank').find('.tips span').html(jsonData['New Pin cannot be empty']);
            }else if (pinAgain.length<6){
                $(this).parents('.rank').find('.tips').css('visibility','visible');
                $(this).parents('.rank').find('.tips span').html(jsonData['New Pin length must be greater than 6']);
            }
            //判断二者一致
            // else if(pinAgain !== newPin) {
            //     $(this).parents('.rank').find('.tips').css('visibility','visible');
            //     $(this).parents('.rank').find('.tips span').html(jsonData['New Pin again and New Pin are different']);
            // }else {
            //     // $(this).parents('.rank').find('.ft-green').css('visibility','visible');
            // }

        });
        //3,cody
        $('.dialog .dialog-container .sheet .rank .rank-row input[name="pinCode"]')
            .focus(function(){
                $(this).parents('.rank').find('.tips').css('visibility','hidden');
            }).blur(function(){
            var cody=$(this).val();
            if(cody==''){
                $(this).parents('.rank').find('.tips').css('visibility','visible');
                $(this).parents('.rank').find('.tips span').html(jsonData['Email verification code cannot be empty']);
            }else if(cody.length<6){
                $(this).parents('.rank').find('.tips').css('visibility','visible');
                $(this).parents('.rank').find('.tips span').html(jsonData['Email verification code length must be greater']);
            }else{
                return;
            }
        });
        $('.dialog .dialog-container .dialog-part .dialog-content .sheet .rank button.resetPin').click(function(){
            var PinNew = $("input[ name='pinNew']").val();
            var Pinagain = $("input[ name='pinNew_confirmation' ]").val();
            var pincode = $("input[ name='pinCode' ]").val();
            newPin(PinNew,Pinagain,pincode);
        })
    })

    //cancel按钮的reset pin键盘事件
    $(function(){
        //1,newpin
        $('.forgetPin2 .sheet2 .rank .rank-row input[name="newpin"]')
            .focus(function(){
                $(this).parents('.rank').find('.tips').hide();
                $(this).parents('.rank').find('.ft-green2').css('visibility','hidden');

            }).blur(function(){
            var newPin=$(this).val();
            if(newPin==''){
                $(this).parents('.rank').find('.tips').show();
                $(this).parents('.rank').find('.tips span').html(jsonData['New Pin cannot be empty']);
            }else if (newPin.length<6){
                $(this).parents('.rank').find('.tips').show();
                $(this).parents('.rank').find('.tips span').html(jsonData['New Pin length must be greater than 6']);
            }else{
                // $(this).parents('.rank').find('.ft-green2').css('visibility','visible');
                //console.log('okok')
                //console.log($(this).parents('.rank').find('.ft-green2'));

            }
        });
        //2,again
        $('.forgetPin2 .sheet2 .rank .rank-row input[name="pinagain"]')
            .focus(function(){
                $(this).parents('.rank').find('.tips').hide();
                // $(this).parents('.rank').find('.ft-green2').css('visibility','hidden');

            }).blur(function(){
            var newPin= $('.forgetPin2 .sheet2 .rank .rank-row input[name="newpin"]').val();
            var pinAgain=$(this).val();
            if(newPin==''){
                $(this).parents('.rank').find('.tips').show();
                $(this).parents('.rank').find('.tips span').html(jsonData['New Pin cannot be empty']);
            }else if (pinAgain.length < 6){
                $(this).parents('.rank').find('.tips').css('visibility','visible');
                $(this).parents('.rank').find('.tips span').html(jsonData['New Pin length must be greater than 6']);
            }
            //判断二者一致
            // else if(pinAgain !== newPin) {
            //     $(this).parents('.rank').find('.tips').show();
            //     $(this).parents('.rank').find('.tips span').html(jsonData['New Pin again and New Pin are different']);
            // }else {
            //     // $(this).parents('.rank').find('.ft-green2').css('visibility','visible');
            // }
        });
        //3,cody
        $('.forgetPin2 .sheet2 .rank .rank-row input[name="emailCode"]')
            .focus(function(){
                $(this).parents('.rank').find('.tips').hide();

            }).blur(function(){
            var cody=$(this).val();
            if(cody==''){
                $(this).parents('.rank').find('.tips').show();
                $(this).parents('.rank').find('.tips span').html(jsonData['Email verification code cannot be empty']);
            }else if(cody.length<6){
                $(this).parents('.rank').find('.tips').show();
                $(this).parents('.rank').find('.tips span').html(jsonData['Email verification code length must be greater']);

            }else{
                return;
            }
        });

//点击提交pin
        $('.forgetPin2 .sheet2 .rank button').click(function(){
            var PinNew = $('.forgetPin2 .sheet2 .rank .rank-row input[name="newpin"]').val();
            var Pinagain = $('.forgetPin2 .sheet2 .rank .rank-row input[name="pinagain"]').val();
            var pincode = $('.forgetPin2 .sheet2 .rank .rank-row input[name="emailCode"]').val();

            //console.log('PinNew=>'+PinNew+'====='+'Pinagain=>'+Pinagain+'====='+'pincode=>'+pincode);
            newPin(PinNew,Pinagain,pincode);

        })
    })
});

function minMum() {
    var curr = $('#trade .withdraw2 .manu2 .lists0 ul li.act3').html();
    $.ajax({
        url:'/wallet/minLimit',
        type:'get',
        data:{curr:curr},
        success:function (data) {
            $('#trade .withdraw-box .boxlist .amount input').attr('placeholder',data.data.minLimit)
        }
    })
}
//表格数据下拉加载更多
$(function(){
    //sellOrders
    //var sellpage=1;
    //
    //$('.sellorbuy .sell2 table tbody.sellOrders').scroll(function() {
    //    var tradeCurr=bits();
    //    var curr=bits2();//usd/btc/
    //    var $dom = $(this).find('tr');
    //    var $hh = $dom.height();
    //    var $len = $dom.length;
    //
    //    var viewHeight =$(this).height();
    //    var contentHeight =$hh*$len;
    //    var scrollHeight =$(this).scrollTop();
    //    if(contentHeight - viewHeight <= scrollHeight) {//滚到底部加载数据
    //        //console.log("到达底部了");
    //        sellpage=sellpage+1;
    //        sellOrders(sellpage,tradeCurr,'20',curr);
    //    }
    //});





    //BuyOrder
    // buyOrders('1','bch','10');
    //var buypage=1;
    //$(".sellorbuy .buy2 table tbody.buyOrders").scroll(function() {
    //    var tradeCurr=bits();
    //    var curr=bits2();//usd/btc/
    //
    //    var $dom = $(this).find('tr');
    //    var $hh = $dom.height();
    //    var $len = $dom.length;
    //
    //    var viewHeight =$(this).height();
    //    var contentHeight =$hh*$len;
    //    var scrollHeight =$(this).scrollTop();
    //    if(contentHeight - viewHeight <= scrollHeight) {//滚到底部加载数据
    //        //console.log("到达底部了");
    //        buypage=buypage+1;
    //        buyOrders(buypage,tradeCurr,'10',curr);
    //    }
    //});





    //MyOpenOrder

    // var tradeCurr=bits();
    // open(page,'bch');

    $(".addtable .sell2 tbody").scroll(function() {
        var tradeCurr=bits();
        var curr=bits2();//usd/btc/
        console.log('scroll myopenorder')
        console.log('mypage:'+mypage)
        var $dom = $(this).find('tr');
        var $hh = $dom.height();
        var $len = $dom.length;

        var viewHeight =$(this).height();
        var contentHeight =$hh*$len;
        var scrollHeight =$(this).scrollTop();
        if(contentHeight - viewHeight <= scrollHeight) {//滚到底部加载数据
            //console.log("到达底部了");
            mypage=mypage+1;
            open(mypage,tradeCurr,curr);
        }
    });




    //Market history

    //$(".addtable .buy2 tbody").scroll(function() {
    //    var tradeCurr=bits();
    //    var curr=bits2();//usd/btc/
    //    console.log('scroll Market history');
    //    console.log('marketpage:'+marketpage);
    //    var $dom = $(this).find('tr');
    //    var $hh = $dom.height();
    //    var $len = $dom.length;
    //
    //    var viewHeight =$(this).height();
    //    var contentHeight =$hh*$len;
    //    var scrollHeight =$(this).scrollTop();
    //    if(contentHeight - viewHeight <= scrollHeight) {//滚到底部加载数据
    //        //console.log("到达底部了");
    //        marketpage=marketpage+1;
    //        marketHistory(marketpage,tradeCurr,curr);
    //    }
    //});


    //Open Order

    $(".order_table .order_body").scroll(function() {
        var tradeCurr= $('.order_head th.openbtn>span').html();
        var curr=bits2();//usd/btc/
        var $dom = $(this).find('tr');
        var $hh = $dom.height();
        var $len = $dom.length;
        var viewHeight =$(this).height();
        var contentHeight =$hh*$len;
        var scrollHeight =$(this).scrollTop();
        if(contentHeight - viewHeight <= scrollHeight) {//滚到底部加载数据
            //console.log("到达底部了");
            openpage=openpage+1;
            openOrders(openpage,tradeCurr,curr);
        }
    });

    //Order History

    $(".history_table .history_body").scroll(function() {
        var tradeCurr= $('.history_head th.historybtn>span').html();
        var curr=bits2();//usd/btc/
        var $dom = $(this).find('tr');
        var $hh = $dom.height();
        var $len = $dom.length;
        var viewHeight =$(this).height();
        var contentHeight =$hh*$len;
        var scrollHeight =$(this).scrollTop();
        if(contentHeight - viewHeight <= scrollHeight) {//滚到底部加载数据
            //console.log("到达底部了");
            historypage=historypage+1;
            orderHistory(historypage,tradeCurr,curr);
        }
    });
});











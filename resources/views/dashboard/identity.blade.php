@extends('dashboard.layout.app')
<style>
    .reason {
        background: #29314f;
        padding: 3px;
        margin:2px;
    }
    .reason p {
        margin: auto;
        height: 25px;
    }
    .reason_input{
        float: right;
        width: 20px;
        height: 20px;
        margin: 2px auto;
    }
</style>
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl">Identity Verification</div>
        <div class="wrapper-content">
            <div class="search-wrapper">
                <div class="form-box">
                    <form action="" onsubmit="return false;">
                        <input type="text" id="search" placeholder="(id) name or nationality">
                        <button type="button" id="sear"><i class="iconfont icon-icon--"></i>Search</button>
                        <a onclick="location.reload()"><i class="iconfont icon-xunhuan101"></i>Refresh</a>
                    </form>
                </div>
            </div>
            <div class="list-client">
                <table class="identity-list" id="change">
                    <thead>
                        <tr>
                            <th>Submitted</th>
                            <th>Client ID/Full Name/DOB</th>
                            <th>Same</th>
                            <th>Country City ZipCode</th>
                            <th>Address</th>
                            <th>Passport Number</th>
                            <th>Picture</th>
                            <th>Status</th>
                            <th>Bind</th>
                            <th>Edit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
    <div id="pagination" class="pagination"></div>
</section>

<!-- 弹出层  -->
<div class="modal fade" id="imgModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">  
    <div class="modal-dialog">  
        <div class="modal-content">  
            
            <div class="modal-body">  
                <img src="" alt="">
            </div>  
        </div> 
    </div> 
</div>
<div class="loading" style="display: none;">
    <img class="img-gif" src="{{asset('dashboard/dist/img/Loading.gif')}}">
</div>
{{--拒绝理由选择窗--}}
<div class="modal fade" id="confirm_modal" role="dialog" style="padding-right: 17px;">
    <div class="modal-dialog" role="document" style="margin-top: 340px; max-width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Reason</h4>
            </div>
            <div class="modal-body reasons_box">
                <div class="authy_code"></div>
            </div>
            <div class="modal-footer">
                <button id="send_authy" type="button" class="btn btn-nov" onclick="reason_ok()">OK</button>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
<script>
    $.ajax({//拒绝理由数据
        url: "{{route('reject_reasons')}}",
        data: {},
        dataType: 'json',
        type: 'get',
        success: function (msg) {
            if (msg.code == 200) {
                var str = '';
                for (var i in msg[0]) {
                    str += `<div class="reason" trid="${msg[0][i]['id']}">
                                <p> ${msg[0][i]['reason']}
                                    <input class="reason_input" type="checkbox" name="vehicle"/>
                                </p>
                            </div>`;
                    $('.reasons_box').empty().append(str);
                }
            } else {
                layer.msg('Data Error', {
                    offset: 'auto',
                    anim: 6,
                    area:['420px']
                });
            }
        },
        error: function () {
            layer.msg('Error', {
                offset: 'auto',
                anim: 6,
                area:['420px']
            });
        }
    });
    var reasons = [];
    var that = null;

    $('#change').on('click','.choose',function(){
        var $select = $(this).parent().find('.img-list');
        if($select.hasClass('on')){
            $select.slideUp();
            $select.removeClass('on')
        }else{
            $select.addClass('on')
            $select.slideDown()
        }
    });
    $('#change').on('click','.choose-photo .img-list li',function(){
        $('.choose-photo .img-list').slideUp();
        $('.choose-photo .img-list').removeClass('on');
        var imgUrl = $(this).find('img').attr('src');
        $('#imgModal').find('img').attr('src',imgUrl);
        $('#imgModal').modal('show');
    });
    $(function() {
        $('#sear').on('click',function(){
            opt.onPageClicked('','',1,1);
        });
        var opt = {
            currentPage: 1,
            totalPages: 1,
            numberOfPages:5,
            onPageClicked:function (event, originalEvent, type, newPage) {
                $('.loading').show();
                var something = $('#search').val();
                $.ajax({
                    url:"{{route('check_list')}}",
                    data:{'page':newPage,'something':something},
                    dataType:'json',
                    type:'get',
                    success:function (msg) {
                        if(msg.code == 200) {
                            var str = '';
                            opt.totalPages = Math.ceil(msg[1]/10);
                            for(var i in msg[0]['data']) {
                                str += `
                                <tr trid="${msg[0]['data'][i]['id']}" userid="${msg[0]['data'][i]['id']}">
                                    <td>${msg[0]['data'][i]['certification_time']}</td>
                                    <td>( ${msg[0]['data'][i]['id']} )<br>${msg[0]['data'][i]['first_name']} ${msg[0]['data'][i]['last_name']}<br>DOB: ${msg[0]['data'][i]['birthday']} </td>
                                    <td>${msg[0]['data'][i]['same']}</td>
                                    <td>${msg[0]['data'][i]['en_country']} ${msg[0]['data'][i]['region']}</td>
                                    <td>${msg[0]['data'][i]['residential_address']}</td>
                                    <td>${msg[0]['data'][i]['id_number']}</td>
                                    <td class="text-center">
                                        <div class="choose-photo">
                                            <span class="choose">Choose Photo<i class="iconfont icon-ln_jiantouxia"></i></span>
                                            <ul class="img-list">`;
                                if(msg[0]['data'][i]['pictures']){
                                    for(var j=0;j<msg[0]['data'][i]['pictures'].length;j++){
                                        str += `<li><img src="${msg[0]['data'][i]['pictures'][j]}" alt=""></li>`;
                                    }
                                }
                                str +=`</ul>
                                        </div>
                                    </td>`;
                                switch (msg[0]['data'][i]['is_certification']){
                                    case 4: str +=  `<td><span class="status-pass">Pass</span></td>`; break;
                                    case 3: str +=  `<td><span class="status-reject">Reject</span></td>`; break;
                                    case 1: str +=  `<td><span class="status-raw">Raw</span></td>`; break;
                                }
                                if(msg[0]['data'][i]['bind_user_id']){
                                    str += `<td>${msg[0]['data'][i]['bind_user_id']}</td>`;
                                }else{
                                    str += `<td>--</td>`;
                                }
                                str += `<td>
                                          <a class="action-edit"><i class="iconfont icon-bianji"></i>EDIT</a>
                                        </td>`;
                                if(msg[0]['data'][i]['is_certification'] === 1){
                                    str += `
                                        <td><a class="action-pass">Pass</a>
                                        <a class="action-reject">Reject</a></td>
                                        `;
                                }else{
                                    str += `<td><a class="action-again">Check it again</a></td>`;
                                }
                                str += `</tr>`;
                            }
                            $('#change tbody').empty().append(str);
                            $('.loading').hide();
                        }else {
                            layer.msg('Success Error.', {
                                offset: 'auto',
                                anim: 6,
                                area:['420px']
                            });
                        }
                        if(type == 1){
                            $('#pagination').bootstrapPaginator(opt);
                        }
                    },
                    error:function () {
                        layer.msg('Error.', {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                    }
                });
            }
        }
        opt.onPageClicked('','',1,1);
        $('#pagination').bootstrapPaginator(opt);
        $('#change').on('click','.action-pass',function () {
            var userid = $(this).parent().parent().attr('userid');
            var that = $(this);
            console.log(userid);
            $.ajax({
                url:"{{route('check_action')}}",
                data:{'user_id':userid,'type':4},
                dataType:'json',
                type:'post',
                success:function (msg) {
                    if(msg.code == 200) {
                        layer.msg('Success.', {
                            offset: 'auto',
                            anim: 0,
                            area:['420px']
                        });
                        var sta = `Pass`;
                        var but = `<a class="action-again">Check it again</a>`;
                        that.parent().prev('td').empty().append(sta);
                        that.parent().empty().append(but);
                    }else {
                        layer.msg('Success Error.', {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                    }
                },
                error:function () {
                    layer.msg('Error.', {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                }
            });
        });
        $("#change").on('click',".action-edit",function(){
            var trid = $(this).parent().parent().attr('trid');
            window.location.href = "{{env('ADMIN_DOMAIN').'/editUserCheck'}}?id="+trid;
        });
        $('#change').on('click','.action-reject',function () {
            reasons = [];
            that = $(this);
            $("#confirm_modal").modal();//#4a5270
        });
        $('#change').on('click','.action-again',function () {
            var userid = $(this).parent().parent().attr('userid');
            var that = $(this);
            console.log(userid);
            $.ajax({
                url:"{{route('check_action')}}",
                data:{'user_id':userid,'type':1},
                dataType:'json',
                type:'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function (msg) {
                    if(msg.code == 200) {
                        layer.msg('Success.', {
                            offset: 'auto',
                            anim: 0,
                            area:['420px']
                        });
                        var sta = `Raw`;
                        var but = `     <a class="action-pass">Pass</a>
                                        <a class="action-reject">Reject</a>
                                        `;
                        that.parent().prev('td').empty().append(sta);
                        that.parent().empty().append(but);
                    }else {
                        layer.msg('Success Error.', {
                            offset: 'auto',
                            anim: 6,
                            area:['420px']
                        });
                    }
                },
                error:function () {
                    layer.msg('Error.', {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                }
            });
        });
    });
    function reason_ok() {//选择理由确认
        $('.reasons_box input[type=checkbox]').each(function (index, item) {
            if ($(item).prop('checked') == true) {
                var para = $(item).parent().parent().attr('trid');
                if(reasons.indexOf(para) < 0)
                    reasons.push($(item).parent().parent().attr('trid'))
            }
        });
        var userid = that.parent().parent().attr('userid');
        console.log(userid);
        $.ajax({
            url:"{{route('check_action')}}",
            data:{'user_id':userid,'type':3,'reasons':reasons},
            dataType:'json',
            type:'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function (msg) {
                if(msg.code == 200) {
                    layer.msg('Success.', {
                        offset: 'auto',
                        anim: 0,
                        area:['420px']
                    });
                    var sta = `Reject`;
                    var but = `<a class="action-again">Check it again</a>`;
                    that.parent().prev('td').empty().append(sta);
                    that.parent().empty().append(but);
                    $("#confirm_modal").modal('hide');
                }else {
                    layer.msg('Success Error.', {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                }
            },
            error:function () {
                layer.msg('Error.', {
                    offset: 'auto',
                    anim: 6,
                    area:['420px']
                });
            }
        });
    }
</script>
@endsection
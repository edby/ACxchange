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
    .reason_input {
        float: right;
        width: 20px;
        height: 20px;
        margin: 2px auto;
    }
</style>
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl" user="{{$user->id}}"><a href="{{env('ADMIN_DOMAIN').'/check/'}}{{$status}}">{{$status}}</a> <span>&gt;</span> Detail</div>
        <div class="wrapper-content">             
            <div class="details">
                <!-- nopass-content 没有通过的时候加nopass-content 已经通过的时候加pass-content   未审核时不加-->
                <div class="details-content {{$auth_status}}">
                    <div class="dblock">
                        <div class="dinline">
                            <label for="">Actual name:</label>
                            <input type="text" value="{{$user->first_name}} {{$user->last_name}}">
                        </div>
                    </div>
                    <div class="dblock">
                        <div class="dinline">
                            <label for="">{{$user->type}} number:</label>
                            <input type="text" value="{{$user->id_number}}">
                        </div>
                    </div>
                    <div class="dblock">
                        <div class="dinline">
                            <label for="">Binding-State:</label>
                            <input type="text" value="@if($user->bind_user_id){{$user->bind_user_id}}@else none @endif">
                        </div>
                    </div>
                    <div class="dblock picture">
                        <div class="dinline">
                            <label for="">{{$user->type}} Photos:</label>
                            <div class="idcard-warp">
                                @foreach($user->pictures as $key=>$row)
                                <div id="MagnifierWrap{{$key+1}}">
                                    <div class="MagnifierMain">
                                        <img class="MagTargetImg" src="{{asset($row)}}">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @if($status == 'unchecked')
                        <div class="btn-group">
                            <button type="button" class="reject" onclick="dialogModel('reject',3)" >Reject</button>
                            <button type="button" class="pass" onclick="dialogModel('pass',4)">Pass</button>
                        </div>
                    @else
                        <div class="btn-group">
                            <button type="button" class="angree again" onclick="dialogModel('again',1)">check again</button>
                        </div>
                    @endif
                </div> 
            </div>
        </div>
    </div>
</section>
{{--确认弹窗--}}
<div class="modal fade" id="audit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="check">
                <i class="iconfont icon-cuo"  data-dismiss="modal"></i>
                <img src="{{asset('dashboard/dist/img/notice.png')}}" alt="">
                <p>Sure to <span class="mes"></span>?</p>
                <div class="check-btn-group">
                    <a href="javascript:;" class="affirm">Yes</a>
                    <a href="javascript:;" class="abolish" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>
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
                <button style="background: #0f91ff" id="send_authy" type="button" class="btn btn-nov" onclick="reason_ok()">OK</button>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('dashboard/dist/js/magnifierf.js')}}"></script>

<script>
    $(function(){
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
                                <p> ${msg[0][i]['reason_en']}
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

        // $(".reasons_box").

        var ww = $('body').width();
        if(ww> 640) {
            MagnifierF("MagnifierWrap1");
            MagnifierF("MagnifierWrap2");
            @if(count($user->pictures)>2)
            MagnifierF("MagnifierWrap3");
            @endif
        }
    });
    var reasons = [];

    /**弹出层，包括兼容性**/
    function dialogModel(dom,status) {
        var w = $(window).width();
        if(dom == 'again') dom = 'check it again';
        $("#audit .mes").html(dom);
        if(w<540){
            $("#audit").find('.modal-dialog').css('width', (w-20) +'px')
            $("#audit").modal();
        }else{
            $("#audit").modal();
        }
        $(".affirm").on('click',function () {
            if(status == 3){//若拒绝，转去选择理由
                reasons = [];
                $("#confirm_modal").modal();//#4a5270
            }else{
                console.log(4);
                ajaxdata(4);
            }
        })
    }

    function reason_ok() {//选择理由确认
        $('.reasons_box input[type=checkbox]').each(function (index, item) {
            if ($(item).prop('checked') == true) {
                var para = $(item).parent().parent().attr('trid');
                if(reasons.indexOf(para) < 0)
                    reasons.push($(item).parent().parent().attr('trid'))
            }
        });
        ajaxdata(3);
    }

    function ajaxdata(status) {//提交操作
        var userid = $(".titl").attr('user');
        console.log(userid);
        $.ajax({
            url:"{{route('check_action')}}",
            data:{'user_id':userid,'type':status,'reasons':reasons},
            dataType:'json',
            type:'post',
            success:function (msg) {
                if(msg.code == 200) {
                    $("#audit").empty();
                    layer.msg('Success!', {
                        offset: 'auto',
                        anim: 0,
                        area:['420px']
                    });
                    setTimeout(function(){
                        window.location.href = "{{env('ADMIN_DOMAIN').'/check/'}}{{$status}}";
                    },800)
                }else {
                    layer.msg('Data Error', {
                        offset: 'auto',
                        anim: 6,
                        area:['420px']
                    });
                }
            },
            error:function () {
                layer.msg('Ajax Error', {
                    offset: 'auto',
                    anim: 6,
                    area:['420px']
                });
            }
        });
    }
</script>
@endsection
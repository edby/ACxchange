@extends('dashboard.layout.app')
@section('style')
    <link href="{{ asset('dashboard/plugins/calendar/calendar.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="content">
        <div class="wallet_history">
            <!-- 表格 -->
            <div class="pendingsendTable">
                <!-- 表格1 -->
                <div class="table2 thePendingTable-box ">
                    <table class="pendingsend_userInfoTable" id="change">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>ToAddress</th>
                            <th>Status</th>
                            <th>Action</th>
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
    <div class="loading" style="display: none;">
        <img class="img-gif" src="{{asset('dashboard/dist/img/Loading.gif')}}">
    </div>

    <script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
    <script src='{{ asset('dashboard/plugins/calendar/calendar.js')}}'></script>

    <script>
        $(function () {
            // var filter = [];
            $('#sear').on('click', function () {
                opt.onPageClicked('', '', 1, 1);
            });
            var opt = {
                currentPage: 1,
                totalPages: 1,
                numberOfPages: 5,
                onPageClicked: function (event, originalEvent, type, newPage) {
                    $.ajax({
                        url: "{{route('withdrawal_wait')}}",
                        data:{'page':newPage},
                        dataType: 'json',
                        type: 'get',
                        success: function (msg) {
                            if (msg.code == 200) {
                                var str = '';
                                opt.totalPages = Math.ceil(msg[1] / 10);
                                for (var i in msg[0]['data']) {
                                    str += `<tr trid="${msg[0]['data'][i]['id']}" user_id="${msg[0]['data'][i]['user_id']}" amount="${msg[0]['data'][i]['amount']} ${msg[0]['data'][i]['currency']}">
                                                <td>${msg[0]['data'][i]['created_at']}</td>
                                                <td>${msg[0]['data'][i]['user_id']}</td>
                                                <td>${msg[0]['data'][i]['name']}</td>
                                                <td>${msg[0]['data'][i]['currency']} ${msg[0]['data'][i]['amount']}</td>
                                                <td>${msg[0]['data'][i]['currency']} ${msg[0]['data'][i]['max_fee']}</td>
                                                <td>${msg[0]['data'][i]['address']}</td>
                                                <td style="color: #ff0000;">Verification in progress</td>
                                                <td>
                                                    <button style="width: 120px; height: 30px; background: #0f91ff" class="btn resend">Resend Email</button>
                                                </td>
                                        </tr>`;
                                }
                                $('#change tbody').empty().append(str);
                                $('.history_total').empty().html('Total ' + msg[1] + ' Withdrawals');
                                if (msg[1] === 0) {
                                    $('#pagination').empty();
                                }
                            } else {
                                layer.msg('Data Error', {
                                    offset: 'auto',
                                    anim: 6,
                                    area:['420px']
                                });
                            }
                            if (type == 1) {
                                $('#pagination').bootstrapPaginator(opt);

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
                }
            }
            opt.onPageClicked('', '', 1, 1);
            $('#pagination').bootstrapPaginator(opt);

            $("#change").on('click',".resend",function () {
                var trid = $(this).parent().parent().attr('trid');
                $(".loading").show();
                $.ajax({
                    url: "{{route('withdrawal_resendEmail')}}",
                    data: {'id': trid},
                    dataType: 'json',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (msg) {
                        if (msg.code === 200) {
                            $(".loading").hide();
                            layer.msg('Success', {
                                offset: 'auto',
                                anim: 0,
                                area:['420px']
                            });
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
            });
        })
    </script>
@endsection

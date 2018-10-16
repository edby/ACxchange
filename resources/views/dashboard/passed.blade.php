@extends('dashboard.layout.app')
@section('content')
    <section class="content">
        <div class="dashboard">
            <div class="titl">Passed</div>
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
                    <table class="uncheck-list" id="change">
                        <thead>
                        <tr>
                            <th>Submission time</th>
                            <th>Actual name</th>
                            <th>User ID</th>
                            <th>Status</th>
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
    <script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>

    <script>
        $(function() {
            $('#sear').on('click',function () {
                opt.onPageClicked('','',1,1);
            });
            var opt = {
                currentPage: 1,
                totalPages: 1,
                numberOfPages:5,
                onPageClicked:function (event, originalEvent, type, newPage) {
                    var something = $('#search').val();
                    $.ajax({
                        url:"{{route('check_status','passed')}}",
                        data:{'page':newPage,'something':something},
                        dataType:'json',
                        type:'get',
                        success:function (msg) {
                            if(msg.code == 200) {
                                var str = '';
                                opt.totalPages = Math.ceil(msg[1]/10);
                                for(var i in msg[0]['data']) {
                                    str += `<tr>
                                                <td>${msg[0]['data'][i]['certification_time']}</td>
                                                <td>${msg[0]['data'][i]['first_name']} ${msg[0]['data'][i]['last_name']}</td>
                                                <td>${msg[0]['data'][i]['id']}</td>
                                                <td>
                                                    <a href="{{env('ADMIN_DOMAIN').'/check-detail'}}?status=${msg[2]}&user_id=${msg[0]['data'][i]['id']}" class="btn ${msg[3]}">${msg[2]}</a>
                                                </td>
                                            </tr>`;
                                }
                                $('#change tbody').empty().append(str);
                                if(msg[1] === 0){
                                    $('#pagination').empty();
                                }
                            }else {
                                layer.msg('Failure', {
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
                            layer.msg('Error', {
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
        });
    </script>
@endsection
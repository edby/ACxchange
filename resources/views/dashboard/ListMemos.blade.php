@extends('dashboard.layout.app')
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl">List Memos</div>
        <a href="javascript:;" class="upload upload-btn"><i class="iconfont icon-jia"></i>UPLOAD</a>
        <div class="wrapper-content">
            <div class="list-client">
                <table class="list-client-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Visible</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>samuel</td>
                            <td>ceshiceshi.PNG</td>
                            <td>0.5 MB</td>
                            <td><i class="iconfont icon-iconfontcheck visible-true"></i></td>
                            <td>2018-01-19 11:36:28</td>
                            <td>
                                <a href="javascript:;" class="action-view">VIEW</a>
                                <a href="javascript:;" class="action-edit">EDIT</a>
                                <a href="javascript:;" class="action-delete">DELETE</a>
                            </td>
                        </tr>
                        <tr>
                            <td>samuel</td>
                            <td>ceshiceshi.PNG</td>
                            <td>0.5 MB</td>
                            <td><i class="iconfont icon-cuo visible-false"></i></td>
                            <td>2018-01-19 11:36:28</td>
                            <td>
                                <a href="javascript:;" class="action-view">VIEW</a>
                                <a href="javascript:;" class="action-edit">EDIT</a>
                                <a href="javascript:;" class="action-delete">DELETE</a>
                            </td>
                        </tr>
                        <tr>
                            <td>samuel</td>
                            <td>ceshiceshi.PNG</td>
                            <td>0.5 MB</td>
                            <td><i class="iconfont icon-iconfontcheck visible-ture"></i></td>
                            <td>2018-01-19 11:36:28</td>
                            <td>
                                <a href="javascript:;" class="action-view">VIEW</a>
                                <a href="javascript:;" class="action-edit">EDIT</a>
                                <a href="javascript:;" class="action-delete">DELETE</a>
                            </td>
                        </tr>
                        <tr>
                            <td>samuel</td>
                            <td>ceshiceshi.PNG</td>
                            <td>0.5 MB</td>
                            <td><i class="iconfont icon-cuo visible-false"></i></td>
                            <td>2018-01-19 11:36:28</td>
                            <td>
                                <a href="javascript:;" class="action-view">VIEW</a>
                                <a href="javascript:;" class="action-edit">EDIT</a>
                                <a href="javascript:;" class="action-delete">DELETE</a>
                            </td>
                        </tr>
                        <tr>
                            <td>samuel</td>
                            <td>ceshiceshi.PNG</td>
                            <td>0.5 MB</td>
                            <td><i class="iconfont icon-iconfontcheck visible-true"></i></td>
                            <td>2018-01-19 11:36:28</td>
                            <td>
                                <a href="javascript:;" class="action-view">VIEW</a>
                                <a href="javascript:;" class="action-edit">EDIT</a>
                                <a href="javascript:;" class="action-delete">DELETE</a>
                            </td>
                        </tr>
                        <tr>
                            <td>samuel</td>
                            <td>ceshiceshi.PNG</td>
                            <td>0.5 MB</td>
                            <td><i class="iconfont icon-cuo visible-false"></i></td>
                            <td>2018-01-19 11:36:28</td>
                            <td>
                                <a href="javascript:;" class="action-view">VIEW</a>
                                <a href="javascript:;" class="action-edit">EDIT</a>
                                <a href="javascript:;" class="action-delete">DELETE</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="pagination" class="pagination"></div>
</section>

@endsection
<script src="{{asset('admin/dashboard/dist/js/bootstrap-paginator.min.js')}}" type="text/javascript"></script>
<script>
    var opt = {
        currentPage: 1,
        totalPages: 10,
        numberOfPages: 5,
        // onPageClicked:function(event, originalEvent, type,page) {
        // console.log(page)
        // },
        onPageChanged: function (event, oldPage, newPage) {
        console.log(newPage) /*页码*/
        /*ajax*/
        }
    }
    $('#pagination').bootstrapPaginator(opt);
</script>
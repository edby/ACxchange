@extends('dashboard.layout.app')
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl">Upload a File</div>
        <div class="listFile">
            <div class="list">
                <span>Videos</span>
                <div class="listbox">
                        <table>
                                <thead>
                                    <tr><th>File Name</th><th>Description</th><th>Size</th><th>Visible</th><th>Created</th><th>Actions</th></tr>
                                </thead>
                                
                                <tbody>
                                    <!-- 没有数据时 -->
                                    <!-- <tr><td colspan="4"><div class="no-transfer">no transfer</div></td><td></td><td></td><td></td></tr> -->
                                    <tr>
                                        <td>banner.jpg</td>
                                        <td>100</td>
                                        <td>0.09MB</td>
                                        <td><i class="iconfont icon-iconfontcheck"></i></td>
                                        <td>2017-08-24 14:05:38</td>
                                        <td>
                                            <button class="view">VIEW</button>
                                            <button class="download">DOWNLOAD</button>
                                            <button class="edit">EDIT</button>
                                            <button class="delete">DELETE</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>banner.jpg</td>
                                        <td>100</td>
                                        <td>0.09MB</td>
                                        <td><i class="iconfont icon-iconfontcheck"></i></td>
                                        <td>2017-08-24 14:05:38</td>
                                        <td>
                                            <button class="view">VIEW</button>
                                            <button class="download">DOWNLOAD</button>
                                            <button class="edit">EDIT</button>
                                            <button class="delete">DELETE</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </div>
                            </table>                                         
                </div>
                <div id="pagination" class="pagination" style="margin-bottom: 60px">
                    <ul>
                        <li class="active"><a title="Current page is 1">1</a></li><li><a title="Go to page 2">2</a></li><li><a title="Go to page 3">3</a></li><li><a title="Go to page 4">4</a></li><li><a title="Go to page 5">5</a></li><li><a title="Go to next page">&gt;</a></li><li><a title="Go to last page">&gt;&gt;</a></li>
                    </ul>
                </div>
            </div>
            <div class="list">
                    <span>Presentations</span>
                    <div class="listbox">
                            <table>
                                    <thead>
                                        <tr><th>File Name</th><th>Description</th><th>Size</th><th>Visible</th><th>Created</th><th>Actions</th></tr>
                                    </thead>
                                    
                                    <tbody>
                                        <!-- 没有数据时 -->
                                        <tr><td colspan="6"><div class="nodata">No data</div></td><td></td><td></td><td></td><td></td><td></td></tr>
                                        <!-- <tr>
                                            <td>banner.jpg</td>
                                            <td>100</td>
                                            <td>0.09MB</td>
                                            <td><i class="iconfont icon-iconfontcheck"></i></td>
                                            <td>2017-08-24 14:05:38</td>
                                            <td>
                                                <button class="view">VIEW</button>
                                                <button class="download">DOWNLOAD</button>
                                                <button class="edit">EDIT</button>
                                                <button class="delete">DELETE</button>
                                            </td>
                                        </tr> -->
                                    </tbody>
                                </div>
                                </table>                                         
                    </div>
                    <div id="pagination2" class="pagination">
                            <ul>
                                <li class="active"><a title="Current page is 1">1</a></li><li><a title="Go to page 2">2</a></li><li><a title="Go to page 3">3</a></li><li><a title="Go to page 4">4</a></li><li><a title="Go to page 5">5</a></li><li><a title="Go to next page">&gt;</a></li><li><a title="Go to last page">&gt;&gt;</a></li>
                            </ul>
                        </div>
                    
                </div>
        </div>
        
    </div>
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
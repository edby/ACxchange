@extends('dashboard.layout.app')
@section('content')
    <section class="content">
        <div class="dashboard">
            <div class="titl">Active Tickets</div>
            <div class="wrapper-content">
                <div class="list-client">
                    <table class="list-client-table-tick">
                        <thead>
                            <tr>
                                <th>Status </th>
                                <th>From Client ID</th>
                                <th>Created</th>
                                <th>Last Update</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2018-01-02 14:05:13</td>
                                <td>CNY201801031010</td>
                                <td>Donald Jelfson</td>
                                <td>Shing@shangtan163.com</td>
                                <td>
                                    <a href="javascript:;" class="action-del">DELETE</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2018-01-02 14:05:13</td>
                                <td>CNY201801031010</td>
                                <td>Donald Jelfson</td>
                                <td>Shing@shangtan163.com</td>
                                <td>
                                    <a href="javascript:;" class="action-del">DELETE</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2018-01-02 14:05:13</td>
                                <td>CNY201801031010</td>
                                <td>Donald Jelfson</td>
                                <td>Shing@shangtan163.com</td>
                                <td>
                                    <a href="javascript:;" class="action-del">DELETE</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2018-01-02 14:05:13</td>
                                <td>CNY201801031010</td>
                                <td>Donald Jelfson</td>
                                <td>Shing@shangtan163.com</td>
                                <td>
                                    <a href="javascript:;" class="action-del">DELETE</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2018-01-02 14:05:13</td>
                                <td>CNY201801031010</td>
                                <td>Donald Jelfson</td>
                                <td>Shing@shangtan163.com</td>
                                <td>
                                    <a href="javascript:;" class="action-del">DELETE</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2018-01-02 14:05:13</td>
                                <td>CNY201801031010</td>
                                <td>Donald Jelfson</td>
                                <td>Shing@shangtan163.com</td>
                                <td>
                                    <a href="javascript:;" class="action-del">DELETE</a>
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
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>  
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('style')
    <!-- Bootstrap 3.3.4 -->
    <link href="{{ asset('dashboard/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="{{ asset('dashboard/dist/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{ asset('dashboard/dist/css/style.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dashboard/dist/fonts/iconfonts/iconfont.css')}}" rel="stylesheet" type="text/css" />
    <!-- jQuery 2.1.4 -->
    <script src="{{asset('dashboard/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <script src='{{asset('dashboard/dist/js/prompt.js')}}'></script>
    <script src="{{ asset('layer/layer.js') }}" type="text/javascript"></script>
    @yield('script')
    <!-- paginator -->
    <script src="{{asset('dashboard/dist/js/bootstrap-paginator.min.js')}}"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="dist/js/html5shiv.min.js"></script>
        <script src="dist/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="sidebar-mini">
    <div class="wrapper">

        <header class="main-header">

            <!-- Logo -->
            <a href="" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">AC</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><img src="{{asset('dashboard/dist/img/login-logo.png')}}"></span>
            </a>

            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="javascript:;" data-toggle="offcanvas" class="munubtn">
                    <i class="iconfont icon-menu2"></i>
                </a>
                {{--<div class="search">
                     <div><input class="search-input" placeholder="Search..."></div>
                     <div><a class="seach-button"><i class="iconfont icon-icon--"></i></a></div>
                </div>--}}
               
                <div class="navbar-custom-menu">
                    <div class="peoplename"><a id="manager">Welcome</a></div>
                    <div><img src="{{asset('images/logo.png')}}" width="50px"></div>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu">
                    <li class="active treeview">
                        <a href="{{env('ADMIN_DOMAIN').'/ACdashboard'}}">
                            <i class="iconfont icon-caidan06"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="javascript:;">
                            <i class="iconfont icon-icon_pc"></i>
                            <span>Account Management</span>
                            <i class="iconfont icon-jia btns"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/getUserList'}}">
                                    <i class="iconfont"></i>List Clients</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/userBalance'}}">
                                    <i class="iconfont"></i>Clients Balance</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/getUserDisabled'}}">
                                    <i class="iconfont"></i>Disable Clients</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/getUserCheck'}}">
                                    <i class="iconfont"></i>Identity Verification</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/userWithdrawTerm'}}">
                                    <i class="iconfont"></i>Clients Withdraw Term</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/administrators'}}">
                                    <i class="iconfont"></i> Administrators</a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="javascript:;">
                            <i class="iconfont icon-qianbao"></i>
                            <span>Wallet Management </span>
                            <i class="iconfont icon-jia btns"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/WalletManagement/adjust'}}">
                                    <i class="iconfont"></i>Adjust</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/WalletManagement/airdrop'}}">
                                    <i class="iconfont"></i>Airdrop</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/getHistory'}}">
                                    <i class="iconfont"></i>Wallet History</a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="javascript:;">
                            <i class="iconfont icon-gongwenchaxun"></i>
                            <span>Withdrawal Audit </span>
                            <i class="iconfont icon-jia btns"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/withdrawal/pending'}}">
                                    <i class="iconfont"></i>Pending</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/withdrawal/approve'}}">
                                    <i class="iconfont"></i>Approve</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/withdrawal/reject'}}">
                                    <i class="iconfont"></i>Reject</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/withdrawal_waitEmail'}}">
                                    <i class="iconfont"></i>Withdrawal Email Resend</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/userWithdrawalReport'}}">
                                    <i class="iconfont"></i>User Withdrawal Report</a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="javascript:;">
                            <i class="iconfont icon-transaction"></i>
                            <span>Transactions</span>
                            <i class="iconfont icon-jia btns"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/getChange'}}">
                                    <i class="iconfont"></i>Transactions</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/getChange?ignored=ture'}}">
                                    <i class="iconfont"></i> Report</a>
                            </li>

                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="javascript:;">
                            <i class="iconfont icon-information"></i>
                            <span>Customer information</span>
                            <i class="iconfont icon-jia btns"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/check/unchecked'}}">
                                    <i class="iconfont"></i>Unchecked</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/check/passed'}}">
                                    <i class="iconfont"></i>Passed</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/check/rejected'}}">
                                    <i class="iconfont"></i>Rejected</a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="javascript:;">
                            <i class="iconfont icon-menu2"></i>
                            <span>Memos</span>
                            <i class="iconfont icon-jia btns"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/updateUserLists'}}">
                                    <i class="iconfont"></i>Upload Memo</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/ListMemos'}}">
                                    <i class="iconfont"></i>List Memos</a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="javascript:;">
                            <i class="iconfont icon-icon--1"></i>
                            <span>Downloads</span>
                            <i class="iconfont icon-jia btns"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/uploadAFile'}}">
                                    <i class="iconfont"></i>Upload a File</a>
                            </li>
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/listFile'}}">
                                    <i class="iconfont"></i>List File</a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="javascript:;">
                            <i class="iconfont icon-toupiao"></i>
                            <span>Support</span>
                            <i class="iconfont icon-jia btns"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{env('ADMIN_DOMAIN').'/activeTicket'}}">
                                    <i class="iconfont"></i>Active Tickets</a>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <i class="iconfont"></i>Closed Tickets</a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="{{env('ADMIN_DOMAIN').'/managerLog'}}">
                            <i class="iconfont icon-rili"></i>
                            <span>Manager Logs</span>
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="{{env('ADMIN_DOMAIN').'/loginOut'}}">
                            <i class="iconfont icon-logout"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

    </div>
    <!-- ./wrapper -->

    
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('dashboard/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
    <!-- FastClick -->
    <script src="{{asset('dashboard/plugins/fastclick/fastclick.min.js')}}"></script>
    <!-- App -->
    <script src="{{asset('dashboard/dist/js/app.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('dashboard/dist/js/index.js')}}" type="text/javascript"></script>
    
</body>

</html>
<script>
    $(function() {
        $.ajax({
            url:"{{route('manager_info')}}",
            dataType:'json',
            type:'get',
            success:function (msg) {
                if(msg.code == 200) {
                    $('#manager').empty().html('Welcome, ' + msg[0].name)
                    if(msg[0].role < 3){
                        $('.non-normal').empty();
                    }
                }else {
                    prompt('wrapper','fail','Error for bug.');
                }
            },
            error:function () {
                prompt('wrapper','fail','Error.');
            }
        });
    })
</script>
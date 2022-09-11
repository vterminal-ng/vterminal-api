<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>vTerminal - Admin Dashboard</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/media/image/vt-favicon.ico') }}"/>

    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset('vendors/bundle.css') }}" type="text/css">

    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Daterangepicker -->
    <link rel="stylesheet" href="{{ asset('vendors/datepicker/daterangepicker.css') }}" type="text/css">

    <!-- DataTable -->
    <link rel="stylesheet" href="{{ asset('vendors/dataTable/datatables.min.css') }}" type="text/css">

<!-- App css -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}" type="text/css">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Preloader -->
<div class="preloader">
    <div class="preloader-icon"></div>
    <span>Loading...</span>
</div>
<!-- ./ Preloader -->

<!-- Layout wrapper -->
<div class="layout-wrapper">

    <!-- Header -->
    <div class="header d-print-none">
        <div class="header-container">
            <div class="header-left">
                <div class="navigation-toggler">
                    <a href="#" data-action="navigation-toggler">
                        <i data-feather="menu"></i>
                    </a>
                </div>

                <div class="header-logo">
                    <a href=index.html>
                        <img width="60px" class="logo" src="{{ asset('assets/media/image/vt-logo.png') }}" alt="logo">
                    </a>
                </div>
            </div>

            <div class="header-body">
                <div class="header-body-left">
                    <ul class="navbar-nav">
                        <li class="nav-item mr-3">
                            <!-- <div class="header-search-form">
                                <form>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn">
                                                <i data-feather="search"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Search">
                                        <div class="input-group-append">
                                            <button class="btn header-search-close-btn">
                                                <i data-feather="x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div> -->
                        </li>
                    </ul>
                </div>

                <div class="header-body-right">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="#" class="nav-link mobile-header-search-btn" title="Search">
                                <i data-feather="search"></i>
                            </a>
                        </li>

                        <li class="nav-item dropdown d-none d-md-block">
                            <a href="#" class="nav-link" title="Fullscreen" data-toggle="fullscreen">
                                <i class="maximize" data-feather="maximize"></i>
                                <i class="minimize" data-feather="minimize"></i>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" title="User menu" data-toggle="dropdown">
                                <figure class="avatar avatar-sm">
                                    <img src="../../assets/media/image/user/man_avatar3.jpg"
                                         class="rounded-circle"
                                         alt="avatar">
                                </figure>
                                <span class="ml-2 d-sm-inline d-none">{{ auth()->user()->userDetail->getFullNameAttribute() ?? '' }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                                <div class="text-center py-4">
                                    <figure class="avatar avatar-lg mb-3 border-0">
                                        <img src="../../assets/media/image/user/man_avatar3.jpg"
                                             class="rounded-circle" alt="image">
                                    </figure>
                                    <h5 class="text-center">{{ auth()->user()->userDetail->getFullNameAttribute() ?? '' }}</h5>
                                    <div class="mb-3 small text-center text-muted">{{ auth()->user()->email ?? '' }}</div>
                                    <!-- <a href="#" class="btn btn-outline-light btn-rounded">Manage Your Account</a> -->
                                </div>
                                <div class="list-group">
                                    <!-- <a href="profile.html" class="list-group-item">View Profile</a> -->
                                    <a href="{{ route('admin.logout') }}" class="list-group-item text-danger">Sign Out!</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item header-toggler">
                    <a href="#" class="nav-link">
                        <i data-feather="arrow-down"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- ./ Header -->

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- begin::navigation -->
        <div class="navigation">
            <div class="navigation-header">
                <span>Navigation</span>
                <a href="#">
                    <i class="ti-close"></i>
                </a>
            </div>
            <div class="navigation-menu-body">
                <ul>
                    <li>
                        <a class="{{ request()->route()->getName() == 'admin.dashboard'  ? 'active': '' }}" href="{{ route('admin.dashboard') }}">
                            <span class="nav-link-icon">
                                <i data-feather="pie-chart"></i>
                            </span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ request()->route()->getName() == 'admin.users'  ? 'active': '' }}" href="{{ route('admin.users') }}">
                            <span class="nav-link-icon">
                                <i data-feather="users"></i>
                            </span>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ request()->route()->getName() == 'admin.merchants'  ? 'active': '' }}" href="{{ route('admin.dashboard') }}">
                            <span class="nav-link-icon">
                                <i data-feather="shopping-bag"></i>
                            </span>
                            <span>Merchants</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ request()->route()->getName() == 'admin.customers'  ? 'active': '' }}" href="{{ route('admin.dashboard') }}">
                            <span class="nav-link-icon">
                                <i data-feather="user"></i>
                            </span>
                            <span>Customers</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- end::navigation -->

        <!-- Content body -->
        <div class="content-body">
            <!-- Content -->
            <div class="content ">
                @yield('content')
            </div>
            <!-- ./ Content -->

            <!-- Footer -->
            <footer class="content-footer">
                <div>© 2022 vTerminal - <a href="https://vterminal.ng/" target="_blank">vterminal.ng</a></div>
            </footer>
            <!-- ./ Footer -->
        </div>
        <!-- ./ Content body -->
    </div>
    <!-- ./ Content wrapper -->
</div>
<!-- ./ Layout wrapper -->

<!-- Main scripts -->
<script src="{{ asset('vendors/bundle.js') }}"></script>

    <!-- Apex chart -->
    <script src="{{ asset('vendors/charts/apex/apexcharts.min.js') }}"></script>

    <!-- Daterangepicker -->
    <script src="{{ asset('vendors/datepicker/daterangepicker.js') }}"></script>

    <!-- DataTable -->
    <script src="{{ asset('vendors/dataTable/datatables.min.js') }}"></script>

    <!-- Dashboard scripts -->
    <script src="{{ asset('assets/js/examples/pages/dashboard.js') }}"></script>

<!-- App scripts -->
<script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>
</html>
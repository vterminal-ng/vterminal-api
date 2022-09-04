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
                                <span class="ml-2 d-sm-inline d-none">Bony Gidden</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                                <div class="text-center py-4">
                                    <figure class="avatar avatar-lg mb-3 border-0">
                                        <img src="../../assets/media/image/user/man_avatar3.jpg"
                                             class="rounded-circle" alt="image">
                                    </figure>
                                    <h5 class="text-center">Bony Gidden</h5>
                                    <div class="mb-3 small text-center text-muted">@bonygidden</div>
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
                        <a class="active" href="{{ route('admin.dashboard') }}">
                            <span class="nav-link-icon">
                                <i data-feather="pie-chart"></i>
                            </span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="" href="{{ route('admin.dashboard') }}">
                            <span class="nav-link-icon">
                                <i data-feather="users"></i>
                            </span>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a class="" href="{{ route('admin.dashboard') }}">
                            <span class="nav-link-icon">
                                <i data-feather="shopping-bag"></i>
                            </span>
                            <span>Merchants</span>
                        </a>
                    </li>
                    <li>
                        <a class="" href="{{ route('admin.dashboard') }}">
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
                    <div class="page-header d-md-flex justify-content-between">
        <div>
            <h3>Welcome back, Bony</h3>
            <p class="text-muted">This page shows an overview for your account summary.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <div id="dashboard-daterangepicker" class="btn btn-outline-light">
                <span></span>
            </div>
            <a href="#" class="btn btn-primary ml-0 ml-md-2 mt-2 mt-md-0 dropdown-toggle" data-toggle="dropdown">Actions</a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item">Download</a>
                <a href="#" class="dropdown-item">Print</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title mb-2">Monthly Financial Status</h6>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-floating">
                                <i class="ti-reload"></i>
                            </a>
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Check how you're doing financially for current month</p>
                    <div id="sales"></div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-primary">
                            <i class="ti-download mr-2"></i> Create Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Positive Reviews</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="avatar">
                                        <span class="avatar-title bg-primary-bright text-primary rounded-pill">
                                            <i class="ti-cloud"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="font-weight-bold ml-1 font-size-30 ml-3">0.16%</div>
                            </div>
                            <p class="mb-0"><a href="#" class="link-1">See comments</a> and respond to customers' comments.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Bounce Rate</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="avatar">
                                        <span class="avatar-title bg-info-bright text-info rounded-pill">
                                            <i class="ti-map"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="font-weight-bold ml-1 font-size-30 ml-3">12.87%</div>
                            </div>
                            <p class="mb-0"><a class="link-1" href="#">See visits</a> that had a higher than expected
                                bounce rate.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Active Referrals</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="avatar">
                                        <span class="avatar-title bg-secondary-bright text-secondary rounded-pill">
                                            <i class="ti-email"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="font-weight-bold ml-1 font-size-30 ml-3">12.87%</div>
                            </div>
                            <p class="mb-0"><a class="link-1" href="#">See referring</a> domains that sent most visits
                                last month.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Opened Invites</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="avatar">
                                        <span class="avatar-title bg-warning-bright text-warning rounded-pill">
                                            <i class="ti-dashboard"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="font-weight-bold ml-1 font-size-30 ml-3">12.87%</div>
                            </div>
                            <p class="mb-0"><a class="link-1" href="#">See clients</a> that accepted your invitation to
                                connect.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title mb-2">Report</h6>
                        <div class="dropdown">
                            <a href="#" class="btn btn-floating" data-toggle="dropdown">
                                <i class="ti-more-alt"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Stats</h5>
                                    <div>Last month targets</div>
                                </div>
                                <h3 class="text-success mb-0">$1,23M</h3>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Payments</h5>
                                    <div>Week's expenses</div>
                                </div>
                                <div>
                                    <h3 class="text-danger mb-0">- $58,90</h3>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Orders</h5>
                                    <div>Total products ordered</div>
                                </div>
                                <div>
                                    <h3 class="text-info mb-0">65</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="#" class="btn btn-info">Report Detail</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title mb-2">Statistics</h6>
                        <div class="dropdown">
                            <a href="#" class="btn btn-floating" data-toggle="dropdown">
                                <i class="ti-more-alt"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Reports</h5>
                                    <div>Monthly sales reports</div>
                                </div>
                                <h3 class="text-primary mb-0">421</h3>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>User</h5>
                                    <div>Visitors last week</div>
                                </div>
                                <div>
                                    <h3 class="text-success mb-0">+10</h3>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Sales</h5>
                                    <div>Total average weekly report</div>
                                </div>
                                <div>
                                    <h3 class="text-primary mb-0">140</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="#" class="btn btn-warning">Statistics Detail</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-title mb-2 text-center">Financial year</h6>
                    <p class="mb-0 text-muted">Expenses statistics to date</p>
                    <hr>
                    <div class="font-size-40 font-weight-bold">$502,680</div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Current month</p>
                            <div>
                                <span class="font-weight-bold">$46,362</span>
                                <span class="badge bg-danger-bright text-danger ml-1">-8%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Last year</p>
                            <div>
                                <span class="font-weight-bold">$34,546</span>
                                <span class="badge bg-success-bright text-success ml-1">-13%</span>
                            </div>
                        </div>
                    </div>
                    <p class="font-weight-bold">Monthly report</p>
                    <div id="ecommerce-activity-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title">Reviews</h6>
                        <a href="#" class="link-1">View All</a>
                    </div>
                    <div class="card-scroll" style="height: 430px">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex px-0 py-4">
                                <a href="#" class="flex-shrink-0">
                                    <figure class="avatar mr-3">
                                        <img src="../../assets/media/image/user/man_avatar1.jpg"
                                             class="rounded-circle" alt="image">
                                    </figure>
                                </a>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <a href="#">
                                            <h6>Valentine Maton</h6>
                                            <ul class="list-inline mb-1">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">(5)</li>
                                            </ul>
                                        </a>
                                        <div class="ml-auto">
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown"
                                                   class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti-more-alt"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">View</a>
                                                    <a href="#" class="dropdown-item">Send Message</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio,
                                        tempora.</p>
                                </div>
                            </li>
                            <li class="list-group-item d-flex px-0 py-4">
                                <a href="#" class="flex-shrink-0">
                                    <figure class="avatar mr-3">
                                        <img src="../../assets/media/image/user/man_avatar2.jpg"
                                             class="rounded-circle" alt="image">
                                    </figure>
                                </a>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <a href="#">
                                            <h6>Valentine Maton</h6>
                                            <ul class="list-inline mb-1">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star-half-o text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star-o"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">(3.5)</li>
                                            </ul>
                                        </a>
                                        <div class="ml-auto">
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown"
                                                   class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti-more-alt"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">View</a>
                                                    <a href="#" class="dropdown-item">Send Message</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio,
                                        tempora.</p>
                                </div>
                            </li>
                            <li class="list-group-item d-flex px-0 py-4">
                                <a href="#" class="flex-shrink-0">
                                    <figure class="avatar mr-3">
                                        <img src="../../assets/media/image/user/man_avatar3.jpg"
                                             class="rounded-circle" alt="image">
                                    </figure>
                                </a>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <a href="#">
                                            <h6>Valentine Maton</h6>
                                            <ul class="list-inline mb-1">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star-half-o text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">(4.5)</li>
                                            </ul>
                                        </a>
                                        <div class="ml-auto">
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown"
                                                   class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti-more-alt"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">View</a>
                                                    <a href="#" class="dropdown-item">Send Message</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio,
                                        tempora.</p>
                                </div>
                            </li>
                            <li class="list-group-item d-flex px-0 py-4">
                                <a href="#" class="flex-shrink-0">
                                    <figure class="avatar mr-3">
                                        <img src="../../assets/media/image/user/man_avatar4.jpg"
                                             class="rounded-circle" alt="image">
                                    </figure>
                                </a>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <a href="#">
                                            <h6>Valentine Maton</h6>
                                            <ul class="list-inline mb-1">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star text-warning"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-star-o"></i>
                                                </li>
                                                <li class="list-inline-item mb-0">(4)</li>
                                            </ul>
                                        </a>
                                        <div class="ml-auto">
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown"
                                                   class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti-more-alt"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">View</a>
                                                    <a href="#" class="dropdown-item">Send Message</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio,
                                        tempora.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="card-title mb-4 text-center">Total sales this month</h6>
                        <h2 class="font-size-35 font-weight-bold text-center">$1.158,000</h2>
                        <p>This chart shows total sales. You can use the button below for details of this
                            month's sales.</p>
                        <div class="mt-4">
                            <a href="#" class="btn btn-primary">View Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-info-bright">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="card-title mb-4 text-center">Total sales in the past week</h6>
                        <h2 class="font-size-35 font-weight-bold text-center">$950,000</h2>
                        <p>This chart shows total sales. You can use the button below for details of this
                            month's sales.</p>
                        <div class="mt-4">
                            <a href="#" class="btn btn-primary">View Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h6 class="card-title mb-1">Monthly Sales</h6>
                            <p class="small text-muted">Avarage total sales +25,5%</p>
                        </div>
                        <div>
                            <a href="#" class="btn btn-floating">
                                <i class="fa fa-refresh"></i>
                            </a>
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="monthly-sales"></div>
                    <ul class="list-inline text-center">
                        <li class="list-inline-item">
                            <i class="fa fa-circle mr-1 text-success"></i> Bank Card <br>
                            <small class="text-muted">25,45% over</small>
                        </li>
                        <li class="list-inline-item">
                            <i class="fa fa-circle mr-1 text-primary"></i> Credit Card <br>
                            <small class="text-muted">75,55% over</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Recent Orders</h6>
            <div class="table-responsive">
                <table id="recent-orders" class="table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product1.png"
                                     class="rounded mr-3" alt="Vase">
                                <span>Vase</span>
                            </a>
                        </td>
                        <td>Dollie Bullock</td>
                        <td>$230</td>
                        <td>
                            <span class="badge bg-secondary-bright text-secondary">On pre-order (not paid)</span>
                        </td>
                        <td>2020/02/28</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product2.png"
                                     class="rounded mr-3" alt="Glasses">
                                <span>Glasses</span>
                            </a>
                        </td>
                        <td>Holmes Hines</td>
                        <td>$300</td>
                        <td>
                            <span class="badge bg-success-bright text-success">Payment accepted</span>
                        </td>
                        <td>2020/08/28</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product3.png"
                                     class="rounded mr-3" alt="Plate">
                                <span>Plate</span>
                            </a>
                        </td>
                        <td>Serena Glover</td>
                        <td>$250</td>
                        <td>
                            <span class="badge bg-danger-bright text-danger">Payment error</span>
                        </td>
                        <td>2020/08/28</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product4.png"
                                     class="rounded mr-3" alt="Pen">
                                <span>Pen</span>
                            </a>
                        </td>
                        <td>Dianne Prince</td>
                        <td>$550</td>
                        <td>
                            <span class="badge bg-success-bright text-success">Payment accepted</span>
                        </td>
                        <td>2020/08/28</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product5.png"
                                     class="rounded mr-3" alt="Armchair">
                                <span>Armchair</span>
                            </a>
                        </td>
                        <td>Morgan Pitts</td>
                        <td>$280</td>
                        <td>
                            <span class="badge bg-warning-bright text-warning">Preparing the order</span>
                        </td>
                        <td>2020/03/30</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product6.png"
                                     class="rounded mr-3" alt="Flowerpot">
                                <span>Flowerpot</span>
                            </a>
                        </td>
                        <td>Merrill Richardson</td>
                        <td>$128</td>
                        <td>
                            <span class="badge bg-info-bright text-info">Awaiting PayPal payment</span>
                        </td>
                        <td>2020/01/14</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product7.png"
                                     class="rounded mr-3" alt="Wall Clock">
                                <span>Wall Clock</span>
                            </a>
                        </td>
                        <td>Krista Mathis</td>
                        <td>$500</td>
                        <td>
                            <span class="badge bg-secondary-bright text-secondary">Shipped</span>
                        </td>
                        <td>2020/02/28</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product8.png"
                                     class="rounded mr-3" alt="Desk">
                                <span>Desk</span>
                            </a>
                        </td>
                        <td>Frankie Hewitt</td>
                        <td>$300</td>
                        <td>
                            <span class="badge bg-success-bright text-success">Remote payment accepted</span>
                        </td>
                        <td>2020/02/09</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product9.png"
                                     class="rounded mr-3" alt="Night Light">
                                <span>Night Light</span>
                            </a>
                        </td>
                        <td>Hayden Fitzgerald</td>
                        <td>$200</td>
                        <td>
                            <span class="badge bg-success-bright text-success">Delivered</span>
                        </td>
                        <td>2020/01/14</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="product-detail.html" class="d-flex align-items-center">
                                <img width="40" src="../../assets/media/image/products/product10.png"
                                     class="rounded mr-3" alt="Shoe">
                                <span>Shoe</span>
                            </a>
                        </td>
                        <td>Cole Holcomb</td>
                        <td>$700</td>
                        <td>
                            <span class="badge bg-secondary-bright text-secondary">On pre-order (not paid)</span>
                        </td>
                        <td>2020/02/28</td>
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-floating"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Send</a>
                                    <a href="#" class="dropdown-item">Edit</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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

@extends('layout')

@section('content')
    <div class="page-header d-md-flex justify-content-between">
        <div>
            <h3>Welcome back, {{ auth()->user()->userDetail->first_name ?? '' }}</h3>
            <p class="text-muted">This page shows an overview of the app usage.</p>
        </div>
        <!-- <div class="mt-3 mt-md-0">
            <div id="dashboard-daterangepicker" class="btn btn-outline-light">
                <span></span>
            </div>
            <a href="#" class="btn btn-primary ml-0 ml-md-2 mt-2 mt-md-0 dropdown-toggle" data-toggle="dropdown">Actions</a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item">Download</a>
                <a href="#" class="dropdown-item">Print</a>
            </div>
        </div> -->
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="card-title mb-4 text-center">Total users</h6>
                        <h2 class="font-size-35 font-weight-bold text-center">{{ $usersCount }}</h2>
                        <!-- <p>This chart shows total sales. You can use the button below for details of this
                            month's sales.</p> -->
                        <div class="mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">View Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info-gradient">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="card-title mb-4 text-center">Total customers</h6>
                        <h2 class="font-size-35 font-weight-bold text-center">{{ $customersCount }}</h2>
                        <!-- <p>This chart shows total sales. You can use the button below for details of this
                            month's sales.</p> -->
                        <div class="mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">View Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="card-title mb-4 text-center">Total merchants</h6>
                        <h2 class="font-size-35 font-weight-bold text-center">{{ $merchantsCount }}</h2>
                        <!-- <p>This chart shows total sales. You can use the button below for details of this
                            month's sales.</p> -->
                        <div class="mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">View Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-title mb-2 text-center">Total Codes</h6>
                    <p class="mb-0 text-muted">Expenses statistics to date</p>
                    <hr>
                    <div class="font-size-40 font-weight-bold">0</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Pending Codes</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="avatar">
                                        <span class="avatar-title bg-primary-bright text-primary rounded-pill">
                                            <i class="ti-cloud"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="font-weight-bold ml-1 font-size-30 ml-3">0</div>
                            </div>
                            <p class="mb-0"><a href="#" class="link-1">See comments</a> and respond to customers' comments.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Active Codes</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="avatar">
                                        <span class="avatar-title bg-info-bright text-info rounded-pill">
                                            <i class="ti-map"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="font-weight-bold ml-1 font-size-30 ml-3">0</div>
                            </div>
                            <p class="mb-0"><a class="link-1" href="#">See visits</a> that had a higher than expected
                                bounce rate.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Completed Codes</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="avatar">
                                        <span class="avatar-title bg-secondary-bright text-secondary rounded-pill">
                                            <i class="ti-email"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="font-weight-bold ml-1 font-size-30 ml-3">0</div>
                            </div>
                            <p class="mb-0"><a class="link-1" href="#">See referring</a> domains that sent most visits
                                last month.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Cancelled Codes</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="avatar">
                                        <span class="avatar-title bg-warning-bright text-warning rounded-pill">
                                            <i class="ti-dashboard"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="font-weight-bold ml-1 font-size-30 ml-3">0</div>
                            </div>
                            <p class="mb-0"><a class="link-1" href="#">See clients</a> that accepted your invitation to
                                connect.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title mb-2">Codes/Transactions</h6>
                        <!-- <div class="dropdown">
                            <a href="#" class="btn btn-floating" data-toggle="dropdown">
                                <i class="ti-more-alt"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </div>
                        </div> -->
                    </div>
                    <div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Total</h5>
                                    <div>No of all transactions</div>
                                </div>
                                <h3 class="text-primary mb-0">{{ $codesCount }}</h3>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Pending Codes</h5>
                                    <div>No of pending transactions</div>
                                </div>
                                <div>
                                    <h3 class="text-primary mb-0">{{ $pendingCodesCount }}</h3>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Active Codes</h5>
                                    <div>No of active transactions</div>
                                </div>
                                <div>
                                    <h3 class="text-success mb-0">{{ $activeCodesCount }}</h3>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Cancelled Codes</h5>
                                    <div>No of cancelled transactions</div>
                                </div>
                                <div>
                                    <h3 class="text-success mb-0">{{ $cancelledCodesCount }}</h3>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h5>Completed Codes</h5>
                                    <div>No of completed transactions</div>
                                </div>
                                <div>
                                    <h3 class="text-primary mb-0">{{ $completedCodesCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="mt-3">
                        <a href="#" class="btn btn-warning">Statistics Detail</a>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-title mb-2 text-center">Total Wallet balance</h6>
                    <p class="mb-0 text-muted">Wallet total balance to date</p>
                    <hr>
                    <div class="font-size-40 font-weight-bold">#{{ $totalWalletBalance }}</div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Customers Wallet Balance</p>
                            <div>
                                <span class="font-weight-bold">#{{ $customerWalletsBalance }}</span>
                                <!-- <span class="badge bg-danger-bright text-danger ml-1">-8%</span> -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Merchants Wallet Balance</p>
                            <div>
                                <span class="font-weight-bold">#{{ $merchantWalletsBalance }}</span>
                                <!-- <span class="badge bg-success-bright text-success ml-1">-13%</span> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

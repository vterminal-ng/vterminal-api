@extends('layout')

@section('content')
<div class="profile-container" style="background: url(../../assets/media/image/image1.jpg)">
        <div class="d-flex align-items-center">
            <figure class="avatar avatar-lg mr-3">
                <img src="../../assets/media/image/user/man_avatar3.jpg"
                     class="rounded-circle border border-white" alt="...">
            </figure>
            <div>
                <h4 class="mb-0">{{ $user->userDetail->first_name }} {{ $user->userDetail->last_name }}</h4>
                <small>{{ $user->email }}</small>
                <br />
                <small>{{ ucfirst($user->role) }}</small>
            </div>
        </div>
        <div class="profile-menu">
            <ul class="nav nav-pills flex-column flex-sm-row" id="myTab" role="tablist">
                <!-- <li class="flex-sm-fill text-sm-center nav-item">
                    <a class="nav-link active" id="home-tab22" data-toggle="tab" href="#home"
                       role="tab" aria-selected="true">Info</a>
                </li> -->
                <!-- <li class="flex-sm-fill text-sm-center nav-item">
                    <a class="nav-link" id="timeline-tab" data-toggle="tab" href="#timeline"
                       role="tab" aria-selected="true">Timeline</a>
                </li>
                <li class="flex-sm-fill text-sm-center nav-item">
                    <a class="nav-link" id="connections-tab1" data-toggle="tab" href="#connections"
                       role="tab"
                       aria-selected="false">
                        Connections
                    </a>
                </li>
                <li class="flex-sm-fill text-sm-center nav-item">
                    <a class="nav-link" id="earnings-tab" data-toggle="tab" href="#earnings"
                       role="tab"
                       aria-selected="false">Earnings</a>
                </li> -->
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-12">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <ul class="list-group">
                        <li class="list-group-item active" aria-disabled="true"><strong>User Details</strong></li>
                        <li class="list-group-item" aria-disabled="true"><strong>Phone Number:</strong> {{$user->userDetail->phone_number}}</li>
                        <li class="list-group-item"><strong>Gender:</strong> {{ ucfirst($user->userDetail->gender) }}</li>
                        <li class="list-group-item"><strong>DOB:</strong> {{ $user->userDetail->date_of_birth }}</li>
                        <li class="list-group-item"><strong>Wallet Balance:</strong> #{{ $user->balance }}</li>
                    </ul>

                    @if(isset($user->merchantDetail))
                    <ul class="list-group mt-4">
                        <li class="list-group-item active" aria-disabled="true"><strong>Merchant Details</strong></li>
                        <li class="list-group-item" aria-disabled="true"><strong>Business Name:</strong> {{ $user->merchantDetail->business_name }}</li>
                        <li class="list-group-item"><strong>Business State:</strong> {{ $user->merchantDetail->business_state }}</li>
                        <li class="list-group-item"><strong>Business City:</strong> {{ $user->merchantDetail->business_city }}</li>
                        <li class="list-group-item"><strong>Business Address:</strong> {{ $user->merchantDetail->business_address }}</li>
                        <li class="list-group-item"><strong>Business Verified At:</strong> {{ $user->merchantDetail->business_verified_at }}</li>
                        <li class="list-group-item"><strong>Physical Location:</strong> {{ $user->merchantDetail->has_physical_location }}</li>
                    </ul>
                    @endif
                </div>
                <!-- <div class="tab-pane fade show" id="timeline" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Timeline</h6>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div>
                                        <a href="#">
                                            <figure class="avatar avatar-sm mr-3 bring-forward">
                                                <img
                                                    src="../../assets/media/image/user/man_avatar2.jpg"
                                                    class="rounded-circle" alt="image/">
                                            </figure>
                                        </a>
                                    </div>
                                    <div>
                                        <h6 class="d-flex justify-content-between mb-4">
                                            <span>
                                                <a href="#">Martina Ash</a> shared a link
                                            </span>
                                            <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                        </h6>
                                        <a href="#">
                                            <div class="mb-3">
                                                <div class="row no-gutters border card flex-row border-radius-1">
                                                    <div class="col-xl-3 col-lg-12">
                                                        <img
                                                            src="../../assets/media/image/photo1.jpg"
                                                            class="w-100"
                                                            alt="image/">
                                                    </div>
                                                    <div class="col-xl-9 col-lg-12 p-3">
                                                        <h5 class="line-height-16">Connection title</h5>
                                                        Lorem ipsum dolor sit amet, consectetur adipisicing
                                                        elit. Facilis, voluptatibus.
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div>
                                        <figure class="avatar avatar-sm mr-3 bring-forward">
                                                        <span
                                                            class="avatar-title bg-success-bright text-success rounded-circle">C</span>
                                        </figure>
                                    </div>
                                    <div>
                                        <h6 class="d-flex justify-content-between mb-4">
                                            <span>
                                                <a href="#">Jonny Richie</a> shared a post
                                            </span>
                                            <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                        </h6>
                                        <a href="#">
                                            <div class="mb-3 border p-3 border-radius-1">
                                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab
                                                aliquid aperiam commodi culpa debitis deserunt enim itaque
                                                laborum minima neque nostrum pariatur perspiciatis, placeat
                                                quidem, ratione recusandae reiciendis sapiente, ut veritatis
                                                vitae. Beatae dolore hic odio! Esse officiis quidem
                                                voluptate.
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div>
                                        <figure class="avatar avatar-sm mr-3 bring-forward">
                                                        <span
                                                            class="avatar-title bg-info-bright text-info rounded-circle">K</span>
                                        </figure>
                                    </div>
                                    <div>
                                        <h6 class="d-flex justify-content-between mb-4">
                                            <span>
                                                <a href="#">Jonny Richie</a> attached file
                                            </span>
                                            <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                        </h6>
                                        <a href="#">
                                            <div class="mb-3 border p-3 border-radius-1">
                                                <i class="fa fa-file-pdf-o mr-2"></i> filename12334.pdf
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div>
                                        <figure class="avatar avatar-sm mr-3 bring-forward">
                                                    <span class="avatar-title bg-warning-bright text-warning rounded-circle">
                                                        <i class="ti-image/"></i>
                                                    </span>
                                        </figure>
                                    </div>
                                    <div>
                                        <h6 class="d-flex justify-content-between mb-4">
                                            <span>
                                                <a href="#">Jonny Richie</a> shared files
                                            </span>
                                            <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                        </h6>
                                        <div class="row row-xs">
                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                <figure>
                                                    <img
                                                        src="../../assets/media/image/portfolio-five.jpg"
                                                        class="w-100 border-radius-1" alt="image/">
                                                </figure>
                                            </div>
                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                <figure>
                                                    <img
                                                        src="../../assets/media/image/portfolio-one.jpg"
                                                        class="w-100 border-radius-1" alt="image/">
                                                </figure>
                                            </div>
                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                <figure>
                                                    <img
                                                        src="../../assets/media/image/portfolio-three.jpg"
                                                        class="w-100 border-radius-1" alt="image/">
                                                </figure>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div>
                                        <figure class="avatar avatar-sm mr-3 bring-forward">
                                            <span class="avatar-title bg-youtube rounded-circle">Y</span>
                                        </figure>
                                    </div>
                                    <div>
                                        <h6 class="d-flex justify-content-between">
                                           <span>
                                               <a href="#">Jonny Richie</a> shared video
                                           </span>
                                            <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                        </h6>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto,
                                            placeat.</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div
                                                    class="embed-responsive embed-responsive-16by9 m-t-b-5">
                                                    <iframe class="embed-responsive-item"
                                                            src="https://www.youtube.com/embed/l-epKcOA7RQ"
                                                            allowfullscreen></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="connections" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Connections</h6>
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <figure class="avatar mr-3">
                                                        <img
                                                            src="../../assets/media/image/user/women_avatar2.jpg"
                                                            class="rounded-circle" alt="...">
                                                    </figure>
                                                </div>
                                                <div>
                                                    <p class="mb-0">Rosemary Krout</p>
                                                    <p class="small text-muted mb-0 line-height-15">
                                                        Team Leader
                                                    </p>
                                                </div>
                                                <a href="#" class="ml-auto" title="Message"
                                                   data-toggle="tooltip">
                                                    <i class="fa fa-comment-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <figure class="avatar mr-3">
                                                        <img
                                                            src="../../assets/media/image/user/women_avatar1.jpg"
                                                            class="rounded-circle" alt="...">
                                                    </figure>
                                                </div>
                                                <div>
                                                    <p class="mb-0">Miller Edwins</p>
                                                    <p class="small text-muted mb-0 line-height-15">
                                                        Team Leader
                                                    </p>
                                                </div>
                                                <a href="#" class="ml-auto" title="Message"
                                                   data-toggle="tooltip">
                                                    <i class="fa fa-comment-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <figure class="avatar mr-3">
                                                        <img
                                                            src="../../assets/media/image/user/women_avatar3.jpg"
                                                            class="rounded-circle" alt="...">
                                                    </figure>
                                                </div>
                                                <div>
                                                    <p class="mb-0">Cahra Smiz</p>
                                                    <p class="small text-muted mb-0 line-height-15">
                                                        Agent
                                                    </p>
                                                </div>
                                                <a href="#" class="ml-auto" title="Message"
                                                   data-toggle="tooltip">
                                                    <i class="fa fa-comment-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <figure class="avatar mr-3">
                                                        <img
                                                            src="../../assets/media/image/user/women_avatar4.jpg"
                                                            class="rounded-circle" alt="...">
                                                    </figure>
                                                </div>
                                                <div>
                                                    <p class="mb-0">Burgess Attwool</p>
                                                    <p class="small text-muted mb-0 line-height-15">
                                                        Contact
                                                    </p>
                                                </div>
                                                <a href="#" class="ml-auto" title="Message"
                                                   data-toggle="tooltip">
                                                    <i class="fa fa-comment-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <figure class="avatar mr-3">
                                                        <img
                                                            src="../../assets/media/image/user/man_avatar2.jpg"
                                                            class="rounded-circle" alt="...">
                                                    </figure>
                                                </div>
                                                <div>
                                                    <p class="mb-0">Allx Life</p>
                                                    <p class="small text-muted mb-0 line-height-15">
                                                        Agent
                                                    </p>
                                                </div>
                                                <a href="#" class="ml-auto" title="Message"
                                                   data-toggle="tooltip">
                                                    <i class="fa fa-comment-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <figure class="avatar mr-3">
                                                        <img
                                                            src="../../assets/media/image/user/man_avatar5.jpg"
                                                            class="rounded-circle" alt="...">
                                                    </figure>
                                                </div>
                                                <div>
                                                    <p class="mb-0">Marti Sorbey</p>
                                                    <p class="small text-muted mb-0 line-height-15">
                                                        Contact
                                                    </p>
                                                </div>
                                                <a href="#" class="ml-auto" title="Message"
                                                   data-toggle="tooltip">
                                                    <i class="fa fa-comment-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="earnings" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Earnings</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Item Sales Count</th>
                                        <th>Earnings</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>monday, 12</td>
                                        <td>
                                            3
                                        </td>
                                        <td>$400</td>
                                    </tr>
                                    <tr>
                                        <td>tuesday, 13</td>
                                        <td>
                                            2
                                        </td>
                                        <td>$400</td>
                                    </tr>
                                    <tr>
                                        <td>wednesday, 14</td>
                                        <td>
                                            3
                                        </td>
                                        <td>$420</td>
                                    </tr>
                                    <tr>
                                        <td>thursday, 15</td>
                                        <td>
                                            5
                                        </td>
                                        <td>$500</td>
                                    </tr>
                                    <tr>
                                        <td>friday, 15</td>
                                        <td>
                                            3
                                        </td>
                                        <td>$400</td>
                                    </tr>
                                    <tr>
                                        <td>saturday, 16</td>
                                        <td>
                                            3
                                        </td>
                                        <td>$400</td>
                                    </tr>
                                    <tr>
                                        <td>sunday, 17</td>
                                        <td>
                                            3
                                        </td>
                                        <td>$400</td>
                                    </tr>
                                    <tr>
                                        <td>monday, 18</td>
                                        <td>
                                            3
                                        </td>
                                        <td>$500</td>
                                    </tr>
                                    <tr>
                                        <td>tuesday, 19</td>
                                        <td>
                                            3
                                        </td>
                                        <td>$400</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>28</th>
                                        <th>$3720</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-8 col-md-12" style="overflow-x:scroll">
            <h4 class="lead">Transactions</h4>
            @if($user->role == 'customer')
                <table id="myTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Merchant Email</th>
                            <th>Code</th>
                            <th>Payment Method</th>
                            <th>Transaction Type</th>
                            <th>Status</th>
                            <th>Charge Amount</th>
                            <th>Charge From</th>
                            <th>Sub-Total Amount</th>
                            <th>Total Amount</th>
                            <th>Reference</th>
                            <th>vTerminal Charge</th>
                            <th>Merchant Charge</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->customerCode as $code)
                        <tr>
                            <td>{{ $code->merchant->email }}</td>
                            <td>{{ $code->code }}</td>
                            <td>{{ $code->payment_method }}</td>
                            <td>{{ $code->transaction_type }}</td>
                            <td>{{ $code->status }}</td>
                            <td>{{ $code->charge_amount }}</td>
                            <td>{{ $code->charge_from }}</td>
                            <td>{{ $code->subtotal_amount }}</td>
                            <td>{{ $code->total_amount }}</td>
                            <td>{{ $code->reference }}</td>
                            <td>{{ $code->vterminal_charge }}</td>
                            <td>{{ $code->merchant_charge }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <table id="myTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Customer Email</th>
                            <th>Code</th>
                            <th>Payment Method</th>
                            <th>Transaction Type</th>
                            <th>Status</th>
                            <th>Charge Amount</th>
                            <th>Charge From</th>
                            <th>Sub-Total Amount</th>
                            <th>Total Amount</th>
                            <th>Reference</th>
                            <th>vTerminal Charge</th>
                            <th>Merchant Charge</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->merchantCode as $code)
                        <tr>
                            <td>{{ $code->customer->email }}</td>
                            <td>{{ $code->code }}</td>
                            <td>{{ $code->payment_method }}</td>
                            <td>{{ $code->transaction_type }}</td>
                            <td>{{ $code->status }}</td>
                            <td>{{ $code->charge_amount }}</td>
                            <td>{{ $code->charge_from }}</td>
                            <td>{{ $code->subtotal_amount }}</td>
                            <td>{{ $code->total_amount }}</td>
                            <td>{{ $code->reference }}</td>
                            <td>{{ $code->vterminal_charge }}</td>
                            <td>{{ $code->merchant_charge }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            <!-- <div class="card">
                <div class="card-body">
                    <h6 class="card-title d-flex justify-content-between align-items-center">
                        Followers
                        <a href="#" class="small">View All</a>
                    </h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center px-0">
                            <figure class="avatar avatar-state-success mr-3">
                                <img src="../../assets/media/image/user/women_avatar5.jpg" class="rounded-circle" alt="image">
                            </figure>
                            <div>
                                <h6 class="mb-0">Valentine Maton</h6>
                                <small class="text-muted">Engineer</small>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center px-0">
                            <figure class="avatar avatar-state-success mr-3">
                                <img src="../../assets/media/image/user/man_avatar1.jpg" class="rounded-circle" alt="image">
                            </figure>
                            <div>
                                <h6 class="mb-0">Holmes Cherryman</h6>
                                <small class="text-muted">Human resources</small>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center px-0">
                            <figure class="avatar avatar-state-success mr-3">
                                <img src="../../assets/media/image/user/women_avatar1.jpg" class="rounded-circle" alt="image">
                            </figure>
                            <div>
                                <h6 class="mb-0">Malanie Hanvey</h6>
                                <small class="text-muted">Real estate agent</small>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center px-0">
                            <figure class="avatar avatar-state-success mr-3">
                                <img src="../../assets/media/image/user/women_avatar2.jpg" class="rounded-circle" alt="image">
                            </figure>
                            <div>
                                <h6 class="mb-0">Kenneth Hune</h6>
                                <small class="text-muted">Engineer</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title d-flex justify-content-between align-items-center">
                        Mutual Friends
                        <a href="#" class="small">View All</a>
                    </h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar-group mr-2">
                            <a href="#" class="avatar" data-toggle="tooltip" title="Valentine Maton">
                                <img src="../../assets/media/image/user/women_avatar1.jpg" class="rounded-circle" alt="image">
                            </a>
                            <a href="#" class="avatar" data-toggle="tooltip" title="Holmes Cherryman">
                                <img src="../../assets/media/image/user/man_avatar1.jpg" class="rounded-circle" alt="image">
                            </a>
                            <a href="#" class="avatar" data-toggle="tooltip" title="Malanie Hanvey">
                                <img src="../../assets/media/image/user/man_avatar2.jpg" class="rounded-circle" alt="image">
                            </a>
                            <a href="#" class="avatar" data-toggle="tooltip" title="Kenneth Hune">
                                <img src="../../assets/media/image/user/women_avatar2.jpg" class="rounded-circle" alt="image">
                            </a>
                            <a href="#" class="avatar" data-toggle="tooltip" title="Kenneth Hune">
                                <img src="../../assets/media/image/user/women_avatar3.jpg" class="rounded-circle" alt="image">
                            </a>
                        </div>
                        <span>+5 friends</span>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
@endsection
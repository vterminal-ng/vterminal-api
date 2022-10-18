@extends('layout')

@section('content')
    <div class="page-header d-md-flex justify-content-between">
        <div>
            <h3>Users</h3>
            <nav aria-label="breadcrumb" class="d-flex align-items-start">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Merchants</li>
                </ol>
            </nav>
        </div>
        <div class="mt-2 mt-md-0">
            <div class="dropdown">
                <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <i class="ti-filter mr-2"></i> Filter
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url()->current() }}?status=active">Active</a>
                    <a class="dropdown-item" href="{{ url()->current() }}?status=inactive">Inactive</a>
                    <a class="dropdown-item" href="{{ url()->current() }}?status=dormant">Dormant</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="user-list" class="table table-lg">
                            <thead>
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="user-list-select-all">
                                        <label class="custom-control-label" for="user-list-select-all"></label>
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td></td>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <a href="#">
                                        <figure class="avatar avatar-sm mr-2">
                                            <img src="../../assets/media/image/user/man_avatar3.jpg"
                                                 class="rounded-circle" alt="avatar">
                                        </figure>
                                        {{ $user->userDetail->first_name }} {{ $user->userDetail->last_name }}
                                    </a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_number }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>
                                    <span class="badge bg-warning-bright text-warning">{{ ($user->is_active) ? 'Active' : 'Blocked' }}</span>
                                </td>
                                <td>{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</td>
                                <td>{{ $user->updated_at ? $user->updated_at->diffForHumans() : '' }}</td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown"
                                           class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="ti-more-alt"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('admin.userdetails', $user->id) }}" class="dropdown-item">View Profile</a>
                                            <a href="{{ route('admin.userstatus', $user) }}" class="dropdown-item">{{ ($user->is_active) ? 'Block User' : 'Unblock User' }}</a>
                                            <a href="#" class="dropdown-item text-danger">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
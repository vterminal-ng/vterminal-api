@extends('layout')


@section('content')

<div class="row">
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <img src="{{url('awesome.jpeg')}}" alt="no view"> 
            <div class="col-3">
            </div>
            User id No  {{$user->id}}
          </div>
        </div>
      </div>
    </div>
@endsection
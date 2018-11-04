@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
    <h2>User Information:</h2>
    <div class="alert alert-info" role="alert">
        <h3 class="alert-heading">Welcome <span style="color:black;">{{$displayName}}</span>!</h3>
        
        <p>We've generated a unique hash for you..</p>
        <a href="" class="alert-link">{{$hash}}</a>
        <p class="mb-0">This hash will be used to identify you on the network. Your display name is used to help friends find your account!</p>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <ul class="list-group" style="font-size:1.2em;">

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Hash
                    <span class="badge badge-primary badge-pill">{{ $hash }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Collection Size
                    <span class="badge badge-primary badge-pill"> {{ $collectionSize }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Collection Reach
                    <span class="badge badge-primary badge-pill">{{ $collectionSpread }} items of yours in other User's collection</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

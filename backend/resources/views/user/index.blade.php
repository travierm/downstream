@extends('layouts.master')

@section('fixed-footer', 'fixed-bottom')

@section('content')
<div class="container pushFromTop">
    <h2>User Information:</h2>
    <div class="alert alert-success" role="alert">
        <h3 class="alert-heading">Welcome <span style="color:black;">{{$displayName}}</span>!</h3>
        
        <p>We've generated a unique hash for you..</p>
        <span class="alert-link" style=" word-wrap: break-word;">{{ $hash }}</span>
        <p class="mb-0">This hash will be used to identify you on the network. Your display name is used to help friends find your account!</p>
    </div>

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <ul class="list-group mb-2">

                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Collection Size</h6>
                        <small class="text-muted">Number of active users.</small>
                    </div>

                    <span class="text-success">{{ $collectionSize }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Collection Reach</h6>
                        <small class="text-muted">Number of your items found in other User's collection.</small>
                    </div>

                    <span class="text-primary">{{ $collectionSpread }}</span>
                </li>

                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Account Creation</h6>
                        <small class="text-muted">Time since you create your account.</small>
                    </div>

                    <span>{{ @$sinceAccountCreation }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

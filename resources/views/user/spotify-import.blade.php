@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
    <div class="row">
        <h2>Spotify Import Setup</h2>
    </div>


    <div class="row mt-3">
        <div class="col-lg-3">
            @if(@$success)
            <div class="alert alert-success" role="alert">
                Your Spotify Account is connected with Downstream!
            </div>
            @endif

            <spotify-authorize-btn @if(@$authorized) authorized="true" @endif></spotify-authorize-btn>
        </div>
    </div>
</div>
@endsection

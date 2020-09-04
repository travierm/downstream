@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
    <div class="row">
        <div class="col-lg-12">
            <h2>Spotify Import Setup</h2>
            <p>Import your favorite tracks from Spotify directly into Downstream</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            @if(@$success)
            <div class="alert alert-success" role="alert">
                Your Spotify Account is connected with Downstream!
            </div>
            @else
            <div class="alert alert-danger" role="alert">
                Your Spotify Account is not connected with Downstream.
            </div>
            @endif

            <spotify-authorize-btn @if(@$success) authorized="true" @endif></spotify-authorize-btn>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-12">
            <h5>How it works:</h5>
            <ul class="list">
                <li class="list-item">Connect your Spotify Account using the button above</li>
                <li class="list-item">We will create a playlist called "DS Import"</li>
                <li class="list-item">Any track added will sync to your Downstream collection automatically</li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table></table>
        </div>
    </div>
</div>
@endsection

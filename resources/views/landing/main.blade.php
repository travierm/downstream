@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <section class="jumbotron text-center mb-4">
    <div class="row justify-content-md-center">
      <div class="col-lg-12">
        <h1 class="jumbotron-heading" style="text-align: center;" >Collect <img src="/open-iconic-master/svg/chevron-right.svg"> Discover <img src="/open-iconic-master/svg/chevron-right.svg"> Play</h1>
        <p style="text-align: center; font-size: 1.2rem;">A music service designed to help you collect and discover whatever type of music you enjoy listening to.</p>
      </div>
    </div>
  </section>

  @if(Auth::guest())
  <div class="row justify-content-md-center">
    <div class="col-lg-3 col-sm-6 mb-3">
      <a href="/register" class="btn btn-outline-success btn-lg btn-block">Join Today</a>
    </div>
  </div>
  @endif

  @if($items)
    <div class="row">
    @foreach($items as $item)
      <div class="col-md-4">
        <youtube-card 
          video-id="{{ $item->index }}"
          thumbnail="{{ $item->meta->thumbnail }}"
          title="{{ $item->meta->title }}"
          collected="{{ $item->collected }}">
        </youtube-card>
      </div>
    @endforeach
    </div>
  @endif

  <div class="row">
    <div class="col-md-4">
      <h2 class="mt-3 mb-3"><i class="fas fa-box mr-2"></i>Features</h2>
    </div>
  </div>

  <div class="row">

    <div class="col-md-4">
      <h2>Unlimited Music</h2>
      <p>Using our search tool you'll have access to nearly every musical piece on the internet.</p>
    </div>

    <div class="col-md-4">
      <h2>Collection</h2>
      <p>Collect any items you find while using search or discover pages. Will save them for replay and make sure you never lose an item even if the original video is removed.</p>
    </div>

    <div class="col-md-4">
      <h2>Discovery</h2>
      <p>There are two great ways to discover: <br /> First we make it easy to see what other users are collecting. Secondly will start finding related music to what you've collected and begin populating your Discover page.</p>
    </div>
  </div>

  <div class="row justify-content-md-center">
    <div class="col-md-4 mt-3">
      <h2>Sharing</h2>
      <p>You'll be able to share your favorite music with friends easily using our direct linking tool.</p>
    </div>
  </div>
</div>

@if(Auth::guest())
  <div class="row justify-content-md-center">
    <div class="col-lg-3 col-sm-6 mb-3 mt-3">
      <a href="/register" class="btn btn-outline-success btn-lg btn-block">Join Today</a>
    </div>
  </div>
  @endif

<!-- <control-bar></control-bar> -->
@endsection

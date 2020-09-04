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
      <a href="/register" class="btn btn-success btn-lg btn-block">Register</a>
    </div>

    <div class="col-lg-3 col-sm-6 mb-3">
      <a href="/demo" class="btn btn-primary btn-lg btn-block">Try Demo</a>
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
          :collected="{!!  json_encode($item->collected) !!}">
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
      <p>You'll have access to literally billions of songs. We use YouTube to power our content delivery and this lets you build the best collection possible.</p>
    </div>

    <div class="col-md-4">
      <h2>Collection</h2>
      <p>Collect any items you find on Downstream!</p>
      <p>We'll keep your collection data safe even if source videos are removed. Our automated systems will fix any broken links quickly.</p>
    </div>

    <div class="col-md-4">
      <h2>Discovery</h2>
      <p>We're building a top-notch discovery service based on items you collect. You can also follow your friends account and see what they collect!</p>
    </div>
  </div>
</div>

@if(Auth::guest())
  <div class="row justify-content-md-center">
    <div class="col-lg-3 col-sm-6 mb-3 mt-3">
      <a href="/register" class="btn btn-outline-success btn-lg btn-block">Register</a>
    </div>
  </div>
  @endif

<!-- <control-bar></control-bar> -->
@endsection

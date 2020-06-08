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
  </div>
  @endif

  @if($items)
    <div class="row">
    @foreach($items as $item)

      <div class="col-md-4">
        <youtube-card 
          @if($loop->iteration == 1)
          :should-play="true"
          @endif
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
      <h2><i class="fas fa-compact-disc"></i> Collect anything</h2>
      <p>Collect any items you search or discover!</p>
      <p>Any item collected on Downstream is kept forever. You never have to worry about losing music again!</p>
    </div>

    <div class="col-md-4">
      <h2><i class="fas fa-search"></i> Discover</h2>
      <p>Discover new music from related items or from music your friends are collecting!</p>
      <p>We're building a top notch Discovery service. Finding new music will never be a probem again!</p>
    </div>

    <div class="col-md-4">
      <h2><i class="far fa-play-circle"></i> Play </h2>
      <p>We use YouTube to power our media player. <br /> <br /> This lets you search and play millions of songs at anytime!</p>
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

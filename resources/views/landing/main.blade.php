@extends('layouts.master')
@section('content')
<div class="container pushFromTop">
  <div class="row justify-content-md-center">
  	<div class="col-lg-12">
    	<h1 style="text-align: center;" >Discover <img src="/open-iconic-master/svg/chevron-right.svg"> Collect <img src="/open-iconic-master/svg/chevron-right.svg"> Play</h1>
    	<p style="text-align: center; font-size: 1.2rem;">A unique music service designed to help you discover and collect whatever type of music you enjoy listening to.</p>
	  </div>
  </div>

  @if(Auth::guest())
  <div class="row justify-content-md-center">
    <div class="col-lg-3 col-sm-6">
      <a href="/register" class="btn btn-outline-info btn-lg btn-block">Register</a>
    </div>
  </div>
  @endif

  @if(count($toplist) >= 2)
    <div class="row">
    @foreach($toplist as $item)
      <div class="col-sm-12 col-md-6 col-lg-6">
        <video-player-card collected="{{ $item->didCollect }}" spotify-id="{{ $item->source_id }}" vid="{{ $item->index }}" title="{{ $item->title }}" thumbnail="{{ $item->thumbnail }}"></video-player-card>
      </div>
    @endforeach
    </div>
  @else
    <div class="row" style="margin-top: 15px;">
    	@foreach($videos as $media)
    	<div class="col-sm-12 col-md-6 col-lg-6">
        <video-player-card :media="{{ $media }}"></video-player-card>
      </div>
      @endforeach
    </div>
  @endif
</div>

<master-bar></master-bar>
@endsection

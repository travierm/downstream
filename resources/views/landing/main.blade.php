@extends('layouts.master')
@section('content')
<div class="container pushFromTop">
  <div class="row justify-content-md-center">
  	<div class="col-lg-12">
    	<h1 style="text-align: center;" >Discover <img src="/open-iconic-master/svg/chevron-right.svg"> Collect <img src="/open-iconic-master/svg/chevron-right.svg"> Play</h1>
    	<p style="text-align: center; font-size: 1.2rem;">A unique music service designed to help you discover and collect what ever type of music you enjoy listening to.</p>
	</div>
  </div>

  <div class="row">
  	@foreach($videos as $media)
  	<div class="col-lg-4">
      <video-player-card :media="{{ $media }}"></video-player-card>
    </div>
    @endforeach
  </div>

  <div class="row justify-content-md-center">
  	<div class="col-lg-3">
  		<a href="/register" class="btn btn-outline-info btn-lg btn-block">Register</a>
  	</div>
  	<div class="col-lg-3">
  		<a href="/register" class="btn btn-outline-info btn-lg btn-block">Login</a>
  	</div>
  </div>

  <div class="row justify-content-md-center pushFromTop">
  	<div class="col-lg-8">
  		<about-page></about-page>
  	</div>
  </div>
</div>
@endsection

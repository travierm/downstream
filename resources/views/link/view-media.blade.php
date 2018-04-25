@extends('layouts.master')

@section('content')
<div class="container-fluid pushFromTop ">

  <div class="row justify-content-md-center" style="margin-top: 10px;">
    <div class="Col">
      <button onclick="share('{{ $media->index }}')" class="btn btn-outline-success">Copy Share Link</button>
    </div>
  </div>

  <div class="row justify-content-md-center">
  	@if($media)

  	@section('meta')
      @if(env("FB_APP_ID"))
        <meta property="fb:app_id"  content="{{ env('FB_APP_ID') }}" />
      @endif
    	<meta property="og:url"   content="{{env('APP_LINK_URL') . $media->index}}" />
  	  <meta property="og:type"  content="music.song" />
  	  <meta property="og:title" content="{{$media->meta->title }}" />
  	  <meta property="og:image" content="{{$media->meta->thumbnail }}" />
      <meta property="og:description" content="Listen now on Downstream!" />
	  @endsection
    <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12" style="margin-top: 15px;">
      <video-player-card autoplay="true" :media="{{ $media }}"></video-player-card>
    </div>
    @else
    <div class="alert alert-danger col-lg-6" role="alert">
  		Media item could not be found!
	  </div>
    @endif
  </div>


  @if(@count($recommendations) >= 1)
  <div class="row">
    <div class="col-lg-4">
      <h3>Recommendations:</h3>
    </div>
  </div>
  <div class="row">
    @foreach($recommendations as $item)
      <div class="col-lg-3 col-md-6 col-sm-12">
        <video-player-card spotify-id="{{ $item->source_id }}" vid="{{ $item->index }}" title="{{ $item->title }}" thumbnail="{{ $item->thumbnail }}"></video-player-card>
      </div>
    @endforeach
  </div>
  @else
  <div class="row">
    <div class="col-lg-4">
      <h3>Processing recommendations soon...</h3>
    </div>
  </div>
  @endif
</div>

<master-bar></master-bar>
@endsection

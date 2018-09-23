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
      <youtube-card 
          spotify-id="{{ $media->source_id }}" 
          video-id="{{ $media->index }}" 
          title="{{ $media->title }}" 
          thumbnail="{{ $media->meta->thumbnail }}">
        </youtube-card>
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
        <youtube-card 
          spotify-id="{{ $item->source_id }}" 
          video-id="{{ $item->index }}" 
          title="{{ $item->title }}" 
          thumbnail="{{ $item->thumbnail }}">
        </youtube-card>
      </div>
    @endforeach
  </div>
</div>
@endif

<control-bar></control-bar>
@endsection

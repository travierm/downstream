@extends('layouts.master')

@section('content')
<div class="container-fluid pushFromTop ">
  <div class="row justify-content-md-center">
  	@if($media)
  	<meta property="og:url"                content="https://down-stream.org/media/{{$media->index}}" />
	<meta property="og:type"               content="music.song" />
	<meta property="og:title"              content="{{$media->meta->title }}" />
	<meta property="og:image"              content="{{$media->meta->thumbnail }}" />

    <div class="col-lg-4 col-md-12 col-sm-12">
      <video-player-card autoplay="true" :media="{{ $media }}"></video-player-card>
    </div>
    @else
    <div class="alert alert-danger col-lg-6" role="alert">
  		Media item could not be found!
	</div>
    @endif
  </div>
</div>
@endsection

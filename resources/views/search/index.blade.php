@extends('layouts.master')

@section('content')
<div class="container-fluid pushFromTop">

  <div class="row mt-2">
    <div class="col-lg-6">
      <img style="height: 2em;" src="http://downstream.us/images/yt_logo_rgb_light.png" />
      <p class="mt-2">All videos are sourced from YouTube. You can read their Term of Service <a href="https://www.youtube.com/t/terms">here</a></p>
    </div>
  </div>

  <div class="row">
    @if(@$videos)
      @foreach($videos as $video)
      <div class="col-lg-3 col-md-12 col-sm-12">
        <youtube-card 
          @if($video->id) :media-id="{{$video->id}}" @endif 
          title="{{$video->title}}"
          @if($video->collected) :collected="{{$video->collected}}" @endif
          video-id={{$video->vid}} 
          thumbnail={{$video->thumbnail}}
          >
        </youtube-player-card>
      </div>
      @endforeach
    @endif
  </div>

  <control-bar></control-bar>
</div>
@endsection

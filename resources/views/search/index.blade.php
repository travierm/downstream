@extends('layouts.master')

@section('content')
<div class="container-fluid pushFromTop">
  <div class="row">
    <div class="col-lg-4 col-md-12 col-sm-12">
      <img height="125" width="125" class="img-fluid" src="{{ asset('images/yt_logo_rgb_light.png')}}"></img>
      <form class="mt-2" action="/search" method="GET">
        <div class="input-group mb-1">
          <input name="query" value="{{@$query}}" type="text" class="form-control" placeholder="Search..." aria-label="Search">
          <div class="input-group-append">
            <input class="btn btn-outline-danger" type="submit" value="Search" />
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    @if(@$videos)
      @foreach($videos as $video)
      <div class="col-lg-3 col-md-12 col-sm-12 pushFromTop">
        <video-player-card @if($video->id) media-id="{{$video->id}}" @endif title="{{$video->title}}" collected="{{$video->collected}}" vid={{$video->vid}} thumbnail={{$video->thumbnail}} :ignore-bad-thumbnail="true" ></video-player-card>
      </div>
      @endforeach
    @endif
  </div>
</div>
@endsection

@extends('layouts.master')

@section('content')
<div class="container-fluid pushFromTop">
  <div class="row">
    <div class="col-12">
      <img height="125" width="125" class="img-fluid" src="{{ asset('images/yt_logo_rgb_light.png')}}"></img>
      <form style="margin-top: 10px;" class="form-inline" action="/search" method="POST">
        {{ csrf_field() }}

        <input placeholder="Search..." style="width:30%;" class="form-control" type="text" name="query" value="{{@$query}}" />

        <button class="btn btn-success">Search</button>
      </form>
    </div>
  </div>

  <div class="row">
    @if(@$videos)
      @foreach($videos as $video)
      <div class="col-lg-3 pushFromTop">
        <youtube-player-card @if($video->id) media-id="{{$video->id}}" @endif collected="{{$video->collected}}" vid={{$video->vid}} ></youtube-player-card>
      </div>
      @endforeach
    @endif
  </div>
</div>
@endsection

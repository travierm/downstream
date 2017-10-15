@extends('layouts.master')
@section('content')
<div class="container-fluid pushFromTop">
  <div class="row">
    @if(@$videos)
      @foreach($videos as $video)
      <div class="col-lg-3">
        <youtube-player-card collected="{{$video->wasLiked}}" id="{{$video->id}}" title="{{$video->title}}"  vid="{{$video->vid}}"></youtube-player-card>
      </div>
      @endforeach
    @endif
  </div>
</div>
@endsection

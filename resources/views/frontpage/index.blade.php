@extends('layouts.master')
@section('content')
<div class="container-fluid pushFromTop">
  @if(@$videos)
  <div class="row">
      @foreach($videos as $video)
      <div class="col-lg-3">
        <youtube-player-card media-id="{{$video->id}}" collected="{{$video->collected}}" title="{{$video->title}}"  vid="{{$video->index}}"></youtube-player-card>
      </div>
      @endforeach
  </div>
  <div class="row">
    <master-bar></master-bar>
  </div>
  @else
  <div class="row">
    <div class="col-lg-6 center" v-if="videos.length == 0">
      <h3>Frontpage is empty rn..</h3>
      <img height="250" width="450" src="https://media.giphy.com/media/hICCrVIACYZY4/giphy.gif" />
      <h4>Search & Collect to populate this page for others!</h4>
    </div>
  </div>
  @endif
</div>
@endsection

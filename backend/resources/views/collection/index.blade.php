@extends('layouts.master')
@section('content')
<div class="container-fluid pushFromTop">
  <div class="row">
    @if(@$videos)
      @foreach($videos as $video)
      <div class="col-lg-3 col-md-6 col-sm-12">
        <youtube-player-card id="{{$video->id}}" collected="{{$video->collected}}" title="{{$video->title}}"  vid="{{$video->vid}}"></youtube-player-card>
      </div>
      @endforeach
    @endif
  </div>
</div>
@endsection

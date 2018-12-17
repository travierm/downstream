@extends('layouts.master')

@section('content')
<div class="container-fluid pushFromTop">
  <div class="row">
    @if(@$videos)
      @foreach($videos as $video)
      <div class="col-lg-3 col-md-12 col-sm-12 pushFromTop">
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

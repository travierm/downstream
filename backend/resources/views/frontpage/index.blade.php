@extends('layouts.master')
@section('content')
<div class="container-fluid pushFromTop">
  @if(@$rows)
  @foreach($rows as $row)
  <h3>{{ $row['title'] }}</h3>
  <div class="row">
    @foreach($row['media'] as $media)
    <div class="col-lg-3 col-md-6 col-sm-12">
      <youtube-card 
        spotify-id="{{ $media->source_id }}" 
        video-id="{{ $media->index }}" 
        title="{{ $media->meta->title }}" 
        thumbnail="{{ $media->meta->thumbnail }}"
        @if($media->collected) :collected="{{ $media->collected }}" @endif
        >
      </youtube-card>
    </div>
    @endforeach
  </div>
  @endforeach
  @else
  <div class="row">
    <div class="col-lg-6 center" v-if="videos.length == 0">
      <h3>Frontpage is empty rn..</h3>
      <img height="250" width="450" src="https://media.giphy.com/media/hICCrVIACYZY4/giphy.gif" />
      <h4>Search & Collect to populate this page for others!</h4>
    </div>
  </div> 
  @endif

  <control-bar></control-bar>
</div>
@endsection

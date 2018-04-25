@extends('layouts.master')
@section('content')
<div class="container-fluid pushFromTop">
  <div class="row">
    <h2>Recommendation Engine Raw Feed:</h2>
  </div>

  <div class="row">
  	@foreach($items as $item)
      <div class="col-lg-3 col-md-6 col-sm-12">

        <video-player-card 
          spotifyId="{{ $item->source_id }}" 
          reference="{{ $item->media_vid}}" 
          vid="{{ $item->index }}" 
          title="{{ $item->title }}" 
          thumbnail="{{ $item->thumbnail }}">
        </video-player-card>

      </div>
    @endforeach
  </div>
</div>
@endsection

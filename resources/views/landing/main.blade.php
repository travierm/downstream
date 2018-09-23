@extends('layouts.master')
@section('content')
<div class="container pushFromTop">
  <section class="jumbotron text-center mb-4">
    <div class="row justify-content-md-center">
      <div class="col-lg-12">
        <h1 class="jumbotron-heading" style="text-align: center;" >Discover <img src="/open-iconic-master/svg/chevron-right.svg"> Collect <img src="/open-iconic-master/svg/chevron-right.svg"> Play</h1>
        <p style="text-align: center; font-size: 1.2rem;">A unique music service designed to help you discover and collect whatever type of music you enjoy listening to.</p>
      </div>
    </div>
  </section>

  @if(Auth::guest())
  <div class="row justify-content-md-center">
    <div class="col-lg-3 col-sm-6 mb-3">
      <a href="/register" class="btn btn-outline-info btn-lg btn-block">Register</a>
    </div>
  </div>
  @endif

  @if(count($toplist) >= 2)
    <div class="row">
    @foreach($toplist as $item)
      <div class="col-md-4">
        <youtube-card 
          video-id="{{ $item->index }}"
          :media-id="{{ $item->id }}"
          thumbnail="{{ $item->thumbnail }}"
          title="{{ $item->title }}"
          >
        </youtube-card>
      </div>
    @endforeach
    </div>
  @elseif(count($videos) >= 2)
    <div class="row" style="margin-top: 15px;">
    	@foreach($videos as $media)
    	<div class="col-md-4">
        <youtube-card 
          :videoId="item->index"
          :title="item->meta->title"
          :thumbnail="item->meta->thumbnail"
          :collected="item->collected"
        >
        </youtube-card>
      </div>
      @endforeach
    </div>
  @endif
</div>

<control-bar></control-bar>
@endsection

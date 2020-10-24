@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <section class="jumbotron text-center mb-4">
    <div class="row justify-content-md-center">
      <div class="col-lg-12 mt-2">
        <h1 class="jumbotron-heading" style="text-align: center;" >
          <i class="fas fa-compact-disc"></i><span class="ml-2">Collect</span>
          <i class="ml-3 fas fa-search"></i><span class="ml-2">Discover</span>
          <i class="ml-3 fas fa-play"></i><span class="ml-2">Play</span>
        </h1>
        <p class="pt-2" style="text-align: center; font-size: 1.2rem;">A music service designed to help you collect and discover whatever type of music you enjoy listening to.</p>
      </div>
    </div>
  </section>

  @if(Auth::guest())
  <div class="row justify-content-md-center">
    <div class="col-lg-2 col-sm-6 mb-4">
      <a href="/register" class="btn btn-primary btn-block">Register</a>
    </div>
  </div>
  @endif

  @if($items)
    <div class="row">
      @foreach($items as $item)
      <div class="col-md-4">
        <youtube-card 
          video-id="{{ $item->index }}"
          thumbnail="{{ $item->meta->thumbnail }}"
          title="{{ $item->meta->title }}"
          :collected="{!!  json_encode($item->collected) !!}">
        </youtube-card>
      </div>
      @endforeach
    </div>
  @endif
</div>

@endsection

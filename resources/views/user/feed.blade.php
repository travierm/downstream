@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="row">
      <div class="col-lg-12">
          <h2>Your Follower Feed</h2>
      </div>
  </div>

  @if($items)
    <div class="row">
    @foreach($items as $item)
      <div class="col-md-4">
        <youtube-card 
          display-name="{{ $item->following_name}}"
          created-at="{{ $item->days_since }}"
          :media-id="{{ $item->id}}"
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

<control-bar></control-bar>
@endsection

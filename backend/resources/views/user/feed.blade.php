@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="row">
      <div class="col-lg-12">
          <h2>Your Follower Feed</h2>
          <a class="nav-link" href="{{ url('/user/list') }}">Find users to follow</a>
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
  @elseif($followingCount == 0)
  <div class="alert alert-primary" role="alert">
    You're not following any users...
  </div>
  @elseif(count($items) <= 0)
  <div class="alert alert-primary" role="alert">
    No recent activity from users you follow...
  </div>
  @endif
</div>

<control-bar></control-bar>
@endsection

@extends('layouts.master')

@section('content')
<div class="container-fluid pushFromTop">
    <div class="row">
    </div>

    @if(@$collection)
    <div class="row">
    @foreach($collection as $item)
      <div class="col-lg-3">
        <youtube-card
          :global-queued="{!! json_encode($item->globalQueued) !!}"
          :media-id="{{ $item->id }}"
          :show-global-queue="true"
          video-id="{{ $item->index }}"
          thumbnail="{{ $item->meta->thumbnail }}"
          title="{{ $item->meta->title }}"
          :collected="{!! json_encode($item->collected) !!}">
        </youtube-card>
      </div>
    @endforeach
    </div>
  @endif
</div>
@endsection




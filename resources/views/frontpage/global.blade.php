@extends('layouts.master')
@section('content')
<div class="container-fluid pushFromTop">
  @if(@$activeItems)
    <h3>Global Queue Items</h3>
    <div class="row">
        @foreach($activeItems as $media)
        
            <div class="col-lg-3 col-md-6 col-sm-12">
                <youtube-card 
                    video-id="{{ $media->index }}" 
                    title="{{ $media->meta->title }}" 
                    thumbnail="{{ $media->meta->thumbnail }}"
                    @if($media->collected) :collected="{{ $media->collected }}" @endif
                    >
                </youtube-card>
            </div>
        @endforeach
    </div>
  @else
    <div class="row">
        <div class="col-lg-6 center">
            <h3>Global Queue is empty ...</h3>
            <img  src="https://media.giphy.com/media/Ty9Sg8oHghPWg/giphy.gif" />
            <h4 class="mt-3">Queue new items by using the new 'Global Queue' button in your collection</h4>
        </div>
    </div> 
  @endif

  <control-bar></control-bar>
</div>
@endsection

@extends('layouts.master')
@section('content')
<div class="container-fluid pushFromTop">
  <div class="row">
    <h2>Recommendation Engine Raw Feed:</h2>
  </div>

  <div class="row">
  	@foreach($items as $item)
    <div class="col-lg-3 col-md-12 col-sm-12 pushFromTop">
        <youtube-card 
          @if($item->id) :media-id="{{$item->id}}" @endif 
          title="{{$item->title}}"
          @if($item->collected) :collected="{{$item->collected}}" @endif
          item-id={{$item->vid}} 
          thumbnail={{$item->thumbnail}}
          >
        </youtube-card>
    @endforeach
  </div>
</div>
@endsection

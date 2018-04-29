@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
	<div class="row" style="margin-bottom: 10px;">
		<div class="col-lg-6">
			<div style="padding-bottom: 5px;">
				<a href="/admin/toplist/update" class="btn btn-outline-primary">Update Toplist items</a>
				<a href="/admin/toplist/clear" class="btn btn-outline-danger">Clear Toplist</a>
			</div>
			<br />

			<h4>Sort By:</h4>
			<a href="/admin/toplist/by/creation" @if($sort == 'creation') class="btn btn-success" @else class="btn btn-outline-success" @endif>Show By Creation Date</a>
			<a href="/admin/toplist/by/visible" @if($sort == 'visible') class="btn btn-primary" @else class="btn btn-outline-primary" @endif>Show Visible</a>
		</div>
	</div>

	@if(count($items) >= 1)
		<h5>Item Count: {{ count($items )}}</h5>
	@endif
   	@foreach($items as $item)
   	  	<div class="row">
			<div class="col-lg-6">
				<video-player-card title="{{ $item->title }}" vid="{{ $item->index }}" thumbnail={{$item->thumbnail }}></video-player-card>
			</div>

			<div class="col-lg-6">
				<p>Visible: {{$item->visible}}</p>
				@if($item->visible)
					<a href="/admin/toplist/media/visible/{{$item->id}}/0" class="btn btn-danger">Hide</a>
				@else
					<a href="/admin/toplist/media/visible/{{$item->id}}/1" class="btn btn-success">Make Visible</a>
				@endif
			</div>
		</div>
	@endforeach

	@if(count($items) == 0)
		<h4>No items found...</h4>
	@endif
</div>
@endsection

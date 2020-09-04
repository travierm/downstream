@extends('layouts.master')
@section('content')
<div class="container pushFromTop">
  <div class="row">
    <h2>Video Manager</h2>
  </div>

  <div class="row">
  	<div class="col">
  		<table class="table">
  			<thead>
  				<th>Title</th>
  				<th>Vid</th>
  			</thead>

  			@foreach($videos as $video)
  			<tr>
  				<td>{{$video->getMeta()->title}}</td>
  				<td>{{$video->index}}</td>
  			</tr>
  			@endforeach
  		</table>
  	</div>
  </div>
</div>
@endsection

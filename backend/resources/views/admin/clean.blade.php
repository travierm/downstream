@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="row">
    <table class="table">
    	<tr>
    		<th>ID</th>
    		<th>Title</th>
    		<th>Edit</th>
    	</tr>

    	@foreach($items as $media)
    	<tr>
    		<td>{{ $media->id }}</td>
    		<td>{{ $media->getMeta()->title }}</td>
    		<td><a href="/admin/media/edit/{{ $media->id }}" class="btn btn-primary">Edit</a></td>
    	</tr>
    	@endforeach
    </table>
  </div>
</div>
@endsection

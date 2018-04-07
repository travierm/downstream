@extends('layouts.master')
@section('content')
<div class="container pushFromTop">
  <div class="row">
    <h1>Admin Control Panel</h1>
  </div>

  <div class="row">
  	<form action="/admin/dash/settings" method="POST">
  		{{ csrf_field() }}

	  	<div class="col-lg-12">
			<div class="form-group">
			    <label for="select1">Show Latest Video on /</label>
			    <select name="settings_showLatestVideos" class="form-control" id="select1">
			      <option @if($settings['showLatestVideos'] == "no") selected @endif value="no">Disabled</option>
			      <option @if($settings['showLatestVideos'] == "yes") selected @endif value="yes">Enabled</option>
			 	</select>
			 </div>
	  	</div>
  	</form>	

  </div>

  <button class="btn btn-info">Save</button>

  <div class="row">
  	<div class="col-lg-8" style="margin-top: 25px;">
  		<about-page></about-page>
  	</div>
  </div>
</div>
@endsection

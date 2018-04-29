@extends('layouts.master')
@section('content')
<div class="container pushFromTop">
  <div class="row">
    <h1>Admin Control Panel</h1>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <h4>Links:</h4>

      <ul>
        <li><a href="/admin/toplist">Manage Toplist</a></li>
        <li><a href="/admin/filter/title">Create Title Filters</a></li>
        <li><a href="/admin/engine/clean">Bad Media Items</a></li>  
        <li><a href="/admin/engine">Recommendation Engine Feed</a></li>        
      </ul>
    </div>
  </div>

  <h4>Service Settings:</h4>
  <div class="row">
  	<form action="/admin/dash/settings" method="POST">
  		{{ csrf_field() }}

	  	<div class="col-lg-12">
			<div class="form-group">
			    <label for="select1">Show /all to users</label>
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

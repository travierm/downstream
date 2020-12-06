@extends('layouts.master')
@section('fixed-footer', 'fixed-bottom')

@section('content')
<div class="container pushFromTop">
  <div class="row">
    <h1>Admin Control Panel</h1>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <h5>Links:</h5>

      <ul>
        <!-- <li><a href="/admin/toplist">Manage Toplist</a></li> -->
        
        <li><a href="/admin/filter/title">Create Title Filters</a></li>
        <li><a href="/admin/globalqueue">Manage Global Queue</a></li>
        <li><a href="/admin/user/list">User List</a></li>
        <li><a href="/admin/logs">System Logs</a></li>
        <!-- <li><a href="/admin/engine/clean">Bad Media Items</a></li>  -->
        <!-- <li><a href="/admin/engine">Recommendation Engine Feed</a></li> -->
      </ul>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <ul class="list-group">
        <li class="list-group-item list-group-item-warning">Total Users: {{ App\User::count() }}</li>
        <li class="list-group-item list-group-item-warning">Total Media Items: {{ App\Media::count() }}</li>
        <li class="list-group-item list-group-item-warning">
          Media created this week: 
          {{ 
            App\Media::whereBetween('created_at', 
              [Carbon\Carbon::now()->startOfWeek(), Carbon\Carbon::now()->endOfWeek()]
            )->count()
          }}
        </li>
        <li class="list-group-item list-group-item-primary">Autofix.lastMediaId: {{ Cache::get('youtubeAutofix.lastMediaId') }} / {{ App\Media::count() }}</li>
        <li class="list-group-item list-group-item-primary">Autofix.fixedMediaItems: {{ Cache::get('youtubeAutofix.fixedMediaItems') }}</li>
      </ul>
    </div>
  </div>

  <!-- <h4>Service Settings:</h4>
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

  <button class="btn btn-info">Save</button>-->

  <div class="row">
  	<div class="col-lg-8 mt-2">
  		<about-page></about-page>
  	</div>
  </div>
</div>
@endsection

@extends('layouts.master')

@section('content')
<import-form inline-template>
<div class="container pushFromTop">
  <div class="row">
    <div class="Col-5">
      <img height="125" width="125" class="img-fluid" src="{{ asset('images/yt_logo_rgb_light.png')}}"></img>

      <form action="/import" method="post">
        {{ csrf_field() }}

        @if($errors->any())
          <div class="alert alert-danger pushFromTop" role="alert">{{$errors->first()}}</div>
        @endif

        @if(Session::has('success'))
          <div class="alert alert-success pushFromTop" role="alert">Your video was imported!</div>
        @endif

        <div class="form-group" style="margin-top:20px;">
          <label>Import Youtube Video ID or URL</label>
          <input required name="vid" type="text" class="form-control" placeholder="Video ID or URL">
          <small id="vidHelp" class="form-text text-muted"></small>
        </div>

        <button class="btn btn-success">Import</button>
      </form>
    </div>
  </div>
</div>
</import-form>
@endsection

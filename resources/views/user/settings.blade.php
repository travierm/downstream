@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
    <div class="row">
        <div class="col-lg-12">
            <h2>User Settings</h2>
            <p></p>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-3 ">
                 <div class="list-group ">
                  <a href="?tab=general" class="@if($tab == 'general') active @endif list-group-item list-group-item-action">General</a>
                  <a href="?tab=themes" class="@if($tab == 'themes') active @endif list-group-item list-group-item-action">Themes</a>
                  <a href="?tab=spotify" class="@if($tab == 'spotify') active @endif list-group-item list-group-item-action">Spotify</a>
                  <a href="?tab=privacy" class="@if($tab == 'privacy') active @endif list-group-item list-group-item-action">Privacy</a>
                 </div>
            </div>

            <div class="col-md-9">
               

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>{{ ucfirst($tab) }}</h3>
                                    <hr>
                                </div>

                                <div class="col-lg-12">
                                    @if (\Session::has('success'))
                                        <div class="alert alert-success">
                                            {!! \Session::get('success') !!}
                                        </div>
                                    @endif

                                    @if (\Session::has('error'))
                                        <div class="alert alert-danger">
                                            {!! \Session::get('error') !!}
                                        </div>
                                    @endif
                                    
                                    @includeWhen($tab == "themes", 'user.settings.theme', $data)
                                     <form id="settingsForm" method="POST">
                                    {{ csrf_field() }}
                                    @includeWhen($tab == "general", 'user.settings.general', $data)
                                    @includeWhen($tab == "spotify", 'user.settings.spotify', $data)
                                    @includeWhen($tab == "privacy", 'user.settings.privacy', $data)
                                    </form>
                                </div>

                                <div class="col-lg-12 mt-5">
                                    <hr>
                                    <button class="btn btn-lg btn-success">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

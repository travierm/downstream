<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{{ URL::asset('css/app.css') }}" rel="stylesheet" >
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=BGAqg9LG5E">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=BGAqg9LG5E">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=BGAqg9LG5E">
    <link rel="manifest" href="/site.webmanifest?v=BGAqg9LG5E">
    <link rel="shortcut icon" href="/favicon.ico?v=BGAqg9LG5E">
    <meta name="apple-mobile-web-app-title" content="Downstream">
    <meta name="application-name" content="Downstream">
    <meta name="msapplication-TileColor" content="#026876">
    <meta name="theme-color" content="#026876">

    @yield('meta')

    @if(!Auth::guest())
    <meta name="xyz-token" content="{{ auth()->user()->api_token }}">
    @endif

    <title>Downstream</title>
  </head>

  <body>
    <div id="app">
      <nav class="main-navbar navbar-expand-md navbar navbar-dark navbar-deco" style="background-color: #038294;">
        <div class="container-fluid">
          <a id="brand" class="navbar-brand" style="font-size:1.6em;" href="{{ url('/') }}">Downstream</a>
          <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                <a class="nav-link" href="{{ url('/all') }}">All</a>
              </li>
            </ul>
            <ul class="navbar-nav">
              @if(Auth::guest())
              <li class="nav-item">
                <a class="nav-link" href="{{ url('/register') }}">Register</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ url('/login') }}">Login</a>
              </li>
              @else
              <li class="nav-item">
                <router-link class="nav-link" to="/search">Search</router-link>
              </li>

              <li class="nav-item">
                <router-link class="nav-link" to="/collection">Collection</router-link>
              </li>

              <!--<li class="nav-item">
                <a class="nav-link" href="/hash">{{Auth::user()->getNavDisplayName()}}</a>
              </li>-->

              @if(Auth::user()->isAdmin())
              <li class="nav-item">
                <a class="nav-link" href="/admin/dash">Admin</a>
              </li>
              @endif
              
              <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
              </li>
              @endif
            </ul>
          </div>
          </div>
        </nav>

        <div class="container-fluid">
          <!-- Vue Router View -->
          <router-view :key="$route.fullPath"></router-view>

          <!-- PHP Generated HTML -->
          <div id="hardContent">
            @yield('content')
          </div>

          <div id="modals" style="display: none;">
            <!-- Modals -->
            <b-modal :visible=false id="registerModal" :lazy=true :hide-header=true :hide-footer=true title="Register" title="Bootstrap-Vue">
              <h4>Please Register!</h4>
              <p class="my-4">We only need your email and a secure password.</p>
              <p class="my-4">Click the button below to register in only a few secs!</p>
               <a href="/register" class="btn btn-info btn-lg btn-block">Register</a> 
            </b-modal>
          </div>
      </div>
      <!-- End App -->
      </div>

    <script>
    window.Laravel = <?php echo json_encode([
      'csrfToken' => csrf_token(),
    ]); ?>;

    window.config = {
      APP_LINK_URL: "<?php echo env('APP_LINK_URL'); ?>"
    };
    </script>

    <script src="{{ mix('js/app.js') }}"></script>
  </body>
</html>

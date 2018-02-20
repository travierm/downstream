<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{{ URL::asset('css/app.css') }}" rel="stylesheet" >
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!Auth::guest())
    <meta name="xyz-token" content="{{ auth()->user()->api_token }}">
    @endif

    <title>Downstream</title>
  </head>

  <body>
    <div id="app">
      <nav class="main-navbar navbar-expand-md navbar navbar-dark navbar-deco" style="background-color: #117F90;">
        <div class="container-fluid"> 
          <a id="brand" class="navbar-brand" style="font-size:1.6em;" href="{{ url('/') }}">Downstream</a>
          <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav mr-auto">
              @if(!Auth::guest())
              <li class="nav-item">
                <router-link class="nav-link" to="/search">Search</router-link>
              </li>
              @endif
            </ul>
            <ul class="navbar-nav">
              <li class="nav-item">
                <router-link class="nav-link" to="/about">About</router-link>
              </li>

              @if(Auth::guest())
              <li class="nav-item">
                <a class="nav-link" href="{{ url('/login') }}">Login</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ url('/register') }}">Register</a>
              </li>
              @else

              <li class="nav-item">
                <router-link class="nav-link" to="/collection">Collection</router-link>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="/hash">{{Auth::user()->getNavDisplayName()}}</a>
              </li>

              @if(Auth::user()->isAdmin())
              <li class="nav-item">
                <a class="nav-link" href="/admin/dash">Admin CP</a>
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
          <router-view></router-view>

          <!-- PHP Generated HTML -->
          <div id="hardContent">
            @yield('content')
          </div>
        </div>
      <!-- End App -->
      </div>

    <script>
    window.Laravel = <?php echo json_encode([
      'csrfToken' => csrf_token(),
    ]); ?>;
    </script>

    <script src="{{ mix('js/app.js') }}"></script>
  </body>
</html>

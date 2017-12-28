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
      <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
          <a id="brand" class="navbar-brand" href="#/frontpage">Downstream</a>
          <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav mr-auto">
              @if(!Auth::guest())
              <li class="nav-item">
                <router-link class="nav-link" to="/frontpage">Front Page</router-link>
              </li>

              <!--<li class="nav-item">
                <a class="nav-link" href="/import">Import</a>
              </li>-->

              <li class="nav-item">
                <a class="nav-link" href="/search">Search</a>
              </li>
              @endif
            </ul>
            <ul class="navbar-nav">
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
    <script type="text/javascript" src="https://www.youtube.com/iframe_api"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111656856-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-111656856-1');
    </script>
  </body>
</html>

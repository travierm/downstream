<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ URL::asset('css/app.css') }}" rel="stylesheet" >
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!Auth::guest())
    <meta name="xyz-token" content="{{ auth()->user()->api_token }}">
    @endif

    <title>Downstream</title>
  </head>

  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="/">Dowstream</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav mr-auto">
          @if(!Auth::guest())
          <li class="nav-item">
            <a class="nav-link" href="/">Front Page</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/import">Import</a>
          </li>

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
            <a class="nav-link" href="/collection">My Collection</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="">{{Auth::user()->shrinkHash()}}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/logout">Logout</a>
          </li>
          @endif
        </ul>
      </div>
    </nav>
    <div id="app" class="container-fluid">
      @yield('content')
    </div>

    <script type="text/javascript" src="https://www.youtube.com/iframe_api"></script>
    <script src="{{ mix('js/app.js') }}"></script>
  </body>
</html>

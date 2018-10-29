<nav class="main-navbar navbar-expand-md navbar navbar-dark navbar-deco" style="background-color: #4a52e8;">
    <div class="container-fluid">
        <a id="brand" class="navbar-brand" href="{{ url('/') }}">Downstream</a>
        <a target="_blank" href="https://github.com/travierm/downstream"><img src={{asset("images/GitHub-Mark-Light-64px.png")}} width="32" height="32" alt=""></a>
        
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse w-100" id="navbarNavDropdown">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all') }}">Global</a>
                </li>
                @if(!Auth::guest())
                <li id="navSearchLink" class="nav-item">
                    <a class="nav-link" href="{{ url('/search') }}">Search</a>
                </li>
                @endif
            </ul>
            <form class="mx-2 my-auto d-inline w-100 d-none d-sm-none d-lg-block" action="/search" method="GET">
                <div class="input-group">
                    <input autofocus id="searchInputBar" value="{{@$searchQuery}}" name="query" type="search" class="form-control" placeholder="Search...">
                    <span class="input-group-append">
                        <button class="btn btn-default border border-left-0" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>
            <ul class="navbar-nav">
                @if(Auth::guest())
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/register') }}">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/login') }}">Login</a>
                </li>
                @else
                @if(Auth::user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link" href="/admin/dash">Admin</a>
                </li>
                @endif

                <li class="nav-item">
                    <router-link class="nav-link text-lg" to="/collection">Collection</router-link>
                </li>
                <li class="nav-item">
                    <router-link class="nav-link" to="/discover">Discover</router-link>
                </li>
                <!--<li class="nav-item">
                    <router-link class="nav-link" to="/playlists">Playlists</router-link>
                </li>-->
                <!--<li class="nav-item">
                <a class="nav-link" href="/hash">{{Auth::user()->getNavDisplayName()}}</a>
              </li>-->
                <li class="nav-item">
                    <a class="nav-link" href="/logout">Logout</a>
                </li>

                
                @endif
            </ul>
        </div>
    </div>
</nav>
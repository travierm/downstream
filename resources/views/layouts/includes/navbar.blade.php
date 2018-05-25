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
                @if(!Auth::guest())
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/search') }}">Search</a>
                </li>
                @endif
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
                    <router-link class="nav-link" to="/discover">Discover</router-link>
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
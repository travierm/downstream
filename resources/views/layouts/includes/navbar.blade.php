<nav id="navbar"
    class="main-navbar navbar-expand-md navbar navbar-{{ @Auth::user() ? @Auth::user()->themeOption('style_type') : 'dark' }} navbar-deco">
    <div class="container-fluid">
        <a id="brand" class="navbar-brand" href="{{ url('/') }}">downstream</a>
        <a target="_blank" href="https://github.com/travierm/downstream"><img
                src={{ asset('images/GitHub-Mark-Light-64px.png') }} width="32" height="32" alt=""></a>

        <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            @if (Auth::guest())
                <div class="collapse navbar-collapse ml-3" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <!-- a class="nav-link" href="/demo">Try Demo <span class="sr-only">(current)</span></a> -->
                        </li>
                    </ul>
                </div>
            @else
                <form class="ml-3 mx-2 my-auto d-inline w-25 d-none d-sm-none d-lg-block" action="/search"
                    method="GET">
                    <div class="input-group w-100">
                        <input autofocus id="searchInputBar" value="{{ @$searchQuery }}" name="ds_query" type="search"
                            class="form-control" placeholder="Search...">
                        <span class="input-group-append">
                            <button onclick="this.form.submit()" class="btn btn-warning" type="button">
                                <i onclick="this.form.submit()" class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </form>
            @endif
            <ul class="navbar-nav">
                @if (Auth::guest())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/register') }}">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/login') }}">Login</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/feed') }}">Activity <span
                                class="badge badge-{{ @Auth::user() ? @Auth::user()->themeOption('style_type') : 'dark' }}">{{ $activity_feed_count }}</span></a>
                    </li>

                    @if (Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/dash') }}">Admin</a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/collection') }}">Collection</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle cursor-pointer" data-bs-toggle="dropdown">User</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="/user">Profile</a>
                            <a class="dropdown-item" href="/settings">Settings</a>
                            <a class="dropdown-item" href="/logout">Logout</a>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

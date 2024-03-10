<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link type="text/css" href="{{ URL::asset('css/app.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=7kbNm9OYmg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=7kbNm9OYmg">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=7kbNm9OYmg">
    <link rel="manifest" href="/site.webmanifest?v=7kbNm9OYmg">
    <link rel="mask-icon" href="/safari-pinned-tab.svg?v=7kbNm9OYmg" color="#4a52e8">
    <link rel="shortcut icon" href="/favicon.ico?v=7kbNm9OYmg">
    <meta name="apple-mobile-web-app-title" content="Downstream">
    <meta name="application-name" content="Downstream">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#8e93f1">
    <meta name="description" content="A free music collection and discovery service.">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css"
        integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    @vite('resources/css/app.css')

    @yield('meta')

    @if (!Auth::guest())
        <meta name="xyz-token" content="{{ auth()->user()->api_token }}">
    @endif

    <title>Downstream</title>

    @include('layouts.includes.theme-css')
</head>

<body>
    <div id="app">
        @include('layouts.includes.navbar')

        <div class="d-flex flex-row justify-content-end">
            <div class="flex-grow-1">
                @yield('content')
            </div>

            {{-- @include('layouts.includes.sidebar') --}}
        </div>

        @include('layouts.includes.playbar')
        {{-- @include('layouts.includes.footer') --}}
    </div>

    @vite(['resources/js/app.js'])

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
</body>


</html>

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
    <meta name="Description" content="A unique music service built around YouTube">

    @yield('meta')

    @if(!Auth::guest())
    <meta name="xyz-token" content="{{ auth()->user()->api_token }}">
    @endif

    <title>Downstream</title>
  </head>

  <body>
    <div id="app">
        @include('layouts.includes.navbar')

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
              <h4>Please Register</h4>
              <p class="my-4">To collect items you must register first.</p>
              <p class="my-4">Your data is stored securely and never shared with 3rd parties.</p>
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

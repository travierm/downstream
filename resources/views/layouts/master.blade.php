<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <!-- <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>  -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link type="text/css" href="{{ URL::asset('css/app.css') }}" rel="stylesheet" >
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    @vite('resources/css/app.css')

    @yield('meta')

    @if(!Auth::guest())
      <meta name="xyz-token" content="{{ auth()->user()->api_token }}">
    @endif

    <title>Downstream</title>

    @include('layouts.includes.theme-css')
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
              <h5 class="font-weight-bold">Registration Required</h5>
              <hr />

              <p class="my-4 lead">To start collecting items you must first register.</p>

              <a href="/register" class="btn btn-info btn-lg btn-block">Register</a> 
            </b-modal>
          </div>
      </div>
      <!-- End App -->
      </div>

      <footer class=" text-muted text-center text-small">
        <div class="container">
          <div class="row justify-content-center">
              <div class="col-lg-6">
                  <ul class="list-inline">
                      <li class="list-inline-item"><a class="theme-primary-text" href="/privacy">Privacy</a></li>
                      <li class="list-inline-item"><a class="theme-primary-text" href="/tos">Terms</a></li>
                      <li class="list-inline-item"><a class="theme-primary-text" href="https://www.youtube.com/t/terms">YouTube's Terms of Service</a></li>
                      <li class="list-inline-item"><a class="theme-primary-text" href="/contact">Contact Us</a></li>
                    </ul>
                    <p class="mb-1">&copy; {{ date('Y') }} Downstream. All rights reserved.</p>       
                    <p class="text-muted">Season 1</p>                    
                    <a href="https://youtube.com"><img class="img-fluid" width="300" src="https://developers.google.com/youtube/images/developed-with-youtube-sentence-case-dark.png"/></a>
              </div>
          </div>
        </div>    
      </footer>
    
    @vite([
      'resources/js/app.js'
    ])

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
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

<style>

      .main-navbar {
        background-color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
      }

      .btn-primary {
        border-color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
        background-color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
      }

      .btn-outline-primary {
          color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
          background-color: transparent;
          background-image: none;
          border-color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
      }

      .btn-outline-primary:hover {
          color: {{ @Auth::user() ? Auth::user()->themeOption('secondary') : '#4a52e8' }} !important;
          background-color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
          border-color: {{ @Auth::user() ? Auth::user()->themeOption('secondary') : '#4a52e8' }} !important;
      }

      .footer {
        border-color: {{ @Auth::user() ? Auth::user()->themeOption('secondary') : '#4a52e8' }} !important;
        background-color: {{ @Auth::user() ? Auth::user()->themeOption('secondary') : '#4a52e8' }} !important;
      }

      .list-group-item.active {
        border-color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
        background-color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
      }

      .theme-primary-text {
        color: {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }} !important;
      }

      .card-header-playing {
        background: linear-gradient(123deg, {{ @Auth::user() ? Auth::user()->themeOption('primary') : '#4a52e8' }}, {{ @Auth::user() ? Auth::user()->themeOption('secondary') : '#4a52e8' }});
      }

      /*@if((@Auth::user() && Auth::user()->themeOption('id') == 'night_mode'))

      body {
        color: #FFFFFF;
        background-color: {{  Auth::user()->themeOption('background_color') }};
      }

      .main-navbar {  
          background-color: {{ @Auth::user() ? Auth::user()->themeOption('secondary') : '#4a52e8' }} !important;
      }

      .card {
        background-color: {{  Auth::user()->themeOption('secondary') }};
      }

      .jumbotron {
        display: none;
      }

      @endif*/
</style>
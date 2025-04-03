<!doctype html>
<html lang="fr">
   <head>
      <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta charset="utf-8">
      <meta name="author" content="Lucas TOUJAS - Raphaël TOLANDAL - Stéphane PLATHEY--BADIN">
      <meta name="description" content="Venez rechercher un stage ou une opportunité pour la vie.">

      <title>@yield('title', 'MonStage')</title>

      <link rel="stylesheet" href="{{ asset('css/style.css') }}">
      <link rel="stylesheet" href="{{ asset('css/app.css') }}">
      <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
      <script src="{{ asset('js/notification.js') }}" defer></script>
      
      <script src="https://kit.fontawesome.com/1eff8d6f21.js" crossorigin="anonymous"></script>
      <script src="{{ asset('js/loader.js') }}" defer></script>
   </head>
   <body>
      <div id="loader-container">
         <span class="loader"></span>
      </div>
      <div id="content" style="display: none;">
         @include('partials.header')

         @if (session('success'))
            <div id="notification-success" class="notification-success">
               {{ session('success') }}
            </div>
         @endif

         @if (session('error'))
            <div class="alert alert-danger">
               {{ session('error') }}
            </div>
         @endif

         <main>
            @yield('content')
         </main>

         @include('partials.footer')
      </div>
   </body>
</html>
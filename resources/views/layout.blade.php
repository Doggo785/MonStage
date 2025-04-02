<!doctype html>
<html lang="fr">
   <head>
      <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
      <meta charset="utf-8">
      <meta name="author" content="Lucas TOUJAS - Raphaël TOLANDAL - Stéphane PLATHEY--BADIN">
      <meta name="description" content="Venez rechercher un stage ou une opportunité pour la vie.">
      <title>@yield('title', 'MonStage')</title>
      <link rel="stylesheet" href="{{ asset('css/style.css') }}">
      <link rel="stylesheet" href="{{ asset('css/app.css') }}">
      <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
      <script src="https://kit.fontawesome.com/1eff8d6f21.js" crossorigin="anonymous"></script>
      <script src="{{ asset('js/loader.js') }}" defer></script>
   </head>
   <body>
      <div id="loader-container">
         <span class="loader"></span>
      </div>
      <div id="content" style="display: none;">
         @include('partials.header')

         <main>
            @yield('content')
         </main>

         @include('partials.footer')
      </div>
   </body>
</html>
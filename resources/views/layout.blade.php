<!doctype html>
<html lang="fr">
   <head>
      <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
      <meta charset="utf-8">
      <meta name="author" content="Lucas TOUJAS - Raphaël TOLANDAL - Stéphane PLATHEY--BADIN">
      <meta name="description" content="Venez rechercher un stage ou une opportunité pour la vie.">
      <title>@yield('title', 'MonStage')</title>
      <link rel="stylesheet" href="{{ asset('css/style.css') }}">
      <script src="https://kit.fontawesome.com/1eff8d6f21.js" crossorigin="anonymous"></script>
   </head>
   <body>
      @include('partials.header')

      <main>
         @yield('content')
      </main>

      @include('partials.footer')
   </body>
</html>
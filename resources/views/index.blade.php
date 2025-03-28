@extends('layout')

@section('title', 'Accueil')

@section('content')
<section>
   <center><h1>Ton stage, à portée de main !</h1></center>
   <div class="input-icons">
      <form class="search-container" action="#">
         <i class="fa-solid fa-magnifying-glass"></i>
         <input type="text" class="search-input" placeholder="Mots clés...">
         <button type="submit" class="search-button">Rechercher</button>
      </form>
   </div>

   <div class="container_offre">
      <a href="{{ url('offre_ex') }}">
         <div class="card">
            <div class="title">Stage - Developer
               <div class="subtitle">TOTAL | PAU 64000</div>
            </div>
         </div>
      </a>

      <a href="{{ url('offre_ex') }}">
         <div class="card">
            <div class="title">Stage - Developer
               <div class="subtitle">TOTAL | PAU 64000</div>
            </div>
         </div>
      </a>

      <a href="{{ url('offre_ex') }}">
         <div class="card">
            <div class="title">Stage - Developer
               <div class="subtitle">TOTAL | PAU 64000</div>
            </div>
         </div>
      </a>
   </div>
</section>
@endsection
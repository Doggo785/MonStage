@extends('layout')

@section('title', 'Liste des Offres')

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
      @foreach ($offres as $offre)
         <a href="{{ route('offres.show', ['id' => $offre->ID_Offre]) }}">
            <div class="card {{ $offre->Etat == 0 ? 'expired' : '' }}">
               @if ($offre->Etat != 0)
                  <div class="status-indicator"></div>
               @endif
               <div class="title">{{ $offre->Titre }}</div>
               <div class="subtitle">
                  {{ $offre->entreprise->Nom ?? 'Entreprise inconnue' }} | 
                  {{ $offre->Ville->Nom ? ucfirst($offre->Ville->Nom) : 'Ville inconnue' }} | 
                  Publiée le {{ $offre->Date_publication }}
               </div>
               <div class="description">
                  {!! Str::limit(strip_tags($offre->Description), 100, '...') !!}
               </div>
            </div>
         </a>
      @endforeach
   </div>
</section>
@endsection
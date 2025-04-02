@extends('layout')

@section('title', 'Accueil')

@section('content')
<section>
<center><div class="hero">
   <div class="hero-content">
      <h1 class="animated-title">Bienvenue sur Mon Stage</h1>
      <p>Découvrez des opportunités de stage sélectionnées spécialement pour vous et rejoignez notre communauté pour booster votre carrière.</p>
      <a href="{{ route('offres.index') }}" class="btn-primary">Voir toutes les offres</a>
   </div>
</div></center>

<div class="recent-offers">
   <h2>Nos dernières offres</h2>
   @php
      // Filtrer et trier les offres valides par date décroissante
      $validOffres = $offres->filter(function ($offre) {
         return $offre->Etat == 1;
      })->sortByDesc('Date_publication')->values();
      
      // Récupérer initialement 4 offres validées
      $lastOffres = $validOffres->take(4);
      
      // Tant que nous n'avons pas 4 offres et qu'il en reste,
      // récupérer celles qui n'ont pas déjà été sélectionnées (par ID)
      while ($lastOffres->count() < 4 && 
             $validOffres->reject(function($offre) use ($lastOffres) {
                return $lastOffres->contains('ID_Offre', $offre->ID_Offre);
             })->count() > 0) {
         $missing = 4 - $lastOffres->count();
         $remainingOffres = $validOffres->reject(function($offre) use ($lastOffres) {
                              return $lastOffres->contains('ID_Offre', $offre->ID_Offre);
                           })
                           ->sortByDesc('Date_publication')
                           ->take($missing);
         $lastOffres = $lastOffres->merge($remainingOffres);
      }
   @endphp
   <div class="index_container_offre">
      @foreach ($lastOffres as $offre)
         <a href="{{ route('offres.show', ['id' => $offre->ID_Offre]) }}">
            <div class="index_card">
               <div class="title">{{ $offre->Titre }}</div>
               <div class="subtitle">
                  {{ $offre->entreprise->Nom ?? 'Entreprise inconnue' }}<br>
                  {{ $offre->Ville->Nom ? ucfirst($offre->Ville->Nom) : 'Ville inconnue' }},
                  {{ $offre->Ville->region->Nom ?? 'Région inconnue' }}, France<br>
                  Publiée le {{ \Carbon\Carbon::parse($offre->Date_publication)->format('d/m/Y') }}<br>
                  <strong>Compétences requises :</strong>
                  @if ($offre->competences && $offre->competences->isNotEmpty())
                     <div class="competences-container">
                        @foreach ($offre->competences as $competence)
                           <div class="competence-badge">{{ $competence->Libelle }}</div>
                        @endforeach
                     </div>
                  @else
                     <p>Aucune compétence spécifiée.</p>
                  @endif
               </div>
            </div>
         </a>
      @endforeach
   </div>
</div>

<div class="about">
   <h2>À propos de Mon Stage</h2>
   <p>Mon Stage est une plateforme dédiée à la recherche et à la gestion de stages pour étudiants et entreprises. Nous mettons en relation des talents avec des entreprises innovantes pour créer des opportunités de collaboration stimulantes.</p>
   <div class="about-details">
      <div class="detail">
         <i class="fa-solid fa-briefcase"></i>
         <h3>Offres de qualité</h3>
         <p>Chaque offre est vérifiée pour garantir la pertinence et la qualité des stages proposés.</p>
      </div>
      <div class="detail">
         <i class="fa-solid fa-user-graduate"></i>
         <h3>Communauté engagée</h3>
         <p>Rejoignez un réseau de milliers d’étudiants et d’entreprises à la recherche de nouvelles opportunités.</p>
      </div>
      <div class="detail">
         <i class="fa-solid fa-handshake"></i>
         <h3>Partenariats solides</h3>
         <p>Nous collaborons avec des entreprises reconnues pour offrir des stages formateurs et enrichissants.</p>
      </div>
   </div>
</div>
</section>
@endsection
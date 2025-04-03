@extends('layout')

@section('title', 'Liste des Offres')

@section('content')
<section>
   <center><h1>Ton stage, à portée de main !</h1></center>
   <div class="input-icons">
      <form class="search-container" action="{{ route('offres.index') }}" method="GET">
         <i class="fa-solid fa-magnifying-glass"></i>
         <input type="text" name="search" class="search-input" placeholder="Rechercher par entreprise, ville ou titre..." value="{{ request('search') }}">
         
         <!-- Filtre par entreprise -->
         <select name="entreprise" class="search-input filter-button">
            <option value="">Toutes les entreprises</option>
            @foreach ($entreprises as $entreprise)
                <option value="{{ $entreprise->ID_Entreprise }}" {{ request('entreprise') == $entreprise->ID_Entreprise ? 'selected' : '' }}>
                    {{ $entreprise->Nom }}
                </option>
            @endforeach
         </select>

         <!-- Filtre par région -->
         <select name="region" class="search-input filter-button">
            <option value="">Toutes les régions</option>
            @foreach ($regions as $region)
                <option value="{{ $region->ID_Region }}" {{ request('region') == $region->ID_Region ? 'selected' : '' }}>
                    {{ $region->Nom }}
                </option>
            @endforeach
         </select>

         <button type="submit" class="search-button">Rechercher</button>
      </form>
   </div>

   @if (auth()->check() && (Auth::user()->role->Libelle === 'Pilote' || Auth::user()->role->Libelle === 'Administrateur'))
      <div style="text-align: right; margin: 20px;">
         <a href="{{ route('offres.create') }}" class="btn1 btn-primary">Créer une Offre</a>
      </div>
   @endif

   <div class="container_offre">
      @foreach ($offres as $offre)
         @if ($offre->Etat == 1 || (auth()->check() && (Auth::user()->role->Libelle === 'Pilote' || Auth::user()->role->Libelle === 'Administrateur')))
            <a href="{{ route('offres.show', ['id' => $offre->ID_Offre]) }}">
               <div class="card {{ $offre->Etat == 0 ? 'expired' : '' }}">
                  @if ($offre->Etat == 0)
                     <div title="Offre désactivée"></div>
                  @else
                     <div class="status-indicator" style="background-color: green;" title="Offre active"></div>
                  @endif
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
                  @php
                     $inWishlist = auth()->check() && \App\Models\Wishlist::where('ID_User', auth()->id())->where('ID_Offre', $offre->ID_Offre)->exists();
                  @endphp
                  @if (auth()->check() && Auth::user()->role->Libelle !== 'Administrateur' && Auth::user()->role->Libelle !== 'Pilote')
                     @if ($inWishlist)
                        <button class="btn1 wishlist-added" disabled><i class="fa-solid fa-circle-check"></i></button>
                     @else
                        <form action="{{ route('wishlist.add') }}" method="POST" style="display:inline;">
                           @csrf
                           <input type="hidden" name="offre_id" value="{{ $offre->ID_Offre }}">
                           <button type="submit" class="btn1 btn-add-to-wishlist">+</button>
                        </form>
                     @endif
                  @endif
               </div>
            </a>
         @endif
      @endforeach
   </div>

   <!-- Liens de pagination -->
   <div class="pagination-links" style="text-align: center; margin-top: 20px;">
      {{ $offres->links('pagination::bootstrap-4') }}
   </div>
</section>
@endsection
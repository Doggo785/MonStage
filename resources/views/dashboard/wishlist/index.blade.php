@extends('layout')

@section('title', 'Ma Wishlist')

@section('content')
<section>
    <center><h1>Ma Wishlist</h1></center>
    <div class="container_offre">
        @if ($wishlists->isEmpty())
            <p style="text-align: center; font-size: 18px; color: #555; margin-top: 20px;">
                Oh, il semblerait que votre wishlist soit vide..
            </p>
        @else
            @foreach ($wishlists as $wishlist)
                <a href="{{ route('offres.show', ['id' => $wishlist->offre->ID_Offre]) }}">
                    <div class="card {{ $wishlist->offre->Etat == 0 ? 'expired' : '' }}">
                        @if ($wishlist->offre->Etat == 0)
                            <div title="Offre désactivée"></div>
                        @else
                            <div class="status-indicator" style="background-color: green;" title="Offre active"></div>
                        @endif
                        <div class="title">{{ $wishlist->offre->Titre }}</div>
                        <div class="subtitle">
                            {{ $wishlist->offre->entreprise->Nom ?? 'Entreprise inconnue' }}<br>
                            {{ $wishlist->offre->Ville->Nom ? ucfirst($wishlist->offre->Ville->Nom) : 'Ville inconnue' }},
                            {{ $wishlist->offre->Ville->region->Nom ?? 'Région inconnue' }}, France<br>
                            Publiée le {{ \Carbon\Carbon::parse($wishlist->offre->Date_publication)->format('d/m/Y') }}<br>
                            <strong>Compétences requises :</strong>
                            @if ($wishlist->offre->competences && $wishlist->offre->competences->isNotEmpty())
                                <div class="competences-container">
                                    @foreach ($wishlist->offre->competences as $competence)
                                        <div class="competence-badge">{{ $competence->Libelle }}</div>
                                    @endforeach
                                </div>
                            @else
                                <p>Aucune compétence spécifiée.</p>
                            @endif
                        </div>
                        <form action="{{ route('wishlist.remove', $wishlist->offre->ID_Offre) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn1">-</button>
                        </form>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</section>
@endsection
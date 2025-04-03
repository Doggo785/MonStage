@extends('layout')

@section('title', 'Mes Candidatures')

@section('content')
<section>
    <center><h1>Mes Candidatures</h1></center>
    <div class="container_offre">
        @if ($candidatures->isEmpty())
            <p style="text-align: center; font-size: 18px; color: #555; margin-top: 20px;">
                Vous n'avez postulé à aucune offre pour le moment.
            </p>
        @else
            @foreach ($candidatures as $candidature)
                <a href="{{ route('offres.show', ['id' => $candidature->offre->ID_Offre]) }}">
                    <div class="card {{ $candidature->offre->Etat == 0 ? 'expired' : '' }}">
                        @if ($candidature->offre->Etat == 0)
                            <div title="Offre désactivée"></div>
                        @else
                            <div class="status-indicator" style="background-color: green;" title="Offre active"></div>
                        @endif
                        <div class="title">{{ $candidature->offre->Titre }}</div>
                        <div class="subtitle">
                            {{ $candidature->offre->entreprise->Nom ?? 'Entreprise inconnue' }}<br>
                            {{ $candidature->offre->Ville->Nom ? ucfirst($candidature->offre->Ville->Nom) : 'Ville inconnue' }},
                            {{ $candidature->offre->Ville->region->Nom ?? 'Région inconnue' }}, France<br>
                            Publiée le {{ \Carbon\Carbon::parse($candidature->offre->Date_publication)->format('d/m/Y') }}<br>
                            <strong>Statut de la candidature :</strong> {{ $candidature->statut->Libelle ?? 'Statut inconnu' }}<br>
                            <strong>Date de candidature :</strong> {{ \Carbon\Carbon::parse($candidature->Date_postule)->format('d/m/Y') }}
                        </div>
                        @if ($candidature->CV_path || $candidature->LM_Path)
                            <div class="documents">
                                @if ($candidature->CV_path)
                                    <a href="{{ asset('storage/' . $candidature->CV_path) }}" target="_blank" class="btn1">Voir CV</a>
                                @endif
                                @if ($candidature->LM_Path)
                                    <a href="{{ asset('storage/' . $candidature->LM_Path) }}" target="_blank" class="btn1">Voir Lettre de Motivation</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        @endif
    </div>
    <div class="pagination-links" style="text-align: center; margin-top: 20px;">
        {{ $candidatures->links('pagination::bootstrap-4') }}
    </div>
</section>
@endsection
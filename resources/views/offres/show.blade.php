@extends('layout')

@section('title', 'MonStage - ' . $offre->Titre)

@section('content')

<section >
<div class="container_offre_show">
    <div class="vide"></div>
    
    <div class="content_offre">
        <h1>{{ $offre->Titre }}</h1>
        <p class="ptit-texte">
            {{ $offre->entreprise->Nom ?? 'Entreprise inconnue' }} | 
            {{ ucfirst($offre->ville->Nom) ?? 'Ville inconnue' }} | 
            Publiée le {{ \Carbon\Carbon::parse($offre->Date_publication)->format('d/m/Y') }} | 
            Ref. {{ $offre->ID_Offre }}
        </p>

        <article>
            <h3>Description du poste</h3>
            <p>{!! $offre->Description !!}</p>
            

            <h3>Compétences requises</h3>
            @if ($offre->competences && $offre->competences->isNotEmpty())
                <div class="competences-container">
                    @foreach ($offre->competences as $competence)
                        <div class="competence-badge">{{ $competence->Libelle }}</div>
                    @endforeach
                </div>
            @else
                <p>Aucune compétence spécifiée.</p>
            @endif
            
            
            <h3>Profil recherché</h3>
            <p>
                - Secteur : {{ $offre->secteur->Nom ?? 'Secteur inconnu' }}            
                <br>- Localisation : {{ $offre->ville->Nom ?? 'Ville inconnue' }} ({{ $offre->ville->CP ?? 'Code postal inconnu' }})                 
                <br>- Entreprise : {{ $offre->entreprise->Nom ?? 'Entreprise inconnue' }}            
                <br>- Rémunération : {{ $offre->Remuneration ?? 'Non précisée' }} €
                <br>- Date de publication : {{ \Carbon\Carbon::parse($offre->Date_publication)->format('d/m/Y') }}
                <br>- Date d'expiration : {{ \Carbon\Carbon::parse($offre->Date_expiration)->format('d/m/Y') }}
            </p>
            
            <h3>Avantages</h3>
            <p>
                - {{ $offre->entreprise->Description ?? 'Aucun avantage spécifié.' }}
            </p>
        </article>
    </div>	

        <div class="content_entreprise">
            <div class="profile-picture-wrapper">
                    <img id="profile-picture-{{ $offre->entreprise->ID_Entreprise }}" 
                    src="{{ $offre->entreprise->pfp_path ? asset('storage/' . $offre->entreprise->pfp_path) : asset('assets/default-company.png') }}" 
                    alt="Logo de {{ $offre->entreprise->Nom }}" class="card__img profile-picture">
                </div>
            <div>
            <h3 class="card__name">{{ $offre->entreprise->Nom }}</h3>
                <span class="card__price">{{ ucfirst($offre->entreprise->ville->Nom) }} | {{ $offre->entreprise->ville->CP }}</span><br>
                <span class="card__price">
                    @if ($offre->entreprise->avis->avg('Note'))
                        {{ number_format($offre->entreprise->avis->avg('Note'), 1) }} / 5 
                        <i class="fa-solid fa-star" style="color: gold;"></i>
                    @else
                        Non notée
                    @endif
                </span>
            </div>
        </div>
        <div class="vide"></div>
        <div class="vide"></div>
        <div class="content_postuler">
            <div class="submit-button">
                <!-- Bouton pour ouvrir la modale -->
                @if (Auth::check() && Auth::user()->role->Libelle === 'Etudiant')
                    <button type="button" class="btn2" onclick="openModal()">Je postule</button>
                @elseif (Auth::check() && (Auth::user()->role->Libelle === 'Administrateur' || Auth::user()->role->Libelle === 'Pilote'))
                    <div style="position: relative; display: inline-block;">
                        <button type="button" class="btn2" style="cursor: not-allowed; opacity: 0.6;" disabled>Je postule</button>
                        <div style="position: absolute; top: -55px; left: 0; background-color: #dc3545; color: white; padding: 5px; border-radius: 5px; font-size: 12px; display: none;" class="tooltip">
                            Accessible uniquement aux étudiants
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn2">Je postule</a>
                @endif

                <!-- Boutons Éditer et Supprimer (affichés uniquement pour les administrateurs ou pilotes) -->
                @if (auth()->check() && (Auth::user()->role->Libelle === 'Pilote' || Auth::user()->role->Libelle === 'Administrateur'))
                    <a href="{{ route('offres.edit', ['id' => $offre->ID_Offre]) }}" class="btn2">Éditer</a>
                    <form action="{{ route('offres.destroy', ['id' => $offre->ID_Offre]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')">Supprimer</button>
                    </form>
                @endif
            </div>
        </div>
</div>				
    <br>
</section>


<!-- Modale pour postuler -->
<div id="postulerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Postuler à l'offre : {{ $offre->Titre }}</h2>
        <form action="{{ route('offres.apply.submit', ['id' => $offre->ID_Offre]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="cv">Déposer votre CV :</label><br>
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required><br><br>

            <label for="motivation">Lettre de motivation :</label><br>
            <textarea id="motivation" name="motivation" rows="5" required></textarea><br><br>

            <button type="submit" class="btn2">Envoyer ma candidature</button>
        </form>
    </div>
</div>

@if ($candidature = $offre->candidatures->where('ID_User', auth()->id())->first())
    <h3>Vos fichiers envoyés</h3>
    <ul>
        @if ($candidature->CV_path)
            <li>
                <a href="{{ asset('storage/' . $candidature->CV_path) }}" target="_blank">Voir le CV</a>
                <form action="{{ route('offres.deleteFile', ['id' => $offre->ID_Offre, 'type' => 'cv']) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn2">Supprimer</button>
                </form>
            </li>
        @endif
        @if ($candidature->LM_Path)
            <li>
                <a href="{{ asset('storage/' . $candidature->LM_Path) }}" target="_blank">Voir la lettre de motivation</a>
                <form action="{{ route('offres.deleteFile', ['id' => $offre->ID_Offre, 'type' => 'motivation']) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn2">Supprimer</button>
                </form>
            </li>
        @endif
    </ul>
@endif

@include('partials.footer')

<!-- Scripts pour gérer la modale -->
<style>
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    }

    .modal.active-modal {
        display: block !important;
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        width: 80%;
    }
</style>

<script>
    function openModal() {
        const modal = document.getElementById('postulerModal');
        modal.style.display = 'block';
        modal.classList.add('active-modal');
    }

    function closeModal() {
        const modal = document.getElementById('postulerModal');
        modal.style.display = 'none';
        modal.classList.remove('active-modal');
    }

    // Empêche la fermeture du modal lorsqu'on clique à l'intérieur de la carte
    document.querySelectorAll('.modal__card').forEach(modalCard => {
        modalCard.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Ferme le modal lorsqu'on clique en dehors de la carte
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', () => {
            closeModal();
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn2[disabled]').forEach(button => {
            const tooltip = button.nextElementSibling;
            button.addEventListener('mouseenter', () => {
                tooltip.style.display = 'block';
            });
            button.addEventListener('mouseleave', () => {
                tooltip.style.display = 'none';
            });
        });
    });
</script>
@endsection
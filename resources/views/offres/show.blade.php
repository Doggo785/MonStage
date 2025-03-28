@extends('layout')

@section('title', 'MonStage - ' . $offre->Titre)

@section('content')
<main>
    <div class="background_image"></div>
    <section style="margin-left: 20px; margin-right: 20px;">
        <h1>{{ $offre->Titre }}</h1>
        <p class="ptit-texte">
            {{ $offre->entreprise->Nom ?? 'Entreprise inconnue' }} | 
            {{ $offre->ville->Nom ?? 'Ville inconnue' }} | 
            Publiée le {{ $offre->Date_publication }} | 
            Ref. {{ $offre->ID_Offre }}
        </p>
        <h3 class="page-header">Résumé de l'offre</h3>
        <p class="bloc-texte">{{ $offre->Description }}</p>&nbsp;

        <article>
            <h3>Description du poste</h3>
            <p>{{ $offre->Description }}</p>
            
            <h3>Missions principales</h3>  
            <p>
                - Collaborer avec l'équipe pour atteindre les objectifs du projet.              
                <br>- Participer activement aux tâches assignées.                  
                <br>- Apporter des idées innovantes pour améliorer les processus.                  
            </p>
            
            <h3>Profil recherché</h3>
            <p>
                - Secteur : {{ $offre->secteur->Nom ?? 'Secteur inconnu' }}            
                <br>- Localisation : {{ $offre->ville->Nom ?? 'Ville inconnue' }} ({{ $offre->ville->CP ?? 'Code postal inconnu' }})                 
                <br>- Entreprise : {{ $offre->entreprise->Nom ?? 'Entreprise inconnue' }}            
                <br>- Rémunération : {{ $offre->Remuneration ?? 'Non précisée' }} €
            </p>
            
            <h3>Avantages</h3>
            <p>
                - {{ $offre->entreprise->Description ?? 'Aucun avantage spécifié.' }}
            </p>
            
            <div class="submit-button">
                <!-- Bouton pour ouvrir la modale -->
                <button type="button" class="btn2" onclick="openModal()">Je postule</button>
            </div>
        </article>					
        <br>
    </section>
</main>

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

@include('partials.footer')

<!-- Scripts pour gérer la modale -->
<script>
    function openModal() {
        document.getElementById('postulerModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('postulerModal').style.display = 'none';
    }
</script>

<!-- Styles pour la modale -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 8px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
@endsection
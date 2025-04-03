@extends('layout')

@section('title', 'Dashboard')

@section('content')
<section>
    @if (Auth::user()->role->Libelle === 'Pilote')
        <center><h1>Espace Pilote</h1></center>
        <div class="container_compte">
            <div class="compte">
                <!-- Display the profile picture -->
                <div class="profile-container">
                    <!-- Conteneur de la photo de profil -->
                    <div class="profile-picture-wrapper">
                        <img src="{{ Auth::user()->pfp_path ? asset('storage/' . Auth::user()->pfp_path) : asset('assets/default-avatar.png') }}" 
                             alt="Avatar" class="photo_compte profile-picture">
                        <!-- Logo de modification -->
                        <div class="edit-overlay">
                            <i class="fa-solid fa-pen"></i>
                        </div>
                    </div>

                    <!-- Formulaire pour uploader une nouvelle photo -->
                    <form id="profile-picture-form" action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewProfilePicture(event)">
                    </form>

                    <!-- Bouton pour enregistrer les modifications -->
                    <button id="save-profile-picture" class="btn1" style="display: none;" onclick="document.getElementById('profile-picture-form').submit();">
                        Enregistrer les modifications
                    </button>
                </div>

                <!-- Informations utilisateur -->
                <div class="user-info">
                    <div class="user-name">
                        <h3>{{ strtoupper(Auth::user()->Nom) }} {{ ucfirst(strtolower(Auth::user()->Prenom)) }}</h3>
                    </div>
                    <div class="user-email">
                        <h4><i class="fa-solid fa-envelope"></i> {{ Auth::user()->Email }}</h4>
                    </div>
                    <div class="user-phone">
                        <h4><i class="fa-solid fa-phone"></i> {{ Auth::user()->Telephone ?? 'Numéro non renseigné' }}</h4>
                    </div>
                </div>
            </div>   

            <div class="whishlist">
                <h2>Actions Pilote</h2>
                <div class="card_compte">
                    <a href="{{ route('users.index') }}">
                    <div class="content">
                        <div class="title">Gérer les étudiants</div>
                        <div class="subtitle">Affiche la liste des étudiants</div>
                    </div>
                    </a>
                </div>
                <div class="card_compte">
                    <a href="{{ route('offres.index') }}">
                    <div class="content">
                        <div class="title">Gérer les offres</div>
                        <div class="subtitle">Affiche la liste des offres</div>
                    </div>
                    </a>
                </div>
            </div>
        </div>

    @elseif (Auth::user()->role->Libelle === 'Etudiant')
        <center><h1>Espace Étudiant</h1></center>
        <div class="container_compte">
            <div class="compte">
                <div class="profile-container">
                    <!-- Conteneur de la photo de profil -->
                    <div class="profile-picture-wrapper">
                        <img src="{{ Auth::user()->pfp_path ? asset('storage/' . Auth::user()->pfp_path) : asset('assets/default-avatar.png') }}" 
                             alt="Avatar" class="photo_compte profile-picture">
                        <!-- Logo de modification -->
                        <div class="edit-overlay">
                            <i class="fa-solid fa-pen"></i>
                        </div>
                    </div>

                    <!-- Formulaire pour uploader une nouvelle photo -->
                    <form id="profile-picture-form" action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewProfilePicture(event)">
                    </form>

                    <!-- Bouton pour enregistrer les modifications -->
                    <button id="save-profile-picture" class="btn1" style="display: none;" onclick="document.getElementById('profile-picture-form').submit();">
                        Enregistrer les modifications
                    </button>
                </div>

                <!-- Informations utilisateur -->
                <div class="user-info">
                    <div class="user-name">
                        <h3>{{ strtoupper(Auth::user()->Nom) }} {{ ucfirst(strtolower(Auth::user()->Prenom)) }}</h3>
                    </div>
                    <div class="user-email">
                        <h4><i class="fa-solid fa-envelope"></i> {{ Auth::user()->Email }}</h4>
                    </div>
                    <div class="user-phone">
                        <h4><i class="fa-solid fa-phone"></i> {{ Auth::user()->Telephone ?? 'Numéro non renseigné' }}</h4>
                    </div>
                </div>

                <!-- Affichage du nombre de candidatures -->
                <div class="candidature-stats">
                    <?php
                    $statutRecherche = Auth::user()->etudiant->Statut_recherche ?? null;
                    ?>
                    <h4>
                        Vous avez postulé à <strong>{{ Auth::user()->candidatures->count() }}</strong> offre(s).
                    </h4>
                    <h4>
                        Statut : 
                        <strong>
                            @if ($statutRecherche == 1)
                                En recherche de stage
                            @elseif ($statutRecherche == 2)
                                A trouvé un stage
                            @else
                                Statut inconnu
                            @endif
                        </strong>
                    </h4>
                </div>
            </div>

            <!-- Dernières candidatures -->
            
            <div class="whishlist">
            <h2>Dernières candidatures</h2>
                <div class="button_right">
                    <a href="{{ route('dashboard.candidatures.index') }}" class="btn1">Voir toutes mes candidatures</a>
                </div>
                @if ($candidatures = Auth::user()->candidatures()->orderBy('Date_postule', 'desc')->take(3)->get())
                    @foreach ($candidatures as $candidature)
                            <div class="card_compte">
                            <a href="{{ route('offres.show', ['id' => $candidature->offre->ID_Offre]) }}">
                                <div class="content" style="text-decoration: none;">
                                    <div class="title">{{ $candidature->offre->Titre }}</div>
                                    <div class="subtitle">{{ $candidature->offre->entreprise->Nom }} | {{ $candidature->offre->ville->Nom }}</div>
                                </div>
                                </a>
                            </div>
                    @endforeach
                @else
                    <p>Aucune candidature récente.</p>
                @endif
                <br>
            </div>
        </div>
        
    @elseif (Auth::user()->role->Libelle === 'Administrateur')
        <center><h1>Espace Administrateur</h1></center>
        <div class="container_compte">
            <div class="compte">
                <div class="profile-container">
                    <!-- Conteneur de la photo de profil -->
                    <div class="profile-picture-wrapper">
                        <img src="{{ Auth::user()->pfp_path ? asset('storage/' . Auth::user()->pfp_path) : asset('assets/default-avatar.png') }}" 
                             alt="Avatar" class="photo_compte profile-picture">
                        <!-- Logo de modification -->
                        <div class="edit-overlay">
                            <i class="fa-solid fa-pen"></i>
                        </div>
                    </div>

                    <!-- Formulaire pour uploader une nouvelle photo -->
                    <form id="profile-picture-form" action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewProfilePicture(event)">
                    </form>

                    <!-- Bouton pour enregistrer les modifications -->
                    <button id="save-profile-picture" class="btn1" style="display: none;" onclick="document.getElementById('profile-picture-form').submit();">
                        Enregistrer les modifications
                    </button>
                </div>

                <!-- Informations utilisateur -->
                <div class="user-info">
                    <div class="user-name">
                        <h3>{{ strtoupper(Auth::user()->Nom) }} {{ ucfirst(strtolower(Auth::user()->Prenom)) }}</h3>
                    </div>
                    <div class="user-email">
                        <h4><i class="fa-solid fa-envelope"></i> {{ Auth::user()->Email }}</h4>
                    </div>
                    <div class="user-phone">
                        <h4><i class="fa-solid fa-phone"></i> {{ Auth::user()->Telephone ?? 'Numéro non renseigné' }}</h4>
                    </div>
                </div>
            </div>
            <div class="whishlist">
                <h2>Actions Administrateur</h2>
                <div class="card_compte">
                    <a href="{{ route('users.index') }}">
                    <div class="content">
                        <div class="title">Gérer les utilisateurs</div>
                        <div class="subtitle">Affiche la liste des utilisateurs</div>
                    </div>
                    </a>
                </div>
                <div class="card_compte">
                    <a href="{{ route('offres.index') }}">
                    <div class="content">
                        <div class="title">Gérer les offres</div>
                        <div class="subtitle">Affiche la liste des offres</div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    @else
        <center><h1>Accès refusé</h1></center>
        <p>Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
    @endif
</section>

<script>
    function previewProfilePicture(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];
        const reader = new FileReader();

        if (file) {
            reader.onload = function (e) {
                // Met à jour l'image de profil avec l'aperçu
                document.querySelector('.profile-picture').src = e.target.result;

                // Affiche le bouton "Enregistrer les modifications"
                document.getElementById('save-profile-picture').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Ouvre le sélecteur de fichier lorsque l'utilisateur clique sur l'overlay
    document.querySelector('.edit-overlay').addEventListener('click', function () {
        document.getElementById('profile_picture').click();
    });
</script>
@endsection
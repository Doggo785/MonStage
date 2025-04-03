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
                <div class="admin-actions">
                    <h2>Actions Administrateur</h2>
                    <ul>
                        <li><a href="{{ route('users.index') }}" class="btn1 btn-primary">Gérer les utilisateurs</a></li>
                        <li><a href="{{ route('offres.index') }}" class="btn1 btn-primary">Gérer les offres</a></li>
                    </ul>
                </div>
            </div>
            <div class="whishlist">
        <h2>Gestion des élèves</h2>
            <div class="button_right">
                <a class="btn1">Voir tous les élèves</a>
            </div>
            <div class="card_compte">
                    <div class="content">
                        <div class="title">Un evèle au pif</div>
                        <div class="subtitle">Statut : pauvre</div>
                    </div>
            </div>
        </div>

        
        <div class="offre">
        <h2>Gestion des offres</h2>
            <div class="button_right">
                <a href="{{ route('offres.index') }}" class="btn1">Voir toutes les offres</a>
            </div>
            <div class="card_compte">
                    <div class="content">
                        <div class="title">Stage de ouf</div>
                        <div class="subtitle">en france j'espère</div>
                    </div>
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
                <h4>Statut : En attente</h4>
            </div>

            <!-- Dernières candidatures -->
            
            <div class="dashboard_container_offre">
                <div class="header">
                    <h2 style="display: inline-block;">Dernières candidatures</h2>
                    <a href="{{ route('dashboard.candidatures.index') }}" class="btn1 btn-primary" style="float: right;">Voir toutes mes candidatures</a>
                </div>
                @if ($candidatures = Auth::user()->candidatures()->orderBy('Date_postule', 'desc')->take(3)->get())
                    @foreach ($candidatures as $candidature)
                        <a href="{{ route('offres.show', ['id' => $candidature->offre->ID_Offre]) }}" class="card-link">
                            <div class="dashboard_card">
                                <div class="content">
                                    <div class="title">{{ $candidature->offre->Titre }}</div>
                                    <div class="subtitle">{{ $candidature->offre->entreprise->Nom }} | {{ $candidature->offre->ville->Nom }}</div>
                                </div>
                            </div>
                        </a>
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
            <div class="admin-actions">
                <h2>Actions Administrateur</h2>
                <ul>
                    <li><a href="{{ route('users.index') }}" class="btn1 btn-primary">Gérer les utilisateurs</a></li>
                    <li><a href="{{ route('offres.index') }}" class="btn1 btn-primary">Gérer les offres</a></li>
                </ul>
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
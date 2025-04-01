@extends('layout')

@section('title', 'Entreprises')

@section('content')
<main>
    <center><h1>Tu cherches une entreprise en particulier ?</h1></center>
    <section class="container">
        <div class="card__container">
            @foreach ($entreprises as $entreprise)
                <div class="card__box">
                    <!-- CARD PRODUCT -->
                    <div class="card__product">
                        <div class="profile-container">
                            <!-- Conteneur de la photo de l'entreprise -->
                            <div class="profile-picture-wrapper">
                                <img id="profile-picture-{{ $entreprise->ID_Entreprise }}" 
                                     src="{{ $entreprise->pfp_path ? asset('storage/' . $entreprise->pfp_path) : asset('assets/default-company.png') }}" 
                                     alt="Logo de {{ $entreprise->Nom }}" class="card__img profile-picture">
                                
                                <!-- Logo de modification (visible uniquement pour Admin et Pilote) -->
                                @if (Auth::user()->role->Libelle === 'Administrateur' || Auth::user()->role->Libelle === 'Pilote')
                                    <div class="edit-overlay">
                                        <i class="fa-solid fa-pen"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Formulaire pour uploader une nouvelle photo -->
                            @if (Auth::user()->role->Libelle === 'Administrateur' || Auth::user()->role->Libelle === 'Pilote')
                                <form id="profile-picture-form-{{ $entreprise->ID_Entreprise }}" 
                                      action="{{ route('entreprises.update_picture', $entreprise->ID_Entreprise) }}" 
                                      method="POST" enctype="multipart/form-data" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" id="profile_picture_{{ $entreprise->ID_Entreprise }}" 
                                           name="image" accept="image/*" 
                                           onchange="previewProfilePicture(event, {{ $entreprise->ID_Entreprise }})">
                                </form>

                                <!-- Bouton pour enregistrer les modifications -->
                                <button id="save-profile-picture-{{ $entreprise->ID_Entreprise }}" 
                                        class="btn1" style="display: none;" 
                                        onclick="document.getElementById('profile-picture-form-{{ $entreprise->ID_Entreprise }}').submit();">
                                    Enregistrer les modifications
                                </button>
                            @endif
                        </div>

                        <!-- Informations de l'entreprise -->
                        <div>
                            <h3 class="card__name">{{ $entreprise->Nom }}</h3>
                            <span class="card__price">{{ ucfirst($entreprise->ville->Nom) }} | {{ $entreprise->ville->CP }}</span><br>
                            <span class="card__price">{{ $entreprise->Note ?? 'Non notée' }}</span>
                        </div>
                    </div>
                </div>

                <!-- POPUP MODAL -->
                <div class="modal">
                    <div class="modal__card">
                        <i class="fa-solid fa-xmark modal__close"></i>
                        <img src="{{ asset('storage/' . $entreprise->pfp_path) }}" alt="Logo de {{ $entreprise->Nom }}" class="modal__img">
                        <div>
                            <h3 class="modal__name">{{ $entreprise->Nom }}</h3>
                            <p class="modal__info">
                                Téléphone : {{ $entreprise->Telephone }}<br>
                                Email : {{ $entreprise->Email }}<br>
                                Site : 
                                @if ($entreprise->Site)
                                    <a href="{{ $entreprise->Site }}" target="_blank">{{ $entreprise->Site }}</a>
                                @else
                                    Non disponible
                                @endif
                                <br>
                                Description : {{ $entreprise->Description }}
                            </p>
                        </div>
                        <div class="modal__buttons">
                            <a href="{{ route('offres.index', ['entreprise' => $entreprise->ID_Entreprise]) }}">
                                <button class="modal__button">Voir leurs offres</button>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</main>

<footer>
    <br>
    <div class="footer_fixe">&copy;2025 - Tous droits réservés - JGT</div>
</footer>

<script>
    function previewProfilePicture(event, entrepriseId) {
        const fileInput = event.target;
        const file = fileInput.files[0];
        const reader = new FileReader();

        if (file) {
            reader.onload = function (e) {
                // Met à jour l'image de profil avec l'aperçu
                document.getElementById(`profile-picture-${entrepriseId}`).src = e.target.result;

                // Affiche le bouton "Enregistrer les modifications"
                document.getElementById(`save-profile-picture-${entrepriseId}`).style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Ouvre le sélecteur de fichier lorsque l'utilisateur clique sur l'overlay
    document.querySelectorAll('.edit-overlay').forEach(overlay => {
        // Supprime les anciens gestionnaires d'événements pour éviter les doublons
        overlay.removeEventListener('click', handleOverlayClick);

        // Ajoute un gestionnaire d'événement unique
        overlay.addEventListener('click', handleOverlayClick);
    });

    function handleOverlayClick(event) {
        event.stopPropagation(); // Empêche la propagation de l'événement
        const entrepriseId = this.closest('.profile-container').querySelector('input[type="file"]').id.split('_')[2];
        document.getElementById(`profile_picture_${entrepriseId}`).click();
    }
</script>
@endsection
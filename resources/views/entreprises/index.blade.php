@extends('layout')

@section('title', 'Entreprises')

@section('content')
<main>
    <center><h1>Tu cherches une entreprise en particulier ?</h1></center>

    <div class="input-icons">
        <form class="search-container" action="{{ route('entreprises.search') }}" method="GET">
           <i class="fa-solid fa-magnifying-glass"></i>
           <input type="text" name="search" class="search-input" placeholder="Rechercher par entreprise, ville ou titre..." value="{{ request('search') }}">
           <button type="submit" class="search-button">Rechercher</button>
        </form>
    </div>

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
                                @if ((auth()->check() && (Auth::user()->role->Libelle === 'Pilote' || Auth::user()->role->Libelle === 'Administrateur')))
                                    <div class="edit-overlay">
                                        <i class="fa-solid fa-pen"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Formulaire pour uploader une nouvelle photo -->
                            @if ((auth()->check() && (Auth::user()->role->Libelle === 'Pilote' || Auth::user()->role->Libelle === 'Administrateur')))
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
                            @if ((auth()->check() && (Auth::user()->role->Libelle === 'Pilote' || Auth::user()->role->Libelle === 'Administrateur')))
                                <!-- Bouton de suppression -->
                                <form action="{{ route('entreprises.destroy', $entreprise->ID_Entreprise) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="modal__button modal__button--delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ?')">
                                        Supprimer
                                    </button>
                                </form>
                                <button class="modal__button" onclick='openEditEntrepriseModal(@json($entreprise))'>Éditer</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if ((auth()->check() && (Auth::user()->role->Libelle === 'Pilote' || Auth::user()->role->Libelle === 'Administrateur')))
                <!-- Carte vide pour ajouter une nouvelle entreprise -->
                <div class="card__box add-new-card">
                    <div class="card__product" onclick="openAddEntrepriseModal()">
                        <div class="add-new-icon">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <h3 class="add-new-text">Ajouter une entreprise</h3>
                    </div>
                </div>
            @endif

            <!-- Modal pour ajouter une nouvelle entreprise -->
            <div id="add-entreprise-modal" class="modal" style="display: none;">
                <div class="modal__card">
                    <i class="fa-solid fa-xmark modal__close" onclick="closeAddEntrepriseModal()"></i>
                    <h3 class="modal__name">Ajouter une nouvelle entreprise</h3>
                    <form action="{{ route('entreprises.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="Nom">Nom de l'entreprise</label>
                            <input type="text" id="Nom" name="Nom" required>
                        </div>
                        <div class="form-group">
                            <label for="Ville">Ville</label>
                            <input type="text" id="Ville" name="Ville" required>
                        </div>
                        <div class="form-group">
                            <label for="Telephone">Téléphone</label>
                            <input type="text" id="Telephone" name="Telephone" required>
                        </div>
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="email" id="Email" name="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="Site">Site Web</label>
                            <input type="url" id="Site" name="Site">
                        </div>
                        <div class="form-group">
                            <label for="Description">Description</label>
                            <textarea id="Description" name="Description" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Logo de l'entreprise</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn1">Ajouter</button>
                    </form>
                </div>
            </div>

            <!-- Modal pour modifier une entreprise -->
            <div id="edit-entreprise-modal" class="modal" style="display: none;">
                <div class="modal__card" style="margin: 20px;">
                    <center><h1>Modifier l'Entreprise</h1></center>
                    <i class="fa-solid fa-xmark modal__close" onclick="closeEditEntrepriseModal()"></i>
                    <article>
                        <form id="edit-entreprise-form" action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <h3>Informations Générales</h3>
                            <label for="edit-Nom">Nom de l'entreprise :</label><br>
                            <input type="text" id="edit-Nom" name="Nom" class="search-input" required><br><br>
                            
                            <label for="edit-Ville">Ville :</label><br>
                            <input type="text" id="edit-Ville" name="Ville" class="search-input" required><br><br>
                            
                            <label for="edit-Telephone">Téléphone :</label><br>
                            <input type="text" id="edit-Telephone" name="Telephone" class="search-input" required><br><br>
                            
                            <label for="edit-Email">Email :</label><br>
                            <input type="email" id="edit-Email" name="Email" class="search-input" required><br><br>
                            
                            <label for="edit-Site">Site Web :</label><br>
                            <input type="url" id="edit-Site" name="Site" class="search-input"><br><br>
                            
                            <label for="edit-Description">Description :</label><br>
                            <textarea id="edit-Description" name="Description" rows="4" class="search-input"></textarea><br><br>
                            
                            <label for="edit-image">Logo de l'entreprise :</label><br>
                            <input type="file" id="edit-image" name="image" accept="image/*"><br><br>
                            
                            <div class="submit-button">
                                <button type="submit" class="btn2">Modifier</button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
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

    function openAddEntrepriseModal() {
        document.getElementById('add-entreprise-modal').style.display = 'block';
    }

    function closeAddEntrepriseModal() {
        document.getElementById('add-entreprise-modal').style.display = 'none';
    }

    function openEditEntrepriseModal(entreprise) {
        console.log("openEditEntrepriseModal appelé pour :", entreprise);
        
        // Fermer tous les modals ouverts en retirant la classe active-modal
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('active-modal');
        });
        
        // Remplir les champs du formulaire avec les données de l'entreprise
        document.getElementById('edit-Nom').value = entreprise.Nom;
        document.getElementById('edit-Ville').value = entreprise.ville.Nom;
        document.getElementById('edit-Telephone').value = entreprise.Telephone;
        document.getElementById('edit-Email').value = entreprise.Email;
        document.getElementById('edit-Site').value = entreprise.Site || '';
        document.getElementById('edit-Description').value = entreprise.Description || '';
        
        // Mettre à jour l'action du formulaire
        document.getElementById('edit-entreprise-form').action = `/entreprises/${entreprise.ID_Entreprise}`;
        
        // Afficher le modal en modifiant le style et en ajoutant la classe active-modal
        const modal = document.getElementById('edit-entreprise-modal');
        modal.style.display = 'block';
        modal.classList.add('active-modal');
    }

    function closeEditEntrepriseModal() {
        document.getElementById('edit-entreprise-modal').style.display = 'none';
    }
</script>
<script src="{{ asset('js/cards.js') }}"></script>
@endsection
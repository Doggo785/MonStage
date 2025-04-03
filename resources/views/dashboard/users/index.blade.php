@extends('layout')

@section('title', 'Utilisateurs')

@section('content')
<section>
    <center><h1>Gestion des utilisateurs</h1></center>

    <div class="input-icons">
        <form class="search-container" action="{{ route('users.search') }}" method="GET">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" class="search-input" placeholder="Rechercher par nom, email ou rôle..." value="{{ request('search') }}">
            <button type="submit" class="search-button">Rechercher</button>
        </form>
    </div>

    <section class="container">
        <div class="card__container">
            @foreach ($users as $user)
                <div class="card__box">
                    <!-- CARD USER -->
                    <div class="card__product {{ $user->role->Libelle === 'Administrateur' ? 'border-admin' : ($user->role->Libelle === 'Pilote' ? 'border-pilot' : '') }}">
                        <div class="profile-container">
                            <!-- Conteneur de la photo de profil -->
                            <div class="profile-picture-wrapper">
                                <img id="profile-picture-{{ $user->ID_User }}" 
                                     src="{{ $user->pfp_path ? asset('storage/' . $user->pfp_path) : asset('assets/default-user.png') }}" 
                                     alt="Photo de {{ strtoupper($user->Nom) }} {{ ucfirst(strtolower($user->Prenom)) }}" class="card__img profile-picture">
                                
                                <!-- Logo de modification (visible uniquement pour Admin) -->
                                @if (auth()->check() && Auth::user()->role->Libelle === 'Administrateur')
                                    <div class="edit-overlay">
                                        <i class="fa-solid fa-pen"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- Informations de l'utilisateur -->
                        <div>
                            <h3 class="card__name">{{ strtoupper($user->Nom) }} {{ ucfirst(strtolower($user->Prenom)) }}</h3>
                            <span class="card__info">Email : {{ $user->Email }}</span><br>
                            <span class="card__info">Rôle : {{ $user->role->Libelle }}</span>
                        </div>
                    </div>
                </div>

                <!-- POPUP MODAL -->
                <div class="modal">
                    <div class="modal__card">
                        <i class="fa-solid fa-xmark modal__close"></i>
                        
                        <!-- Informations détaillées -->
                        <div>
                            <!-- Affichage de la photo de profil -->
                            <div class="modal__profile-picture">
                                <img id="modal-profile-picture-{{ $user->ID_User }}" 
                                     src="{{ $user->pfp_path ? asset('storage/' . $user->pfp_path) : asset('assets/default-user.png') }}" 
                                     alt="Photo de {{ strtoupper($user->Nom) }} {{ ucfirst(strtolower($user->Prenom)) }}" 
                                     class="modal__img">
                            </div>

                            <h3 class="modal__name">{{ strtoupper($user->Nom) }} {{ ucfirst(strtolower($user->Prenom)) }}</h3>
                            <p class="modal__info">
                                Email : {{ $user->Email }}<br>
                                Téléphone : {{ $user->Telephone ?? 'Non renseigné' }}<br>
                                Rôle : {{ $user->role->Libelle }}<br>

                                @if ($user->role->Libelle === 'Etudiant')
                                    <!-- Affichage du statut de recherche -->
                                    Statut : 
                                    @if ($user->etudiant && $user->etudiant->Statut_recherche == 1)
                                        En recherche de stage
                                    @elseif ($user->etudiant && $user->etudiant->Statut_recherche == 2)
                                        A trouvé un stage
                                    @else
                                        Non renseigné
                                    @endif
                                    <br>

                                    <!-- Affichage du nombre de candidatures -->
                                    Nombre de candidatures : {{ $user->candidatures->count() }}
                                @endif
                            </p>
                        </div>
                        <div class="modal__buttons">
                            @if (auth()->check() && (Auth::user()->role->Libelle === 'Administrateur' || Auth::user()->role->Libelle === 'Pilote'))
                                <!-- Bouton de suppression -->
                                <form action="{{ route('users.destroy', $user->ID_User) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="modal__button modal__button--delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                        Supprimer
                                    </button>
                                </form>
                                <button class="modal__button" onclick='openEditUserModal(@json($user))'>Éditer</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if (auth()->check() && (Auth::user()->role->Libelle === 'Administrateur' || Auth::user()->role->Libelle === 'Pilote'))
                <div class="card__box add-new-card">
                    <div class="card__product" onclick="openAddUserModal()">
                        <div class="add-new-icon">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <h3 class="add-new-text">Ajouter un utilisateur</h3>
                    </div>
                </div>
            @endif

            <!-- Modal pour ajouter un nouvel utilisateur -->
            <div id="add-user-modal" class="modal" style="display: none;">
                <div class="modal__card" style="margin: 20px;">
                    <center><h1>Ajouter un nouvel utilisateur</h1></center>
                    <i class="fa-solid fa-xmark modal__close" onclick="closeAddUserModal()"></i>
                    <article>
                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <h3>Informations Générales</h3>
                            <label for="name">Nom :</label><br>
                            <input type="text" id="name" name="name" class="search-input" required><br><br>
                            
                            <label for="prenom">Prénom :</label><br>
                            <input type="text" id="prenom" name="prenom" class="search-input" required><br><br>
                            
                            <label for="email">Email :</label><br>
                            <input type="email" id="email" name="email" class="search-input" required><br><br>
                            
                            <label for="telephone">Téléphone :</label><br>
                            <input type="text" id="telephone" name="telephone" class="search-input"><br><br>
                            
                            <label for="role">Rôle :</label><br>
                            <select id="role" name="role" class="search-input" required>
                                @if (auth()->check() && Auth::user()->role->Libelle === 'Pilote')
                                    <option value="{{ $roles->where('Libelle', 'Etudiant')->first()->ID_Role }}">Étudiant</option>
                                @else
                                    <option value="">Sélectionnez un rôle</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->ID_Role }}">{{ $role->Libelle }}</option>
                                    @endforeach
                                @endif
                            </select><br><br>
                            
                            <label for="profile_picture">Photo de profil :</label><br>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*"><br><br>
                            
                            <div class="submit-button">
                                <button type="submit" class="btn2">Ajouter</button>
                            </div>
                        </form>
                        <p style="margin-top: 15px; font-size: 14px; color: #555;">
                            <strong>Note :</strong> Le mot de passe par défaut pour le nouvel utilisateur est <strong>DefaultPassword!</strong>.
                        </p>
                    </article>
                </div>
            </div>

            <!-- Modal pour modifier un utilisateur -->
            <div id="edit-user-modal" class="modal" style="display: none;">
                <div class="modal__card" style="margin: 20px;">
                    <center><h1>Modifier l'utilisateur</h1></center>
                    <i class="fa-solid fa-xmark modal__close" onclick="closeEditUserModal()"></i>
                    <article>
                        <form id="edit-user-form" action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <h3>Informations Générales</h3>
                            <label for="edit-name">Nom :</label><br>
                            <input type="text" id="edit-name" name="name" class="search-input" required><br><br>
                            
                            <label for="edit-prenom">Prénom :</label><br>
                            <input type="text" id="edit-prenom" name="prenom" class="search-input" required><br><br>
                            
                            <label for="edit-email">Email :</label><br>
                            <input type="email" id="edit-email" name="email" class="search-input" required><br><br>
                            
                            <label for="edit-telephone">Téléphone :</label><br>
                            <input type="text" id="edit-telephone" name="telephone" class="search-input"><br><br>
                            
                            <label for="edit-role">Rôle :</label><br>
                            @if (auth()->check() && Auth::user()->role->Libelle === 'Pilote')
                                <!-- Champ désactivé pour les pilotes -->
                                <select id="edit-role" name="role" class="search-input" disabled>
                                    <option value="{{ $user->role->ID_Role }}">{{ $user->role->Libelle }}</option>
                                </select>
                                <input type="hidden" name="role" value="{{ $user->role->ID_Role }}">
                            @else
                                <!-- Champ modifiable pour les administrateurs -->
                                <select id="edit-role" name="role" class="search-input" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->ID_Role }}">{{ $role->Libelle }}</option>
                                    @endforeach
                                @endif
                            <br><br>
                            
                            <label for="edit-profile_picture">Photo de profil :</label><br>
                            <input type="file" id="edit-profile_picture" name="profile_picture" accept="image/*"><br><br>
                            
                            <div class="submit-button">
                                <button type="submit" class="btn2">Modifier</button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        </div>
           <!-- Liens de pagination -->
   <div class="pagination-links" style="text-align: center; margin-top: 20px;">
    {{ $users->links('pagination::bootstrap-4') }}
 </div>
    </section>
</section>

<script>
    function openAddUserModal() {
        document.getElementById('add-user-modal').style.display = 'block';
    }

    function closeAddUserModal() {
        document.getElementById('add-user-modal').style.display = 'none';
    }

    function openEditUserModal(user) {
        console.log("openEditUserModal appelé pour :", user);

        // Fermer tous les modals actifs
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('active-modal');
        });

        // Remplir les champs du formulaire
        document.getElementById('edit-name').value = user.Nom;
        document.getElementById('edit-prenom').value = user.Prenom;
        document.getElementById('edit-email').value = user.Email;
        document.getElementById('edit-telephone').value = user.Telephone || '';
        document.getElementById('edit-role').value = user.ID_Role;

        // Mettre à jour l'action du formulaire
        document.getElementById('edit-user-form').action = `/dashboard/users/${user.ID_User}`;

        // Afficher le modal
        const modal = document.getElementById('edit-user-modal');
        modal.style.display = 'block';
        modal.classList.add('active-modal');
    }

    function closeEditUserModal() {
        document.getElementById('edit-user-modal').style.display = 'none';
    }
</script>
<script src="{{ asset('js/cards.js') }}"></script>
@endsection
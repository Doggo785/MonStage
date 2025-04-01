@extends('layout')

@section('title', 'Dashboard')

@section('content')
<section>
    @if (Auth::user()->role->Libelle === 'Pilote')
        <center><h1>Espace Pilote</h1></center>
        <div class="container_compte">
            <div class="compte">
                <!-- Display the profile picture -->
                <img src="{{ Auth::user()->pfp_path ? asset('storage/' . Auth::user()->pfp_path) : asset('assets/default-avatar.png') }}" 
                     alt="Avatar" class="photo_compte profile-picture">
                <h3>{{ Auth::user()->name }}</h3>
                <h4>{{ Auth::user()->email }}</h4>

                <!-- Form to upload a new profile picture -->
                <form action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="profile_picture">Changer la photo de profil :</label><br>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required><br><br>
                    <button type="submit" class="btn1">Mettre à jour</button>
                </form>
            </div>
            <div class="whishlist">
                <h2>Élèves</h2>
                <div class="card">
                    <div class="content">
                        <div class="title">Lucas TOUJAS</div>
                        <div class="subtitle">CPI A2 Info</div>
                    </div>
                    <div class="star"><i class="fa-solid fa-clock-rotate-left"></i></div>
                </div>
                <br>
                <div class="card">
                    <div class="content">
                        <div class="title">Raphaël TOLANDAL</div>
                        <div class="subtitle">CPI A2 Info</div>
                    </div>
                    <div class="star"><i class="fa-solid fa-check"></i></div>
                </div>
            </div>
        </div>
    @elseif (Auth::user()->role->Libelle === 'Etudiant')
        <center><h1>Espace Étudiant</h1></center>
        <div class="container_compte">
            <div class="compte">
                <img src="{{ Auth::user()->pfp_path ? asset('storage/' . Auth::user()->pfp_path) : asset('assets/default-avatar.png') }}" 
                     alt="Avatar" class="photo_compte profile-picture">
                <h3>{{ Auth::user()->name }}</h3>
                <h4>{{ Auth::user()->email }}</h4>
                <h4>Statut : En attente</h4>

                <!-- Form to upload a new profile picture -->
                <form action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="profile_picture">Changer la photo de profil :</label><br>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required><br><br>
                    <button type="submit" class="btn1">Mettre à jour</button>
                </form>
            </div>
            <div class="whishlist">
                <h2>Whishlist</h2>
                <div class="card">
                    <div class="content">
                        <div class="title">Stage - Developer</div>
                        <div class="subtitle">TOTAL | PAU 64000</div>
                    </div>
                    <div class="star"><i class="fa-regular fa-star"></i></div>
                </div>
                <br>
                <div class="card">
                    <div class="content">
                        <div class="title">Stage - Data Management</div>
                        <div class="subtitle">LIDL | PAU 64000</div>
                    </div>
                    <div class="star"><i class="fa-regular fa-star"></i></div>
                </div>
            </div>
            <div class="offre">
                <h2>Candidature</h2>
                <div class="card">
                    <div class="content">
                        <div class="title">Stage - Administrateur Système</div>
                        <div class="subtitle">SAFRAN | BORDES 64510</div>
                    </div>
                    <div class="icon_bloc"><i class="fa-solid fa-xmark"></i></div>
                </div>
            </div>
        </div>
    @elseif (Auth::user()->role->Libelle === 'Administrateur')
        <center><h1>Espace Administrateur</h1></center>
        <div class="container_compte">
            <div class="compte">
                <img src="{{ Auth::user()->pfp_path ? asset('storage/' . Auth::user()->pfp_path) : asset('assets/default-avatar.png') }}" 
                     alt="Avatar" class="photo_compte profile-picture">
                <h3>{{ Auth::user()->name }}</h3>
                <h4>{{ Auth::user()->email }}</h4>

                <!-- Form to upload a new profile picture -->
                <form action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="profile_picture">Changer la photo de profil :</label><br>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required><br><br>
                    <button type="submit" class="btn1">Mettre à jour</button>
                </form>
            </div>
            <div class="admin-actions">
                <h2>Actions Administrateur</h2>
                <ul>
                    {{-- <li><a href="{{ route('users.index') }}">Gérer les utilisateurs</a></li> --}}
                    <li><a href="{{ route('offres.index') }}">Gérer les offres</a></li>
                    {{-- <li><a href="{{ route('logs.index') }}">Consulter les logs</a></li> --}}
                </ul>
            </div>
        </div>
    @else
        <center><h1>Accès refusé</h1></center>
        <p>Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
    @endif
</section>
@endsection
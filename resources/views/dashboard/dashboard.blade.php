@extends('layout')

@section('title', 'Espace Utilisateur')

@section('content')
<section>
    @if (Auth::user()->role->Libelle === 'Pilote')
        <center><h1>Espace Pilote</h1></center>
        <div class="container_compte">
            <div class="compte">
                <img src="{{ asset('assets/') }}" alt="Avatar" class="photo_compte">
                <h3>{{ Auth::user()->name }}</h3>
                <h4>{{ Auth::user()->email }}</h4>
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
                <img src="{{ asset('assets/') }}" alt="Avatar" class="photo_compte">
                <h3>{{ Auth::user()->name }}</h3>
                <h4>{{ Auth::user()->email }}</h4>
                <h4>Statut : En attente</h4>
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
    @else
        <center><h1>Accès refusé</h1></center>
        <p>Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
    @endif
</section>
@endsection
@extends('layout')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<section class="user-details-section">
    <h1>Détails de l'utilisateur</h1>
    <div class="user-details">
        <p><strong>Nom :</strong> {{ $user->Nom }}</p>
        <p><strong>Prénom :</strong> {{ $user->Prenom }}</p>
        <p><strong>Email :</strong> {{ $user->Email }}</p>
        <p><strong>Téléphone :</strong> {{ $user->Telephone ?? 'Non renseigné' }}</p>
        <p><strong>Rôle :</strong> {{ $user->role->Libelle }}</p>
    </div>
    <div class="user-actions">
        <a href="{{ route('users.index') }}" class="btn1">Retour</a>
        <a href="{{ route('users.edit', $user->ID_User) }}" class="btn1 btn-primary">Modifier</a>
    </div>
</section>
@endsection
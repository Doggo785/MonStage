@extends('layout')

@section('title', 'Modifier un utilisateur')

@section('content')
<section class="edit-user-section">
    <h1>Modifier un utilisateur</h1>
    <form action="{{ route('users.update', $user->ID_User) }}" method="POST" class="edit-user-form">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="Nom">Nom :</label>
            <input type="text" name="Nom" value="{{ $user->Nom }}" required>
        </div>
        <div class="form-group">
            <label for="Prenom">Prénom :</label>
            <input type="text" name="Prenom" value="{{ $user->Prenom }}" required>
        </div>
        <div class="form-group">
            <label for="Email">Email :</label>
            <input type="email" name="Email" value="{{ $user->Email }}" required>
        </div>
        <div class="form-group">
            <label for="Telephone">Téléphone :</label>
            <input type="text" name="Telephone" value="{{ $user->Telephone }}">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn1">Enregistrer</button>
            <a href="{{ route('users.index') }}" class="btn1 btn-secondary">Annuler</a>
        </div>
    </form>
</section>
@endsection
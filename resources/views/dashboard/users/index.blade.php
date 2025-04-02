
@extends('layout')

@section('title', 'Gestion des utilisateurs')

@section('content')
<section>
    <h1>Liste des utilisateurs</h1>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->Nom }}</td>
                    <td>{{ $user->Prenom }}</td>
                    <td>{{ $user->Email }}</td>
                    <td>{{ $user->role->Libelle }}</td>
                    <td>
                        <a href="{{ route('users.show', $user->ID_User) }}" class="btn1">Voir</a>
                        <a href="{{ route('users.edit', $user->ID_User) }}" class="btn1">Modifier</a>
                        <form action="{{ route('users.destroy', $user->ID_User) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn1 btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>
@endsection
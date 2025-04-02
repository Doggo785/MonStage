@extends('layout')

@section('title', 'Définir un nouveau mot de passe')

@section('content')
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }

    main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        position: relative; /* Empêche les mouvements */
    }

    h1 {
        font-size: 24px;
        color: #333333;
        margin-bottom: 20px;
        text-align: center;
    }

    label {
        font-size: 14px;
        color: #555555;
        margin-bottom: 5px;
        display: block;
    }

    .form-input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #cccccc;
        border-radius: 5px;
        font-size: 14px;
    }

    .form-input:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
</style>

<main>
    <form action="{{ route('password.reset') }}" method="POST" class="form-container">
        @csrf
        <h1>Définir un nouveau mot de passe</h1>
        <label for="password">Nouveau mot de passe :</label>
        <input type="password" id="password" name="password" class="form-input" required>

        <label for="password_confirmation">Confirmez le mot de passe :</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>

        <!-- Bouton avec style corrigé -->
        <button type="submit" class="btn1">Mettre à jour</button>
    </form>
</main>
@endsection
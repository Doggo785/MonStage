@extends('layout')

@section('title', 'Connexion')

@section('content')
<section>
   <center><h1>Connexion</h1></center>

   <div class="login_form">
      <form id="reply_form" action="{{ route('login') }}" method="POST">
         @csrf
         <h3>Email:</h3>
         <input name="email" type="email" size="30" placeholder="monemail@wahou.com" class="search-input" value="" required>
         <span class="field_result" id="email_result"></span>
         <p class="separator"></p>

         <h3>Mot de passe:</h3>
         <input type="password" id="password" name="password" class="search-input" placeholder="Entrer votre mot de passe..." required>
         <p class="separator"></p>

         <center>
            <div class="submit-button">
               <button type="submit" id="submit_form" class="btn">Connexion</button>
            </div>
            <p class="petit-lien"><a href="#">Mot de passe oublié</a></p>
         </center>
      </form>
   </div>
</section>
@endsection

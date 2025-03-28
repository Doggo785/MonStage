<header>
    <nav class="navbar">
       <div class="nav-left">
          <img alt="Logo" src="{{ asset('logo.png') }}">
          &nbsp;|&nbsp;<a href="{{ url('/') }}">Accueil</a>
          &nbsp;|&nbsp;<a href="{{ url('offres') }}">Offres</a>
          &nbsp;|&nbsp;<a href="{{ url('entreprises') }}">Entreprises</a>
       </div>
       <div class="nav-right">
          @auth
             <!-- Bouton de déconnexion -->
             <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn">
                   <i class="fa-solid fa-sign-out-alt"></i> Déconnexion
                </button>
             </form>
          @else
             <!-- Bouton pour accéder au compte -->
             <button class="btn">
                <i class="fa-solid fa-circle-user"></i> Compte
             </button>
          @endauth
       </div>
    </nav>
 </header>
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
             <!-- Bouton Compte avec menu déroulant -->
             <div class="dropdown">
                <button class="btn dropdown-toggle">
                   <i class="fa-solid fa-circle-user"></i> Compte
                </button>
                <div class="dropdown-menu">
                   <a href="{{ url('/dashboard') }}" class="dropdown-item">Dashboard</a>
                   <a href="{{ route('logout') }}" class="dropdown-item"
                      onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      Déconnexion
                   </a>
                   <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                   </form>
                </div>
             </div>
          @else
             <!-- Bouton Connexion avec conteneur -->
             <div class="dropdown">
                <a href="{{ route('login') }}" class="btn">
                   <i class="fa-solid fa-circle-user"></i> Connexion
                </a>
             </div>
          @endauth
       </div>
    </nav>
</header>
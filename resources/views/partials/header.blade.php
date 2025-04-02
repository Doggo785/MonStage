<header>
    <nav class="navbar">
       <div class="nav-left" style="display: flex; align-items: center;">
          <img alt="Logo" src="{{ asset('assets/logo.png') }}" style="width: auto; height: 50px; margin-right: 10px;">
          <span style="font-size: 20px; font-weight: bold; color: #333;">MonStage.fr</span>
       </div>
       <div class="nav-center">
          <a href="{{ url('/') }}" class="nav-link">Accueil</a>
          <a href="{{ url('offres') }}" class="nav-link">Offres</a>
          <a href="{{ url('entreprises') }}" class="nav-link">Entreprises</a>
       </div>
       <div class="nav-right">
          @auth
             <div class="dropdown">
                <button class="btn dropdown-toggle">
                   <i class="fa-solid fa-circle-user"></i> Compte
                </button>
                <div class="dropdown-menu">
                   <a href="{{ url('/dashboard') }}" class="dropdown-item">Dashboard</a>
                   @if (auth()->check() && Auth::user()->role->Libelle === 'Etudiant')
                      <a href="{{ route('wishlist.index') }}" class="dropdown-item">Ma Wishlist</a>
                   @endif
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
             <div class="dropdown">
                <a href="{{ route('login') }}" class="btn">
                   <i class="fa-solid fa-circle-user"></i> Connexion
                </a>
             </div>
          @endauth
       </div>
    </nav>
</header>
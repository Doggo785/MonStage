<header>
    <nav class="navbar">
       <div class="nav-left">
          <img alt="Logo" src="{{ asset('logo.png') }}">
          &nbsp;|&nbsp;<a href="{{ url('/') }}">Accueil</a>
          &nbsp;|&nbsp;<a href="{{ url('offres') }}">Offres</a>
          &nbsp;|&nbsp;<a href="{{ url('entreprises') }}">Entreprises</a>
       </div>
       <div class="nav-right">
          <button class="btn">
             <i class="fa-solid fa-circle-user"></i> Compte
          </button>
       </div>
    </nav>
 </header>
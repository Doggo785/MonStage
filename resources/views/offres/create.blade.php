@extends('layout')

@section('title', 'Créer une Offre de Stage')

@section('content')
<main>
    <div class="background_image"></div>
    <section style="margin-left: 20px; margin-right: 20px;">
        <h1>Créer une Offre de Stage</h1>
        <p class="ptit-texte">
            Remplissez le formulaire ci-dessous pour ajouter une nouvelle offre de stage sur le site.
        </p>

        <article>
            <form id="create-offre-form" action="{{ route('offres.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h3>Informations Générales</h3>
                <label for="titre">Titre de l'offre :</label><br>
                <input type="text" id="titre" name="titre" class="search-input" placeholder="Ex : Développeur Web" required><br><br>

                <label for="description">Description du poste :</label><br>
                <div class="ckeditor-container">
                    <textarea id="description" name="description" rows="5" class="ckeditor search-input" placeholder="Décrivez le poste..." required>
                        <h3>📌 Mission</h3>

                        <p>Exemple de mission</p>
                        
                        <h3>🔧 Technologies</h3>
                        
                        <ul>
                            <li>&nbsp;</li>
                        </ul>
                        
                        <h3>🎯 Profil</h3>
                        
                        <ul>
                            <li>&nbsp;</li>
                        </ul>
                        
                        <h3>📩 Contact</h3>
                        
                        <p><a href="mailto:rh@xyztech.com">rh@xyztech.com</a><br />
                        T&eacute;l : 01 23 45 67 89</p>
                    </textarea>
                </div><br><br>
                
                <label for="remuneration">Rémunération (en €) :</label><br>
                <input type="number" id="remuneration" name="remuneration" class="search-input" placeholder="Ex : 600" step="0.01" required><br>
                <span id="remuneration-error" style="color: red; display: none;">La rémunération doit être au moins de 600 €.</span><br><br>

                <label for="date_publication">Date de publication :</label><br>
                <input type="date" id="date_publication" name="date_publication" class="search-input" required><br><br>

                <label for="date_expiration">Date d'expiration :</label><br>
                <input type="date" id="date_expiration" name="date_expiration" class="search-input" required><br><br>

                <h3>Informations sur l'Entreprise</h3>
                <label for="entreprise">Nom de l'entreprise :</label><br>
                <select id="entreprise" name="entreprise" class="search-input" required>
                    <option value="">Sélectionnez une entreprise</option>
                    @foreach ($entreprises as $entreprise)
                        <option value="{{ $entreprise->ID_Entreprise }}">{{ $entreprise->Nom }}</option>
                    @endforeach
                </select><br><br>

                <label for="secteur">Secteur d'activité :</label><br>
                <select id="secteur" name="secteur" class="search-input" required>
                    <option value="">Sélectionnez un secteur</option>
                    @foreach ($secteurs as $secteur)
                        <option value="{{ $secteur->ID_Secteur }}">{{ $secteur->Nom }}</option>
                    @endforeach
                </select><br><br>

                <h3>Localisation</h3>
                <label for="ville">Ville :</label><br>
                <input type="text" id="ville-search" class="search-input" placeholder="Recherchez une ville..." autocomplete="off">
                <input type="hidden" id="ville" name="ville"> <!-- Champ caché pour l'ID de la ville -->
                <ul id="ville-results" class="dropdown-menu" style="display: none;"></ul><br><br>

                <div class="submit-button">
                    <button type="submit" class="btn2">Créer l'Offre</button>
                </div>
            </form>
        </article>
        <br>
    </section>
    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        // Initialisation de CKEditor
        CKEDITOR.replace('description');

        // Gestion de la recherche de ville
        document.getElementById('ville-search').addEventListener('input', function () {
            const query = this.value.trim(); // Supprime les espaces inutiles
            const results = document.getElementById('ville-results');

            if (query.length > 2) { // Ne lance la recherche que si l'utilisateur tape au moins 3 caractères
                fetch(`/villes/search?query=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur lors de la récupération des données');
                        }
                        return response.json();
                    })
                    .then(data => {
                        results.innerHTML = ''; // Vide les résultats précédents
                        results.style.display = 'block';

                        if (data.length === 0) {
                            const li = document.createElement('li');
                            li.textContent = 'Aucun résultat trouvé';
                            li.style.color = 'gray';
                            results.appendChild(li);
                        } else {
                            data.forEach(ville => {
                                const li = document.createElement('li');
                                li.textContent = ville.Nom;
                                li.dataset.id = ville.ID_Ville; // Utilise l'ID de la ville
                                li.addEventListener('click', function () {
                                    document.getElementById('ville-search').value = ville.Nom; // Affiche le nom dans le champ de recherche
                                    document.getElementById('ville').value = ville.ID_Ville; // Stocke l'ID dans le champ caché
                                    results.style.display = 'none';
                                });
                                results.appendChild(li);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        results.innerHTML = '<li style="color: red;">Erreur lors de la recherche</li>';
                        results.style.display = 'block';
                    });
            } else {
                results.style.display = 'none';
            }
        });
    </script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
</main>

@include('partials.footer')
@endsection
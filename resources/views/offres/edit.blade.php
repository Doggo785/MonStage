@extends('layout')

@section('title', 'Modifier une Offre de Stage')

@section('content')
<main>
    <div class="background_image"></div>
    <section style="margin-left: 20px; margin-right: 20px;">
        <h1>Modifier une Offre de Stage</h1>
        <p class="ptit-texte">
            Modifiez les informations ci-dessous pour mettre à jour l'offre de stage.
        </p>

        <article>
            <form id="edit-offre-form" action="{{ route('offres.update', ['id' => $offre->ID_Offre]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') <!-- Utilisation de la méthode PUT pour la mise à jour -->

                <h3>Informations Générales</h3>
                <label for="titre">Titre de l'offre :</label><br>
                <input type="text" id="titre" name="titre" class="search-input" placeholder="Ex : Développeur Web" value="{{ old('titre', $offre->Titre) }}" required><br><br>

                <label for="description">Description du poste :</label><br>
                <div class="ckeditor-container">
                    <textarea id="description" name="description" rows="5" class="ckeditor search-input" placeholder="Décrivez le poste..." required>
                        {{ old('description', $offre->Description) }}
                    </textarea>
                </div><br><br>
                
                <label for="remuneration">Rémunération (en €) :</label><br>
                <input type="number" id="remuneration" name="remuneration" class="search-input" placeholder="Ex : 600" step="0.01" value="{{ old('remuneration', $offre->Remuneration) }}" required><br>
                <span id="remuneration-error" style="color: red; display: none;">La rémunération doit être au moins de 600 €.</span><br><br>

                <label for="date_publication">Date de publication :</label><br>
                <input type="date" id="date_publication" name="date_publication" class="search-input" value="{{ old('date_publication', $offre->Date_publication) }}" required><br><br>

                <label for="date_expiration">Date d'expiration :</label><br>
                <input type="date" id="date_expiration" name="date_expiration" class="search-input" value="{{ old('date_expiration', $offre->Date_expiration) }}" required><br><br>

                <h3>Informations sur l'Entreprise</h3>
                <label for="entreprise">Nom de l'entreprise :</label><br>
                <select id="entreprise" name="entreprise" class="search-input" required>
                    <option value="">Sélectionnez une entreprise</option>
                    @foreach ($entreprises as $entreprise)
                        <option value="{{ $entreprise->ID_Entreprise }}" {{ $offre->ID_Entreprise == $entreprise->ID_Entreprise ? 'selected' : '' }}>
                            {{ $entreprise->Nom }}
                        </option>
                    @endforeach
                </select><br><br>

                <label for="secteur">Secteur d'activité :</label><br>
                <select id="secteur" name="secteur" class="search-input" required>
                    <option value="">Sélectionnez un secteur</option>
                    @foreach ($secteurs as $secteur)
                        <option value="{{ $secteur->ID_Secteur }}" {{ $offre->ID_Secteur == $secteur->ID_Secteur ? 'selected' : '' }}>
                            {{ $secteur->Nom }}
                        </option>
                    @endforeach
                </select><br><br>

                <h3>Localisation</h3>
                <label for="ville">Ville :</label><br>
                <input type="text" id="ville-search" class="search-input" placeholder="Recherchez une ville..." autocomplete="off" value="{{ $offre->ville->Nom ?? '' }}">
                <input type="hidden" id="ville" name="ville" value="{{ $offre->ID_Ville }}"> <!-- Champ caché pour l'ID de la ville -->
                <ul id="ville-results" class="dropdown-menu" style="display: none;"></ul><br><br>

                <div class="submit-button">
                    <button type="submit" class="btn2">Mettre à jour l'Offre</button>
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
</main>

@include('partials.footer')
@endsection
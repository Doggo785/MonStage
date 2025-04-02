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

                <h3>Compétences Requises</h3>
                <div id="competences-container" class="competences-container">
                    <!-- Les compétences sélectionnées seront affichées ici -->
                </div>
                <br>
                <label for="competence-search">Ajouter une compétence :</label><br>
                <input type="text" id="competence-search" class="search-input" placeholder="Recherchez une compétence..." autocomplete="off">
                <ul id="competence-results" class="dropdown-menu" style="display: none;"></ul>
                <input type="hidden" id="competences" name="competences"> <!-- Champ caché pour stocker les IDs des compétences -->
                <br><br>

                <div class="submit-button">
                    <button type="submit" class="btn2">Créer l'Offre</button>
                </div>
            </form>
        </article>
        <br>
    </section>
    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script src="{{ asset('js/ville-search.js') }}"></script>
    <script>
        // Initialisation de CKEditor
        CKEDITOR.replace('description');

        // Gestion de la recherche de compétences
        document.addEventListener('DOMContentLoaded', function () {
            const competenceSearch = document.getElementById('competence-search');
            const competenceResults = document.getElementById('competence-results');
            const competencesContainer = document.getElementById('competences-container');
            const competencesInput = document.getElementById('competences');
            let selectedCompetences = []; // Tableau pour stocker les IDs des compétences sélectionnées

            // Gestion de la recherche de compétences
            competenceSearch.addEventListener('input', function () {
                const query = this.value.trim();
                if (query.length > 0) { // Ne lance la recherche que si l'utilisateur tape au moins 3 caractères
                    fetch(`/competences/search?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            competenceResults.innerHTML = ''; // Vide les résultats précédents
                            competenceResults.style.display = 'block';

                            if (data.length === 0) {
                                const li = document.createElement('li');
                                li.textContent = 'Aucun résultat trouvé';
                                li.style.color = 'gray';
                                competenceResults.appendChild(li);
                            } else {
                                data.forEach(competence => {
                                    const li = document.createElement('li');
                                    li.textContent = competence.Libelle;
                                    li.dataset.id = competence.ID_Competence;
                                    li.addEventListener('click', function () {
                                        addCompetence(competence.ID_Competence, competence.Libelle);
                                        competenceResults.style.display = 'none';
                                        competenceSearch.value = '';
                                    });
                                    competenceResults.appendChild(li);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            competenceResults.innerHTML = '<li style="color: red;">Erreur lors de la recherche</li>';
                            competenceResults.style.display = 'block';
                        });
                } else {
                    competenceResults.style.display = 'none';
                }
            });

            // Ajouter une compétence sélectionnée
            function addCompetence(id, libelle) {
                if (!selectedCompetences.includes(id)) {
                    selectedCompetences.push(id);

                    // Créer une box pour la compétence
                    const badge = document.createElement('div');
                    badge.className = 'competence-badge';
                    badge.innerHTML = `
                        ${libelle}
                        <button class="remove-competence" data-id="${id}">&times;</button>
                    `;

                    // Ajouter un événement pour supprimer la compétence
                    badge.querySelector('.remove-competence').addEventListener('click', function () {
                        removeCompetence(id, badge);
                    });

                    competencesContainer.appendChild(badge);
                    updateCompetencesInput();
                }
            }

            // Supprimer une compétence sélectionnée
            function removeCompetence(id, badge) {
                selectedCompetences = selectedCompetences.filter(compId => compId !== id);
                badge.remove();
                updateCompetencesInput();
            }

            // Mettre à jour le champ caché avec les IDs des compétences
            function updateCompetencesInput() {
                competencesInput.value = selectedCompetences.join(',');
            }
        });
    </script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
</main>

@include('partials.footer')
@endsection
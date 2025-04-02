function setupVilleSearch(inputId, resultsId, hiddenId) {
    let inputEl = document.getElementById(inputId);
    const resultsEl = document.getElementById(resultsId);
    const hiddenEl = document.getElementById(hiddenId);
    
    if (!inputEl) return; // si l'élément n'existe pas, on quitte

    // Réinitialiser le champ en le remplaçant par une copie pour supprimer d'éventuels listeners existants
    const newInputEl = inputEl.cloneNode(true);
    inputEl.parentNode.replaceChild(newInputEl, inputEl);
    inputEl = newInputEl;

    inputEl.addEventListener('input', function () {
        const query = this.value.trim();
        if (query.length > 2) {
            fetch(`/villes/search?query=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la récupération des données');
                    }
                    return response.json();
                })
                .then(data => {
                    resultsEl.innerHTML = '';
                    resultsEl.style.display = 'block';
                    if (data.length === 0) {
                        const li = document.createElement('li');
                        li.textContent = 'Aucun résultat trouvé';
                        li.style.color = 'gray';
                        resultsEl.appendChild(li);
                    } else {
                        data.forEach(ville => {
                            const li = document.createElement('li');
                            li.textContent = ville.Nom;
                            li.dataset.id = ville.ID_Ville;
                            li.addEventListener('click', function () {
                                inputEl.value = ville.Nom;
                                hiddenEl.value = ville.ID_Ville;
                                resultsEl.style.display = 'none';
                            });
                            resultsEl.appendChild(li);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    resultsEl.innerHTML = '<li style="color: red;">Erreur lors de la recherche</li>';
                    resultsEl.style.display = 'block';
                });
        } else {
            resultsEl.style.display = 'none';
        }
    });
}

// Initialisation pour le formulaire d'ajout
setupVilleSearch('ville-search', 'ville-results', 'ville');
// Initialisation pour le formulaire de modification
setupVilleSearch('ville-search-edit', 'ville-results-edit', 'ville-edit');
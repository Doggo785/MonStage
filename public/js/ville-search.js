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
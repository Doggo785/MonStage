document.getElementById('create-offre-form').addEventListener('submit', function (e) {
    const remunerationInput = document.getElementById('remuneration');
    const remunerationError = document.getElementById('remuneration-error');

    if (parseFloat(remunerationInput.value) < 600) {
        e.preventDefault(); // Empêche la soumission du formulaire
        remunerationInput.style.border = '2px solid red'; // Met la bordure en rouge
        remunerationError.style.display = 'inline'; // Affiche le message d'erreur
    } else {
        remunerationInput.style.border = ''; // Réinitialise la bordure
        remunerationError.style.display = 'none'; // Cache le message d'erreur
    }
});
document.addEventListener("DOMContentLoaded", function () {
    const loaderContainer = document.getElementById('loader-container');
    const content = document.getElementById('content');

    if (loaderContainer && content) {
        // Simule un délai pour voir l'effet (peut être ajusté ou supprimé)
        setTimeout(() => {
            loaderContainer.style.display = 'none'; // Cache le loader
            content.style.display = 'block'; // Affiche le contenu
            content.style.opacity = 0;

            // Animation de fade-in
            let opacity = 0;
            const fadeIn = setInterval(() => {
                opacity += 0.05;
                content.style.opacity = opacity;
                if (opacity >= 1) clearInterval(fadeIn);
            }, 50);
        }, 1000); // 1 seconde de délai
    } else {
        console.error("Les éléments #loader-container ou #content sont introuvables dans le DOM.");
    }
});
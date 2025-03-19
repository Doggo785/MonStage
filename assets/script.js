
	//EMAIL
    	document.getElementById('email').addEventListener('input', function () {
		const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
		const emailField = this;
		const errorMsg = document.getElementById('email-error');

		if (!emailPattern.test(emailField.value)) {
			errorMsg.style.display = 'inline';
		} else {
			errorMsg.style.display = 'none';
		}
	});

	// //CV
	// document.getElementById('cv').addEventListener('change', function () {
	// 	const file = this.files[0];
	// 	const allowedExtensions = ['pdf', 'doc', 'docx', 'odt', 'rtf', 'jpg', 'png'];
	// 	const maxSize = 2 * 1024 * 1024; // 2 Mo en octets
	// 	const fileNameDisplay = document.getElementById('file-name');
	// 	const errorDisplay = document.getElementById('file-error');

	// 	if (file) {
	// 		const fileExtension = file.name.split('.').pop().toLowerCase();
	// 		if (!allowedExtensions.includes(fileExtension)) {
	// 			errorDisplay.textContent = "Format de fichier non autorisé !";
	// 			errorDisplay.style.display = "block";
	// 			this.value = ""; // Réinitialise l'input
	// 			return;
	// 		}

	// 		if (file.size > maxSize) {
	// 			errorDisplay.textContent = "Le fichier dépasse 2 Mo !";
	// 			errorDisplay.style.display = "block";
	// 			this.value = ""; // Réinitialise l'input
	// 			return;
	// 		}

	// 		errorDisplay.style.display = "none";
	// 		fileNameDisplay.textContent = "Fichier sélectionné : " + file.name;
	// 	}
	// });

	//POSTULER
	document.getElementById('idForm').addEventListener('submit', function (event) {
		const surname = document.getElementById('surname').value.trim();
		const firstname = document.getElementById('firstname').value.trim();
		const email = document.getElementById('email').value.trim();
        const tel = document.getElementById('tel').value.trim();
		const message = document.getElementById('message').value.trim();
		const errorDisplay = document.getElementById('form-error');

		if (!surname || !firstname || !email || !message || !tel) {
			event.preventDefault(); // Empêche l'envoi du formulaire
			errorDisplay.textContent = "Tous les champs doivent être remplis !";
			errorDisplay.style.display = "block";
		} else {
			errorDisplay.style.display = "none"; // Cache le message d'erreur si tout est bon
		}
	});

	//SCROLL TOP
	window.addEventListener('scroll', function () {
		const button = document.getElementById('backToTop');
		if (window.scrollY > 300) { // Affiche le bouton après 300px de scroll
			button.style.display = "block";
		} else {
			button.style.display = "none";
		}
	});

	function scrollToTop() {
		window.scrollTo({ top: 0, behavior: 'smooth' }); // Scroll fluide vers le haut
	}

	//MENU ON MOBILE
	function toggleMenu() {
    const menuLinks = document.querySelector('.display');
    menuLinks.classList.toggle('active');
}
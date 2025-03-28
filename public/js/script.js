
/* Attendre la fin du chargement de la page pour dérouler les fonctions */
document.addEventListener("DOMContentLoaded", () => {
	//Récupération des champs
	var form = document.querySelector("#login_form");
	var lastname = document.getElementsByName("lastname")[0];
	var firstname = document.getElementsByName("firstname")[0];
	var phonenumber = document.getElementsByName("phonenumber")[0];
	var email = document.getElementsByName("email")[0];
	var file = document.getElementsByName("cv")[0];
	let top_button = document.getElementById("top_button");
	let submit_form = document.getElementById("submit_form");
	
  
	// Création des événements pour les interactions
	lastname.addEventListener("change", strtoupper, false);
	firstname.addEventListener("change", check_value, false);
	phonenumber.addEventListener("change", check_value, false);
	email.addEventListener("change", check_value, false);
	file.addEventListener("change", file_validation, false);
	top_button.addEventListener("click", go_top, false);
	submit_form.addEventListener("click", checkForm, false);
	
	
	// 1. Fonction permettant de mettre en majuscule le contenu passé en paramètre
	function strtoupper(evt)
	{
		evt.currentTarget.value = evt.currentTarget.value.toUpperCase();
	}
	
	// 2. Fonction permettant de vérifier que le format saisie est conforme aux attentes
	function check_value(evt)
	{
		var field = evt.currentTarget.name; // Nom du champs
		var value = evt.currentTarget.value; // Valeur
		var regex_pattern;
		var target_field;
		
		switch (field)
		{
			case 'phonenumber':
				regex_pattern = /^[0-9]{10}$/;
				target_field = "phone_result";
				break;
				
			case 'email':
				regex_pattern = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/;
				target_field = "email_result";
				break;
		}
		
		var regex = new RegExp(regex_pattern);	
		document.getElementById(target_field).innerText  = regex.test(value) ? "" : "Mauvais format"; // Test de la validité du champs

	}
	
	
	// 3. Fonction permettant de vérifier que la taille du fichier respecte les attentes
	function file_validation(evt)
	{
		var result = '';
		var maxFileSizeMo = 2;	
		var fileInput = evt.target;
		var fileSize = (fileInput.files[0].size / 1024 / 1024).toFixed(2);
		if(fileSize > maxFileSizeMo)
		{
			fileInput.value = '';
			result = "Taille du fichier trop importante.";
		}

		document.getElementById('file_result').innerText = result;
		
	}
	
	//4. Fonction permettant de vérifier avant l'avant du formulaire que tous les champs sont correctements remplis
	function checkForm(e)
	{ 
		if( form.checkValidity() ) // Cette fonction est un standard HTML, elle permet de vérifier l'état des champs contrôlés par le navigateur (required, type...)
		{
			e.preventDefault(); // Permet de prendre le contrôle sur l'envoi du formulaire
			
			const t_fields = ["phone_result", "email_result"]; // Liste des id des champs de résultat
			
			for (i = 0; i < t_fields.length; i++) // Parcours du tableau (il est également possible d'utiliser la méthode forEach)
			{
				if( document.getElementById(t_fields[i]).innerText != '' ) // Champs vide = pas d'erreur
				{
					document.getElementById('submit_result').classList.remove("validate"); // Suppression de la classe de validation 
					document.getElementById('submit_result').innerText  = "Un ou plusieurs champs sont incorrects."; // Message de retour
					return;
				}
			} 
			
			document.getElementById('submit_result').classList.add("validate"); // Ajout de la classe de validation pour passer le texte en vert
			document.getElementById('submit_result').innerText  = "Compte créer !"; // Message de retour
			form.reset(); 
			
		}
		
	};
	
	
	//5. Fonction permettant de revenir en haut de la page
	function go_top()
	{
		document.body.scrollTop = 0;
		document.documentElement.scrollTop = 0;
	}

	window.onscroll = function()
	{ 
		// Afficher le bouton lorsque l'utilisateur scroll à plus de 150px en partant du haut de la page
		top_button.style.display = (document.body.scrollTop > 150 || document.documentElement.scrollTop > 150) ? 'block' : 'none';
	}
	
	//6. Affichage du menu mobile lors du click sur le burger
	var sidenav = document.getElementById("sideNav");
	var burgerNav = document.getElementById("burgerNav");
	var closeBtn = document.getElementById("closeBtn");

	burgerNav.onclick = openNav;
	closeBtn.onclick = closeNav;

	/* Affichage du menu latéral */
	function openNav() {
	  sidenav.classList.add("active");
	}

	/* Fermeture du menu latéral */
	function closeNav() {
	  sidenav.classList.remove("active");
	}
		
	
});
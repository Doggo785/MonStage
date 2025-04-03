@extends('layout')

@section('title', 'Mentions légales')

@section('content')
<section class="mentions-legales">
    <h1>Mentions légales</h1>
    
    <div class="section">
        <h2>Éditeur du site</h2>
        <p>
            Ce site est édité par :<br>
            <strong>Doggo.corp</strong><br>
            Adresse : 123 Rue Exemple, 75000 Paris, France<br>
            Email : <a href="mailto:contact@doggo.corp.com">contact@doggo.corp.com</a><br>
            Téléphone : +33 1 23 45 67 89<br>
            Numéro SIRET : 123 456 789 00012<br>
            Directeur de la publication : Jean Dupont
        </p>
    </div>

    <div class="section">
        <h2>Hébergeur</h2>
        <p>
            Le site est hébergé par :<br>
            <strong>Hébergement Exemple</strong><br>
            Adresse : 456 Avenue Hébergeur, 75000 Paris, France<br>
            Téléphone : +33 1 98 76 54 32<br>
            Site web : <a href="https://www.hebergement-exemple.com" target="_blank">www.hebergement-exemple.com</a>
        </p>
    </div>

    <div class="section">
        <h2>Propriété intellectuelle</h2>
        <p>
            Le contenu de ce site (textes, images, vidéos, logos, etc.) est la propriété exclusive de <strong>Doggo.corp</strong>, sauf mention contraire. 
            Toute reproduction, distribution, modification, adaptation, retransmission ou publication, même partielle, de ces différents éléments est strictement interdite sans l'accord écrit préalable de <strong>Doggo.corp</strong>.
        </p>
    </div>

    <div class="section">
        <h2>Responsabilité</h2>
        <p>
            <strong>Doggo.corp</strong> s'efforce de fournir des informations aussi précises que possible sur ce site. Cependant, nous ne pouvons garantir l'exactitude, la complétude ou l'actualité des informations diffusées. 
            L'utilisateur est seul responsable de l'utilisation des informations disponibles sur ce site.
        </p>
    </div>

    <div class="section">
        <h2>Protection des données personnelles</h2>
        <p>
            Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition concernant vos données personnelles. 
            Pour exercer ces droits, vous pouvez nous contacter à l'adresse suivante : <a href="mailto:contact@doggo.corp.com">contact@doggo.corp.com</a>.
        </p>
    </div>

    <div class="section">
        <h2>Cookies</h2>
        <p>
            Ce site utilise des cookies pour améliorer l'expérience utilisateur et analyser le trafic. En continuant à naviguer sur ce site, vous acceptez l'utilisation de cookies. 
            Vous pouvez configurer votre navigateur pour refuser les cookies ou être alerté lorsqu'un cookie est installé.
        </p>
    </div>

    <div class="section">
        <h2>Liens externes</h2>
        <p>
            Ce site peut contenir des liens vers d'autres sites web. <strong>Doggo.corp</strong> ne peut être tenu responsable du contenu ou des pratiques de confidentialité de ces sites tiers.
        </p>
    </div>

    <div class="section">
        <h2>Contact</h2>
        <p>
            Pour toute question ou demande d'information, vous pouvez nous contacter à l'adresse suivante : <a href="mailto:contact@doggo.corp.com">contact@doggo.corp.com</a>.
        </p>
    </div>
</section>
@endsection
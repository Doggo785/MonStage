# MonStage

## Présentation du projet
MonStage est une application web conçue pour faciliter la recherche de stages pour les étudiants. Elle regroupe différentes offres de stage et permet de stocker les données des entreprises qui recherchent des stagiaires. L'application vise à simplifier le processus de recherche de stage en offrant une interface conviviale et des fonctionnalités adaptées aux différents profils d'utilisateurs.

## Fonctionnalités principales
- **Recherche de stages** : Parcourez les offres de stage disponibles selon vos critères.
- **Gestion des candidatures** : Postulez directement aux offres et suivez vos candidatures.
- **Gestion des entreprises** : Les entreprises peuvent publier des offres et gérer leurs informations.
- **Interface utilisateur intuitive** : Une expérience utilisateur fluide et adaptée à tous les profils.

## Prérequis
- **PHP** : Version 8.2 ou supérieure.
- **Composer** : Gestionnaire de dépendances PHP.
- **Base de données** : MySQL ou MariaDB.
- **Node.js** : Pour la gestion des assets front-end.

## Installation
1. Clonez le dépôt :
   ```bash
   git clone https://github.com/votre-utilisateur/monstage.git
   cd monstage
   ```

2. Installez les dépendances PHP :
   ```bash
   composer install
   ```

3. Installez les dépendances front-end :
   ```bash
   npm install
   ```

4. Configurez le fichier `.env` :
   Copiez le fichier `.env.example` en `.env` et configurez les variables d'environnement (base de données, clé d'application, etc.).

5. Générez la clé d'application :
   ```bash
   php artisan key:generate
   ```

6. Exécutez les migrations et seeders :
   ```bash
   php artisan migrate --seed
   ```

7. (Optionnel) **Peuplez la base de données avec un script Python** :  
   Si vous souhaitez ajouter des données supplémentaires pour tester l'application, un script Python est disponible. Assurez-vous d'avoir Python installé, puis suivez les étapes suivantes :
   1. Installez les dépendances Python nécessaires à l'aide du fichier `requirements.txt` :
      ```bash
      pip install -r scripts/requirements.txt
      ```
   2. Exécutez le script pour peupler la base de données. Ce script inclut une interface graphique dans la console pour faciliter son utilisation :
      ```bash
      python scripts/DB_Import.py
      ```

8. Lancez le serveur de développement :
   ```bash
   php artisan serve
   ```

## Contribution
Les contributions sont les bienvenues ! Veuillez suivre les étapes suivantes :
1. Forkez le projet.
2. Créez une branche pour votre fonctionnalité ou correction de bug (`git checkout -b feature/ma-fonctionnalite`).
3. Effectuez vos modifications et validez-les (`git commit -m "Ajout de ma fonctionnalité"`).
4. Poussez vos modifications (`git push origin feature/ma-fonctionnalite`).
5. Ouvrez une Pull Request.

## Licence
Ce projet est sous licence [GNU General Public License (GPL) version 3](LICENSE).

## Auteurs
- **Lucas TOUJAS - Raphaël TOLANDAL - Stéphane PLATHEY--BADIN**
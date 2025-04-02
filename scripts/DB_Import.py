import pandas as pd
import mysql.connector
from mysql.connector import Error
import os
from rich.console import Console
from rich.table import Table
from rich.prompt import Prompt
from tqdm import tqdm
from dotenv import load_dotenv

load_dotenv()

console = Console()

# Configuration de la BDD
DB_CONFIG = {
    'host': os.getenv('DB_HOST'),
    'user': os.getenv('DB_USERNAME'),
    'password': os.getenv('DB_PASSWORD'),
    'database': os.getenv('DB_DATABASE')
}

script_dir = os.path.dirname(__file__)
csv_file_path = os.path.join(script_dir, 'cities.csv')

# Connexion globale à la BDD
conn = mysql.connector.connect(
    **DB_CONFIG
)
cursor = conn.cursor()

def create_tables():
    """Crée les tables si elles n'existent pas et insère des données par défaut."""
    queries = [
        ("Role", """
        CREATE TABLE IF NOT EXISTS Role (
            ID_Role INT AUTO_INCREMENT PRIMARY KEY,
            Libelle VARCHAR(25) UNIQUE
        )
        """),
        ("Secteur", """
        CREATE TABLE IF NOT EXISTS Secteur (
            ID_Secteur INT AUTO_INCREMENT PRIMARY KEY,
            Nom VARCHAR(50) NOT NULL
        )
        """),
        ("Competence", """
        CREATE TABLE IF NOT EXISTS Competence (
            ID_Competence INT AUTO_INCREMENT PRIMARY KEY,
            Libelle VARCHAR(50) NOT NULL
        )
        """),
        ("Statuts_Candidature", """
        CREATE TABLE IF NOT EXISTS Statuts_Candidature (
            ID_Statut INT AUTO_INCREMENT PRIMARY KEY,
            Libelle VARCHAR(50) NOT NULL
        )
        """),
        ("Region", """
        CREATE TABLE IF NOT EXISTS Region (
            ID_Region INT AUTO_INCREMENT PRIMARY KEY,
            Nom VARCHAR(50) UNIQUE
        )
        """),
        ("Utilisateur", """
        CREATE TABLE IF NOT EXISTS Utilisateur (
            ID_User INT AUTO_INCREMENT PRIMARY KEY,
            Password VARCHAR(255) NOT NULL,
            Nom VARCHAR(50) NOT NULL,
            Prenom VARCHAR(50) NOT NULL,
            Telephone VARCHAR(20),
            Email VARCHAR(50) UNIQUE NOT NULL,
            ID_Role INT NOT NULL,
            FOREIGN KEY(ID_Role) REFERENCES Role(ID_Role)
        )
        """),
        ("Etudiant", """
        CREATE TABLE IF NOT EXISTS Etudiant (
            ID_User INT PRIMARY KEY,
            Statut_recherche VARCHAR(50),
            FOREIGN KEY(ID_User) REFERENCES Utilisateur(ID_User)
        )
        """),
        ("Ville", """
        CREATE TABLE IF NOT EXISTS Ville (
            ID_Ville INT AUTO_INCREMENT PRIMARY KEY,
            CP VARCHAR(10),
            Nom VARCHAR(50),
            ID_Region INT,
            FOREIGN KEY(ID_Region) REFERENCES Region(ID_Region)
        )
        """),
        ("Entreprise", """
        CREATE TABLE IF NOT EXISTS Entreprise (
            ID_Entreprise INT AUTO_INCREMENT PRIMARY KEY,
            Nom VARCHAR(50) NOT NULL,
            Telephone VARCHAR(20),
            Email VARCHAR(50) NOT NULL,
            Site VARCHAR(50) NOT NULL,
            Description VARCHAR(250) NOT NULL,
            ID_Ville INT NOT NULL,
            FOREIGN KEY(ID_Ville) REFERENCES Ville(ID_Ville)
        )
        """),
        ("Avis", """
        CREATE TABLE IF NOT EXISTS Avis (
            ID_Avis INT AUTO_INCREMENT PRIMARY KEY,
            Note DECIMAL(3,1),
            ID_Entreprise INT NOT NULL,
            ID_User INT NOT NULL,
            FOREIGN KEY(ID_Entreprise) REFERENCES Entreprise(ID_Entreprise),
            FOREIGN KEY(ID_User) REFERENCES Utilisateur(ID_User)
        )
        """),
        ("Offre", """
        CREATE TABLE IF NOT EXISTS Offre (
            ID_Offre INT AUTO_INCREMENT PRIMARY KEY,
            Titre VARCHAR(50) NOT NULL,
            Description VARCHAR(1000) NOT NULL,
            Remuneration DECIMAL(7,2),
            Etat BOOLEAN NOT NULL,
            Date_publication DATE NOT NULL,
            Date_expiration DATE NOT NULL,
            ID_Secteur INT NOT NULL,
            ID_Ville INT NOT NULL,
            ID_Entreprise INT NOT NULL,
            FOREIGN KEY(ID_Secteur) REFERENCES Secteur(ID_Secteur),
            FOREIGN KEY(ID_Ville) REFERENCES Ville(ID_Ville),
            FOREIGN KEY(ID_Entreprise) REFERENCES Entreprise(ID_Entreprise)
        )
        """),
        ("Candidature", """
        CREATE TABLE IF NOT EXISTS Candidature (
            ID_User INT NOT NULL,
            ID_Offre INT NOT NULL,
            ID_Statut INT NOT NULL,
            Date_postule DATE NOT NULL,
            LM_Path VARCHAR(255),
            CV_Path VARCHAR(255),
            PRIMARY KEY(ID_User, ID_Offre),
            FOREIGN KEY(ID_User) REFERENCES Utilisateur(ID_User),
            FOREIGN KEY(ID_Offre) REFERENCES Offre(ID_Offre),
            FOREIGN KEY(ID_Statut) REFERENCES Statuts_Candidature(ID_Statut)
        )
        """),
        ("Offres_Competences", """
        CREATE TABLE IF NOT EXISTS Offres_Competences (
            ID_Offre INT NOT NULL,
            ID_Competence INT NOT NULL,
            PRIMARY KEY(ID_Offre, ID_Competence),
            FOREIGN KEY(ID_Offre) REFERENCES Offre(ID_Offre),
            FOREIGN KEY(ID_Competence) REFERENCES Competence(ID_Competence)
        )
        """),
        ("Wishlist", """
        CREATE TABLE IF NOT EXISTS Wishlist (
            ID_User INT NOT NULL,
            ID_Offre INT NOT NULL,
            Date_ajout DATE NOT NULL,
            PRIMARY KEY(ID_User, ID_Offre),
            FOREIGN KEY(ID_User) REFERENCES Utilisateur(ID_User),
            FOREIGN KEY(ID_Offre) REFERENCES Offre(ID_Offre)
        )
        """)
    ]
    
    try:
        # Création des tables
        for table_name, query in queries:
            cursor.execute(query)
            console.print(f"Table '{table_name}' vérifiée ou créée avec succès.", style="bold green")
        conn.commit()

        # Insertion des offres par défaut
        offres_query = """
        INSERT IGNORE INTO Offre (
            Titre, Description, Remuneration, Etat, Date_publication, Date_expiration, ID_Entreprise, ID_Secteur, ID_Ville
        ) VALUES 
        ('Développeur Backend', '<h3>📌 Mission</h3><p>Développement d\\'API sécurisées</p><h3>🔧 Technologies</h3><ul><li>Python, Django, PostgreSQL</li></ul><h3>🎯 Profil</h3><ul><li>Connaissance en bases de données</li></ul><h3>📩 Contact</h3><p><a href="mailto:recrutement@devtech.com">recrutement@devtech.com</a></p>', 800.00, 1, '2025-03-31', '2025-07-31', 5, 2, 1200),
        ('Analyste Cybersécurité', '<h3>📌 Mission</h3><p>Audit et sécurisation des systèmes</p><h3>🔧 Technologies</h3><ul><li>SIEM, IDS/IPS, Firewall</li></ul><h3>🎯 Profil</h3><ul><li>Connaissances en pentesting</li></ul><h3>📩 Contact</h3><p><a href="mailto:jobs@securecorp.com">jobs@securecorp.com</a></p>', 950.00, 1, '2025-03-31', '2025-08-20', 6, 4, 9876),
        ('Technicien Réseau', '<h3>📌 Mission</h3><p>Maintenance et configuration des réseaux</p><h3>🔧 Technologies</h3><ul><li>Cisco, VLAN, VPN</li></ul><h3>🎯 Profil</h3><ul><li>Compétences en routage et switching</li></ul><h3>📩 Contact</h3><p><a href="mailto:tech@networking.com">tech@networking.com</a></p>', 700.00, 1, '2025-03-31', '2025-06-30', 3, 3, 25678),
        ('Développeur Front-End', '<h3>:pushpin: Mission</h3><p>Développement d\\'interfaces web modernes</p><h3>:wrench: Technologies</h3><ul><li>React, Tailwind CSS</li></ul><h3>:dart: Profil</h3><ul><li>Bonne maîtrise du JavaScript</li></ul><h3>:envelope_with_arrow: Contact</h3><p><a href="mailto:contact@webcorp.com">contact@webcorp.com</a><br />Tél : 01 45 78 90 12</p>', 750.00, 1, '2025-03-31', '2025-06-30', 2, 1, 4),
        ('Administrateur Systèmes & Réseaux', '<h3>:pushpin: Mission</h3><p>Gestion des infrastructures réseau et serveurs</p><h3>:wrench: Technologies</h3><ul><li>Linux, Docker, Ansible</li></ul><h3>:dart: Profil</h3><ul><li>Compétences en administration système</li></ul><h3>:envelope_with_arrow: Contact</h3><p><a href="mailto:jobs@infra-tech.com">jobs@infra-tech.com</a><br />Tél : 02 98 76 54 32</p>', 850.00, 1, '2025-03-31', '2025-08-01', 3, 3, 5),
        ('Développeur Full-Stack', '<h3>:pushpin: Mission</h3><p>Conception et développement d\\'applications web</p><h3>:wrench: Technologies</h3><ul><li>Node.js, Vue.js, PostgreSQL</li></ul><h3>:dart: Profil</h3><ul><li>Expérience en développement backend et frontend</li></ul><h3>:envelope_with_arrow: Contact</h3><p><a href="mailto:recrutement@startup-dev.com">recrutement@startup-dev.com</a><br />Tél : 03 21 65 87 45</p>', 900.00, 1, '2025-03-31', '2025-09-15', 4, 2, 2)
        """
        cursor.execute(offres_query)
        console.print("Toutes les offres par défaut ont été insérées avec succès.", style="bold green")

        # Insertion des compétences associées
        competences_query = """
        INSERT IGNORE INTO Offres_Competences (ID_Offre, ID_Competence) VALUES 
        (1, 2), (1, 5), (1, 12), 
        (2, 1), (2, 4), (2, 18), 
        (3, 6), (3, 9), (3, 22), 
        (4, 7), (4, 10), (4, 15), 
        (5, 8), (5, 11), (5, 20), 
        (6, 3), (6, 14), (6, 19), 
        (4, 7), (4, 10), (4, 15), 
        (5, 8), (5, 11), (5, 20), 
        (6, 3), (6, 14), (6, 19)
        """
        cursor.execute(competences_query)
        console.print("Compétences associées aux offres insérées avec succès.", style="bold green")

        conn.commit()
    except Error as e:
        console.print(f"Erreur lors de la création des tables ou de l'insertion des données : {e}", style="bold red")

def import_data(table):
    """Importe les données du CSV vers la BDD"""
    try:
        if table == 'Region' or table == 'Ville':
            # Lire le CSV
            df = pd.read_csv(csv_file_path, sep=',', dtype={'zip_code': str})
            df['region_geojson_name'] = df['region_geojson_name'].str.strip()

            if table == 'Region':
                # Importer les Régions
                regions = df[['region_geojson_name']].drop_duplicates()
                for row in tqdm(regions.itertuples(), total=len(regions), desc=f"Import {table}"):
                    cursor.execute(
                        "INSERT IGNORE INTO Region (Nom) VALUES (%s)",
                        (row.region_geojson_name,)
                    )
                conn.commit()
                console.print(f"{len(regions)} régions insérées", style="bold green")
            elif table == 'Ville':
                # Importer les Villes
                cursor.execute("SELECT ID_Region, Nom FROM Region")
                regions_db = {nom: id for (id, nom) in cursor.fetchall()}
                
                villes = df[['zip_code', 'label', 'region_geojson_name']].drop_duplicates()
                count = 0
                
                for row in tqdm(villes.itertuples(), total=len(villes), desc=f"Import {table}"):
                    region_id = regions_db.get(row.region_geojson_name)
                    if region_id:
                        cursor.execute(
                            """INSERT INTO Ville (CP, Nom, ID_Region)
                               VALUES (%s, %s, %s)""",
                            (row.zip_code, row.label, region_id)
                        )
                        count += 1
                conn.commit()
                console.print(f"{count} villes importées", style="bold green")
        elif table == 'Entreprise':
            # Insérer les entreprises directement
            entreprises = [
                ('LVMH', '+33 1 40 69 60 00', 'contact@lvmh.com', 'https://www.lvmh.fr', 'LVMH Moët Hennessy Louis Vuitton, leader mondial des articles de luxe.', 'Paris'),
                ('L\'Oréal', '+33 1 47 56 70 00', 'contact@loreal.com', 'https://www.loreal.com', 'Leader mondial de la beauté et des cosmétiques.', 'Paris'),
                ('Michelin', '+33 4 73 32 20 00', 'contact@michelin.com', 'https://www.michelin.fr', 'Fabricant de pneumatiques et guide gastronomique.', 'Clermont-Ferrand'),
                ('Airbus', '+33 5 61 93 33 33', 'info@airbus.com', 'https://www.airbus.com', 'Constructeur aéronautique européen.', 'Toulouse'),
                ('Dassault Systèmes', '+33 1 61 62 61 62', 'contact@3ds.com', 'https://www.3ds.com', 'Logiciels de conception 3D et simulation.', 'Toulouse'),
                ('Carrefour', '+33 1 53 70 11 11', 'contact@carrefour.com', 'https://www.carrefour.fr', 'Réseau de grande distribution alimentaire.', 'Massy'),
                ('Renault', '+33 1 76 84 16 00', 'contact@renault.com', 'https://www.renault.fr', 'Constructeur automobile français.', 'Woippy'),
                ('Société Générale', '+33 1 42 14 20 00', 'contact@socgen.com', 'https://www.societegenerale.fr', 'Banque et services financiers.', 'Paris'),
                ('Sanofi', '+33 1 53 77 40 00', 'contact@sanofi.com', 'https://www.sanofi.fr', 'Industrie pharmaceutique mondiale.', 'Gentilly'),
                ('Thales', '+33 1 57 77 80 00', 'contact@thalesgroup.com', 'https://www.thalesgroup.com', 'Technologies pour l\'aérospatial et la défense.', 'Paris'),
                ('EDF', '+33 1 40 42 22 22', 'contact@edf.fr', 'https://www.edf.fr', 'Électricité et énergie nucléaire.', 'Paris'),
                ('BNP Paribas', '+33 1 40 14 45 46', 'contact@bnpparibas.com', 'https://www.bnpparibas.fr', 'Groupe bancaire international.', 'Paris'),
                ('Accor', '+33 1 45 38 86 00', 'contact@accor.com', 'https://www.accor.com', 'Leader mondial de l\'hôtellerie (Ibis, Sofitel).', 'Paris'),
                ('Ubisoft', '+33 1 48 18 52 00', 'contact@ubisoft.com', 'https://www.ubisoft.com', 'Jeux vidéo (Assassin\'s Creed, Just Dance).', 'Montreuil'),
                ('Orange', '+33 1 44 44 22 22', 'contact@orange.com', 'https://www.orange.fr', 'Opérateur télécoms et services numériques.', 'Paris'),
                ('Capgemini', '+33 1 47 54 50 00', 'contact@capgemini.com', 'https://www.capgemini.com', 'Services informatiques et conseil en technologie.', 'Paris'),
                ('TotalEnergies', '+33 1 47 44 46 99', 'contact@totalenergies.com', 'https://www.totalenergies.fr', 'Multinationale énergétique.', 'Pau'),
                ('Decathlon', '+33 3 20 99 40 00', 'contact@decathlon.fr', 'https://www.decathlon.fr', 'Équipementier sportif grand public.', 'Brest'),
                ('Schneider Electric', '+33 1 41 29 70 00', 'contact@se.com', 'https://www.se.com', 'Spécialiste de la gestion de l\'énergie.', 'Rueil Malmaison'),
                ('CMA CGM', '+33 4 88 91 90 00', 'contact@cma-cgm.com', 'https://www.cma-cgm.fr', 'Armateur mondial de transport maritime.', 'Marseille')
            ]
            
            # Obtenir les IDs des villes
            cursor.execute("SELECT ID_Ville, Nom FROM Ville")
            villes_db = {nom.strip().lower(): id for (id, nom) in cursor.fetchall()}
            
            count = 0
            insert_values = []
            for entreprise in tqdm(entreprises, desc=f"Import {table}"):
                nom_ville = entreprise[5].strip().lower()
                ville_id = villes_db.get(nom_ville)
                if ville_id:
                    # Vérifier si l'entreprise existe déjà
                    cursor.execute(
                        """SELECT COUNT(*) FROM Entreprise WHERE Nom = %s AND ID_Ville = %s""",
                        (entreprise[0], ville_id)
                    )
                    if cursor.fetchone()[0] == 0:
                        insert_values.append((entreprise[0], entreprise[1], entreprise[2], entreprise[3], entreprise[4], ville_id))
                        count += 1
            if insert_values:
                cursor.executemany(
                    """INSERT INTO Entreprise (Nom, Telephone, Email, Site, Description, ID_Ville)
                       VALUES (%s, %s, %s, %s, %s, %s)""",
                    insert_values
                )
            conn.commit()
            console.print(f"{count} entreprises importées", style="bold green")
        elif table == 'Secteur':
            # Insérer les secteurs directement
            secteurs = [
                'Technologie',
                'Finance',
                'Santé',
                'Énergie',
                'Transport',
                'Commerce',
                'Industrie',
                'Éducation',
                'Tourisme',
                'Agriculture'
            ]
            
            count = 0
            insert_values = []
            for secteur in tqdm(secteurs, desc=f"Import {table}"):
                # Vérifier si le secteur existe déjà
                cursor.execute(
                    """SELECT COUNT(*) FROM Secteur WHERE Nom = %s""",
                    (secteur,)
                )
                if cursor.fetchone()[0] == 0:
                    insert_values.append((secteur,))
                    count += 1
            if insert_values:
                cursor.executemany(
                    """INSERT INTO Secteur (Nom)
                       VALUES (%s)""",
                    insert_values
                )
            conn.commit()
            console.print(f"{count} secteurs importés", style="bold green")
        elif table == 'Competence':
            # Insérer les compétences directement
            competences = [
                'Python',
                'Java',
                'C++',
                'SQL',
                'JavaScript',
                'HTML',
                'CSS',
                'React',
                'Node.js',
                'Django',
                'Gestion de projet',
                'Communication',
                'Marketing',
                'Vente',
                'Finance',
                'Ressources humaines',
                'Design graphique',
                'Rédaction',
                'Analyse de données',
                'Service client',
                'Leadership',
                'Négociation',
                'Résolution de problèmes',
                'Esprit critique',
                'Créativité',
                'Adaptabilité',
                'Gestion du temps',
                'Travail en équipe',
                'Prise de décision',
                'Gestion du stress'
            ]
            
            count = 0
            insert_values = []
            for competence in tqdm(competences, desc=f"Import {table}"):
                # Vérifier si la compétence existe déjà
                cursor.execute(
                    """SELECT COUNT(*) FROM Competence WHERE Libelle = %s""",
                    (competence,)
                )
                if cursor.fetchone()[0] == 0:
                    insert_values.append((competence,))
                    count += 1
            if insert_values:
                cursor.executemany(
                    """INSERT INTO Competence (Libelle)
                       VALUES (%s)""",
                    insert_values
                )
            conn.commit()
            console.print(f"{count} compétences importées", style="bold green")
        elif table == 'Role':
            # Insérer les rôles directement
            roles = [
                'Administrateur',
                'Pilote',
                'Etudiant'
            ]
            
            count = 0
            insert_values = []
            for role in tqdm(roles, desc=f"Import {table}"):
                # Vérifier si le rôle existe déjà
                cursor.execute(
                    """SELECT COUNT(*) FROM Role WHERE Libelle = %s""",
                    (role,)
                )
                if cursor.fetchone()[0] == 0:
                    insert_values.append((role,))
                    count += 1
            if insert_values:
                cursor.executemany(
                    """INSERT INTO Role (Libelle)
                       VALUES (%s)""",
                    insert_values
                )
            conn.commit()
            console.print(f"{count} rôles importés", style="bold green")
        elif table == 'Statuts_Candidature':
            statuts = ['En attente', 'Acceptée', 'Refusée']
            insert_values = [(statut,) for statut in statuts]
            cursor.executemany(
                """INSERT INTO Statuts_Candidature (Libelle) VALUES (%s)""",
                insert_values
            )
            conn.commit()
            console.print(f"{len(statuts)} statuts de candidature importés", style="bold green")
        else:
            console.print(f"Table inconnue: {table}", style="bold red")

    except Error as e:
        conn.rollback()
        console.print(f"Erreur import: {e}", style="bold red")

def delete_data(table):
    """Supprime les données des tables"""
    try:
        if table == 'Region':
            confirmation = input("La suppression de la table 'Region' nécessite également la suppression des tables 'Ville' et 'Entreprise'. Voulez-vous continuer ? (oui/non): ")
            if confirmation.lower() == 'oui':
                cursor.execute("DELETE FROM Entreprise")
                cursor.execute("ALTER TABLE Entreprise AUTO_INCREMENT = 1;")
                cursor.execute("DELETE FROM Ville")
                cursor.execute("ALTER TABLE Ville AUTO_INCREMENT = 1;")
                cursor.execute("DELETE FROM Region")
                cursor.execute("ALTER TABLE Region AUTO_INCREMENT = 1;")
                conn.commit()
                console.print("Toutes les données des tables 'Entreprise', 'Ville' et 'Region' ont été supprimées", style="bold green")
            else:
                console.print("Suppression annulée", style="bold yellow")
        elif table == 'Ville':
            cursor.execute("DELETE FROM Entreprise")
            cursor.execute("ALTER TABLE Entreprise AUTO_INCREMENT = 1;")
            cursor.execute("DELETE FROM Ville")
            cursor.execute("ALTER TABLE Ville AUTO_INCREMENT = 1;")
            conn.commit()
            console.print(f"Toutes les données des tables 'Entreprise' et 'Ville' ont été supprimées", style="bold green")
        elif table == 'Entreprise':
            cursor.execute("DELETE FROM Entreprise")
            cursor.execute("ALTER TABLE Entreprise AUTO_INCREMENT = 1;")
            conn.commit()
            console.print(f"Toutes les données de la table {table} ont été supprimées", style="bold green")
        elif table == 'Secteur':
            cursor.execute("DELETE FROM Secteur")
            cursor.execute("ALTER TABLE Secteur AUTO_INCREMENT = 1;")
            conn.commit()
            console.print(f"Toutes les données de la table {table} ont été supprimées", style="bold green")
        elif table == 'Competence':
            cursor.execute("DELETE FROM Competence")
            cursor.execute("ALTER TABLE Competence AUTO_INCREMENT=1;")
            conn.commit()
            console.print(f"Toutes les données de la table {table} ont été supprimées", style="bold green")
        elif table == 'Role':
            cursor.execute("DELETE FROM Role")
            cursor.execute("ALTER TABLE Role AUTO_INCREMENT=1;")
            conn.commit()
            console.print(f"Toutes les données de la table {table} ont été supprimées", style="bold green")
        else:
            console.print(f"Table inconnue: {table}", style="bold red")
    except Error as e:
        conn.rollback()
        console.print(f"Erreur suppression: {e}", style="bold red")

def count_elements():
    """Compte le nombre d'éléments dans chaque table"""
    try:
        cursor.execute("SELECT COUNT(*) FROM Region")
        region_count = cursor.fetchone()[0]
        cursor.execute("SELECT COUNT(*) FROM Ville")
        ville_count = cursor.fetchone()[0]
        cursor.execute("SELECT COUNT(*) FROM Entreprise")
        entreprise_count = cursor.fetchone()[0]
        cursor.execute("SELECT COUNT(*) FROM Secteur")
        secteur_count = cursor.fetchone()[0]
        cursor.execute("SELECT COUNT(*) FROM Competence")
        competence_count = cursor.fetchone()[0]
        cursor.execute("SELECT COUNT(*) FROM Role")
        role_count = cursor.fetchone()[0]
        return region_count, ville_count, entreprise_count, secteur_count, competence_count, role_count
    except Error as e:
        print(f"Erreur comptage: {e}")
        return 0, 0, 0, 0, 0, 0

def menu():
    """Affiche le menu et gère les choix de l'utilisateur"""
    while True:
        region_count, ville_count, entreprise_count, secteur_count, competence_count, role_count = count_elements()
        
        table = Table(title="MENU")
        table.add_column("Option", justify="center", style="cyan", no_wrap=True)
        table.add_column("Description", justify="center", style="magenta")
        
        table.add_row("1", "Ajouter des éléments")
        table.add_row("2", "Supprimer des éléments")
        table.add_row("3", "Quitter")
        
        console.print(table)
        
        console.print(f"Nombre de régions: {region_count}", style="bold green")
        console.print(f"Nombre de villes: {ville_count}", style="bold green")
        console.print(f"Nombre d'entreprises: {entreprise_count}", style="bold green")
        console.print(f"Nombre de secteurs: {secteur_count}", style="bold green")
        console.print(f"Nombre de compétences: {competence_count}", style="bold green")
        console.print(f"Nombre de rôles: {role_count}", style="bold green")
        
        choice = Prompt.ask("Choisissez une option (1-3)")
        
        if choice == '1':
            console.print("\nTables disponibles: Region, Ville, Entreprise, Secteur, Competence, Role, Toutes", style="bold blue")
            tables = Prompt.ask("Choisissez les tables à importer (séparées par des virgules)").split(',')
            for table in tables:
                table = table.strip()
            if table == 'Toutes':
                for t in ['Region', 'Ville', 'Entreprise', 'Secteur', 'Competence', 'Role']:
                    import_data(t)
            elif table in ['Region', 'Ville', 'Entreprise', 'Secteur', 'Competence', 'Role']:
                import_data(table)
            else:
                console.print(f"Table invalide: {table}, veuillez réessayer.", style="bold red")
        elif choice == '2':
            console.print("\nTables disponibles: Region, Ville, Entreprise, Secteur, Competence, Role, Toutes", style="bold blue")
            tables = Prompt.ask("Choisissez les tables à supprimer (séparées par des virgules)").split(',')
            for table in tables:
                table.strip()
            if table == 'Toutes':
                for t in ['Entreprise', 'Ville', 'Region', 'Secteur', 'Competence', 'Role']:
                    delete_data(t)
            elif table in ['Region', 'Ville', 'Entreprise', 'Secteur', 'Competence', 'Role']:
                delete_data(table)
            else:
                console.print(f"Table invalide: {table}, veuillez réessayer.", style="bold red")
        elif choice == '3':
            console.print("Au revoir!", style="bold yellow")
            break
        else:
            console.print("Choix invalide, veuillez réessayer.", style="bold red")


if __name__ == "__main__":
    create_tables()
    menu()

# Fermer la connexion à la BDD
if conn.is_connected():
    cursor.close()
    conn.close()

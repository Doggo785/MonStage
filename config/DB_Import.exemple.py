import pandas as pd
import mysql.connector
from mysql.connector import Error
import os
from rich.console import Console
from rich.table import Table
from rich.prompt import Prompt
from tqdm import tqdm

console = Console()

# Configuration de la BDD
DB_CONFIG = {
    'host': 'localhost',
    'user': 'your_username',
    'password': 'your_password',
    'database': 'your_database'
}

script_dir = os.path.dirname(__file__)
csv_file_path = os.path.join(script_dir, 'cities.csv')

# Connexion globale à la BDD
conn = mysql.connector.connect(**DB_CONFIG)
cursor = conn.cursor()

def create_tables():
    """Crée les tables si elles n'existent pas"""
    queries = [
        """
        CREATE TABLE IF NOT EXISTS Region (
            ID_Region INT AUTO_INCREMENT PRIMARY KEY,
            Nom VARCHAR(255) UNIQUE
        )
        """,
        """
        CREATE TABLE IF NOT EXISTS Ville (
            ID_Ville INT AUTO_INCREMENT PRIMARY KEY,
            CP VARCHAR(5),
            Nom VARCHAR(50),
            ID_Region INT,
            FOREIGN KEY(ID_Region) REFERENCES Region(ID_Region)
        )
        """,
        """
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
        """,
        """
        CREATE TABLE IF NOT EXISTS Secteur (
            ID_Secteur INT AUTO_INCREMENT PRIMARY KEY,
            Nom VARCHAR(50) NOT NULL
        )
        """,
        """
        CREATE TABLE IF NOT EXISTS Competence (
            ID_Competence INT AUTO_INCREMENT PRIMARY KEY,
            Libelle VARCHAR(50) NOT NULL
        )
        """,
        """
        CREATE TABLE IF NOT EXISTS Role (
            ID_Role INT AUTO_INCREMENT PRIMARY KEY,
            Libelle VARCHAR(25) NOT NULL UNIQUE
        )
        """
    ]
    
    try:
        for query in queries:
            cursor.execute(query)
        conn.commit()
    except Error as e:
        print(f"Erreur création tables: {e}")

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

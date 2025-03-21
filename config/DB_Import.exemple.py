import pandas as pd
import mysql.connector
from mysql.connector import Error
import os

# Configuration de la BDD
DB_CONFIG = {
    'host': 'localhost',
    'user': 'your_username',
    'password': 'your_password',
    'database': 'your_database'
}

script_dir = os.path.dirname(__file__)
csv_file_path = os.path.join(script_dir, 'cities.csv')

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
        """
    ]
    
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        for query in queries:
            cursor.execute(query)
        conn.commit()
    except Error as e:
        print(f"Erreur création tables: {e}")
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def import_data(table):
    """Importe les données du CSV vers la BDD"""
    # Connexion BDD
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    try:
        # Lire le CSV
        df = pd.read_csv(csv_file_path, sep=',', dtype={'zip_code': str})
        df['region_geojson_name'] = df['region_geojson_name'].str.strip()

        if table == 'Region':
            # Importer les Régions
            regions = df[['region_geojson_name']].drop_duplicates()
            for _, row in regions.iterrows():
                cursor.execute(
                    "INSERT IGNORE INTO Region (Nom) VALUES (%s)",
                    (row['region_geojson_name'],)
                )
            conn.commit()
            print(f"{len(regions)} régions insérées")
        elif table == 'Ville':
            # Importer les Villes
            cursor.execute("SELECT ID_Region, Nom FROM Region")
            regions_db = {nom: id for (id, nom) in cursor.fetchall()}
            
            villes = df[['zip_code', 'label', 'region_geojson_name']].drop_duplicates()
            count = 0
            
            for _, row in villes.iterrows():
                region_id = regions_db.get(row['region_geojson_name'])
                if region_id:
                    cursor.execute(
                        """INSERT INTO Ville (CP, Nom, ID_Region)
                           VALUES (%s, %s, %s)""",
                        (row['zip_code'], row['label'], region_id)
                    )
                    count += 1
            conn.commit()
            print(f"{count} villes importées")

    except Error as e:
        conn.rollback()
        print(f"Erreur import: {e}")
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def delete_data(table):
    """Supprime les données des tables"""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        if table == 'Region':
            confirmation = input("La suppression de la table 'Region' nécessite également la suppression de la table 'Ville'. Voulez-vous continuer ? (oui/non): ")
            if confirmation.lower() == 'oui':
                cursor.execute("DELETE FROM Ville")
                cursor.execute("DELETE FROM Region")
                conn.commit()
                print("Toutes les données des tables 'Ville' et 'Region' ont été supprimées")
            else:
                print("Suppression annulée")
        elif table == 'Ville':
            cursor.execute("DELETE FROM Ville")
            conn.commit()
            print(f"Toutes les données de la table {table} ont été supprimées")
    except Error as e:
        conn.rollback()
        print(f"Erreur suppression: {e}")
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def count_elements():
    """Compte le nombre d'éléments dans chaque table"""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM Region")
        region_count = cursor.fetchone()[0]
        cursor.execute("SELECT COUNT(*) FROM Ville")
        ville_count = cursor.fetchone()[0]
        return region_count, ville_count
    except Error as e:
        print(f"Erreur comptage: {e}")
        return 0, 0
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def menu():
    """Affiche le menu et gère les choix de l'utilisateur"""
    while True:
        region_count, ville_count = count_elements()
        print("\nMenu:")
        print(f"Nombre de régions: {region_count}")
        print(f"Nombre de villes: {ville_count}")
        print("1. Ajouter des éléments")
        print("2. Supprimer des éléments")
        print("3. Quitter")
        choice = input("Choisissez une option: ")
        
        if choice == '1':
            tables = input("Choisissez les tables (Region, Ville, Toutes): ").split(',')
            for table in tables:
                table = table.strip()
                if table in ['Region', 'Ville']:
                    import_data(table)
                elif table == 'Toutes':
                    import_data('Region')
                    import_data('Ville')
                else:
                    print(f"Table invalide: {table}, veuillez réessayer.")
        elif choice == '2':
            tables = input("Choisissez les tables (Region, Ville, Toutes): ").split(',')
            for table in tables:
                table = table.strip()
                if table == 'Toutes':
                    delete_data('Ville')
                    delete_data('Region')
                elif table in ['Region', 'Ville']:
                    delete_data(table)
                else:
                    print(f"Table invalide: {table}, veuillez réessayer.")
        elif choice == '3':
            break
        else:
            print("Choix invalide, veuillez réessayer.")

if __name__ == "__main__":
    create_tables()
    menu()
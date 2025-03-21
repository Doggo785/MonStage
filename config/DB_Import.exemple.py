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
            Nom_Region VARCHAR(255) UNIQUE
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

def import_data():
    """Importe les données du CSV vers la BDD"""
    # Connexion BDD
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    try:
        # Lire le CSV
        df = pd.read_csv(csv_file_path, sep=',', dtype={'zip_code': str})
        df['region_geojson_name'] = df['region_geojson_name'].str.strip()
        

        #Importer les Villes
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

if __name__ == "__main__":
    create_tables()
    import_data()
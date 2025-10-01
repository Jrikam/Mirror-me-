CREATE DATABASE IF NOT EXISTS mirror_me CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE mirrorme_db;

CREATE TABLE utilisateurs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  image_profil VARCHAR(255) DEFAULT NULL,
  domaine_favori VARCHAR(100),
  date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE stars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  domaine VARCHAR(100),
  image VARCHAR(255),   
  description TEXT,          
);

CREATE TABLE ressemblances (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  star_id INT NOT NULL,
  pourcentage INT CHECK (pourcentage BETWEEN 0 AND 100),
  date_comparaison DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  FOREIGN KEY (star_id) REFERENCES stars(id) ON DELETE CASCADE
);

CREATE TABLE objectifs (
  id INT AUTO_INCREMENT PRIMARY KEY,  
  utilisateur_id INT NOT NULL,
  star_id INT NOT NULL,
  conseil TEXT,
  niveau VARCHAR(50),
  etat ENUM('en cours','terminé') DEFAULT 'en cours',
  date_checked DATETIME NOT NULL, 
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  FOREIGN KEY (star_id) REFERENCES stars(id) ON DELETE CASCADE
);

CREATE TABLE questionnaires (
  id INT AUTO_INCREMENT PRIMARY KEY, 
  utilisateur_id INT NOT NULL,
  responses TEXT,
  score_psy INT,
  categorie VARCHAR(100),
  date_fait TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);
                                                            
CREATE TABLE journal_entries (
  id INT AUTO_INCREMENT PRIMARY KEY, 
  utilisateur_id INT NOT NULL,
  responses TEXT,
  categorie VARCHAR(100) DEFAULT NULL,
  contenu TEXT NOT NULL,
  audio_path VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_journal_user FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE);

  CREATE TABLE user_objectives ( 
  id INT AUTO_INCREMENT PRIMARY KEY, 
  utilisateur_id INT NOT NULL, 
  objectif_id INT NOT NULL,
  date_checked DATETIME NOT NULL,
  UNIQUE KEY(utilisateur_id,objectif_id) );

CREATE TABLE progression (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    etape INT DEFAULT 0,
    date_update DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tips_journal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    objectif_journal_id INT NOT NULL,
    tip TEXT NOT NULL,
    FOREIGN KEY (objectif_journal_id) REFERENCES objectifs_journal(id) ON DELETE CASCADE
);

CREATE TABLE objectifs_journal (
  id INT AUTO_INCREMENT PRIMARY KEY,
  journal_id INT NOT NULL,
  titre VARCHAR(255) NOT NULL,
  etape INT DEFAULT 0,
  date_update DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tips (
  id INT AUTO_INCREMENT PRIMARY KEY,
  objectif_id INT NOT NULL,
  tip VARCHAR(255) NOT NULL
);

CREATE TABLE stars_temp (
  id INT NOT NULL,
  nom VARCHAR(100) NOT NULL,
  trait_principal VARCHAR(100),
  categorie VARCHAR(100),
  domaine VARCHAR(100),
  image_path VARCHAR(255),
  description TEXT,
  qualities TEXT,
  image VARCHAR(255)
);

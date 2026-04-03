-- ============================================================
-- InfoHub CPNV — Structure de la base de données
-- Importer en premier
-- ============================================================

DROP DATABASE IF EXISTS infohub;

CREATE DATABASE infohub
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE infohub;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role        ENUM('admin', 'etudiant', 'enseignant') NOT NULL DEFAULT 'etudiant',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS concours (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titre       VARCHAR(200) NOT NULL,
    description TEXT         NOT NULL,
    prix        DECIMAL(10,2) DEFAULT NULL,
    deadline    DATE          DEFAULT NULL,
    pdf_url     VARCHAR(500)  DEFAULT NULL,
    actif       BOOLEAN   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inscriptions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    concours_id INT          NOT NULL,
    nom         VARCHAR(150) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    type        ENUM('individuel', 'groupe') NOT NULL DEFAULT 'individuel',
    membres     TEXT         DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (concours_id) REFERENCES concours(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS news (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titre       VARCHAR(200) NOT NULL,
    contenu     TEXT         NOT NULL,
    auteur      VARCHAR(100) NOT NULL,
    image       VARCHAR(500) DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS annonces (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    titre         VARCHAR(200) NOT NULL,
    description   TEXT         NOT NULL,
    prix          DECIMAL(10,2) DEFAULT NULL,
    image         VARCHAR(500)  DEFAULT NULL,
    contact_email VARCHAR(150)  NOT NULL,
    actif         BOOLEAN   NOT NULL DEFAULT 1,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

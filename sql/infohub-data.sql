-- ============================================================
-- InfoHub CPNV — Données de test
-- Importer après infohub-structure.sql
-- ============================================================

USE infohub;

-- Mots de passe en clair : admin123 / etudiant123
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES
('Administrateur', 'admin@cpnv.ch',    '$2y$12$c5BjCnrfDG6Sc4yn..fFn.WMzCGy0sDa/PoKyNK1A6B6kHf97SNBK', 'admin'),
('Test Etudiant',  'etudiant@cpnv.ch', '$2y$12$8TXo788EIhdn2VmThsjVvOIGMd1mE6MXo6FX/v3JMLS5ykh5.ocDO', 'etudiant');

INSERT INTO concours (titre, description, prix, deadline, actif) VALUES (
    'Hackathon — Site pour l\'association',
    'Créer un prototype de site dynamique et responsive permettant d\'afficher le concours du mois, les news et les annonces à partir d\'une base de données, et de créer les outils pour poster les différents contenus.\n\nCe concours est ouvert aux étudiants de la section informatique de Sainte-Croix, soit individuellement, soit en groupe.',
    250.00,
    '2026-04-20',
    1
);

INSERT INTO news (titre, contenu, auteur) VALUES
(
    'MA-Métiers : des pitchs et des prix',
    'Du 12 au 30 janvier, 160 étudiant.e.s ont travaillé par groupes pour réaliser un projet dans le cadre des semaines dédiées aux « modules d\'application Métiers ». Le 30 janvier, chaque groupe devait présenter son projet en 3 minutes devant un jury. Une somme de CHF 1000.- a permis de récompenser les 4 meilleures équipes, offerte par la fondation Générosité.',
    'JP Chavey'
),
(
    'Expo sur le racisme',
    'Une exposition de nombreux panneaux sur le racisme a eu lieu dans nos couloirs. Une quinzaine de grands panneaux occupent les couloirs de l\'info, riches d\'informations sur plusieurs périodes de l\'histoire : colonialisme, apartheid, luttes aux USA pour l\'égalité, et aussi le passé de la Suisse.',
    'JP Chavey'
);

INSERT INTO annonces (titre, description, prix, contact_email, actif) VALUES
(
    '3 beamers Sony VPL-VW270ES',
    'Idéal pour cinéma à domicile. Location pour une année scolaire (avril 2026 à juin 2027). Sans garantie en cas de défaillance de la lampe.',
    30.00,
    'didier.wuillamoz@eduvaud.ch',
    1
),
(
    'Adaptateur 10Gigabit XG-C100C',
    'Compatible Windows et Linux. Interface RJ45 pour migration facile vers réseau 10 Gbit/s. Technologie QoS intégrée. Valeur ~CHF 60.- sur Galaxus.',
    20.00,
    'obada.alsaid@eduvaud.ch',
    1
),
(
    'Carte Wifi Mini-PCI-E Wifi 6',
    'Standard 802.11a/b/g/h/ac/ax. Dans son emballage d\'origine.',
    2.00,
    'didier.wuillamoz@eduvaud.ch',
    1
);

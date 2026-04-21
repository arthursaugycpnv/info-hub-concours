# InfoHub CPNV

Site web de **L'Assoce Info** — section informatique du CPNV, Sainte-Croix (VD).

Affiche le concours du mois, les news, les annonces et les publicités à partir d'une base de données MySQL. Inclut un back-office complet et un système d'authentification des membres.

---

## Stack

- **PHP 8.1+** (sans framework) + PDO
- **MySQL 8.4** via Laragon
- **Bootstrap 5.3.3** + Bootstrap Icons 1.11.3

---

## Installation

### Prérequis

- [Laragon 2026+](https://laragon.org/) — fournit Apache + MySQL
- PHP 8.1+

### Mise en place

**1. Cloner le repo dans le dossier web de Laragon**
```
C:\laragon\www\info-hub-concours-private\
```

**2. Importer la base de données** avec le charset UTF-8 (important) :
```bash
mysql -u root -h 127.0.0.1 --default-character-set=utf8mb4 < sql/infohub-structure.sql
mysql -u root -h 127.0.0.1 --default-character-set=utf8mb4 < sql/infohub-data.sql
```

> Pour repartir d'une base propre, ré-importer `infohub-structure.sql` (DROP + CREATE) puis `infohub-data.sql`.

**3. Vérifier `config.php`** à la racine :
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'infohub');
define('DB_USER', 'root');
define('DB_PASS', '');
```

**4. Démarrer Laragon → Start All**, puis accéder au site :
```
http://localhost/info-hub-concours-private/
```

### Note MySQL 8.4 (Laragon 2026)

MySQL 8.4 utilise `caching_sha2_password` par défaut. Si vous avez des erreurs de connexion, ajoutez dans `my.ini` sous `[mysqld]` :
```ini
mysql_native_password=ON
authentication_policy=mysql_native_password
```
Et connectez-vous via `127.0.0.1` (pas `localhost`).

---

## Structure du projet

```
/
├── config.php              # Connexion PDO + BASE_URL + helpers
├── index.php               # Page d'accueil (hero + concours + news + annonces)
├── concours.php            # Concours du mois (détail + inscription)
├── inscription.php         # Traitement formulaire d'inscription
├── news.php                # Liste des news + détail (?id=X)
├── annonces.php            # Liste avec filtre/tri
├── annonce.php             # Détail annonce + commentaires
├── pubs.php                # Publicités gratuites membres
├── login.php               # Connexion (tous les utilisateurs)
├── logout.php              # Déconnexion
├── register.php            # Inscription nouveau compte
├── admin/
│   ├── login.php           # Connexion admin uniquement
│   ├── logout.php          # Déconnexion admin
│   └── index.php           # Dashboard : Concours / News / Annonces / Pubs / Inscriptions / Modération
├── includes/
│   ├── header.php          # Navbar + <head> + barre admin
│   └── footer.php          # Footer 3 colonnes + scripts
├── assets/
│   ├── css/style.css       # Styles personnalisés
│   └── img/logo.png        # Logo L'Assoce Info
├── uploads/                # Images uploadées (annonces, news)
└── sql/
    ├── infohub-structure.sql   # DROP + CREATE DB et tables
    └── infohub-data.sql        # Données de test
```

---

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@cpnv.ch | admin123 |
| Étudiant | etudiant@cpnv.ch | etudiant123 |

---

## Fonctionnalités

### Public
- **Accueil** : hero avec stats live, concours du mois, dernières news, annonces récentes
- **Concours** : détail + countdown + formulaire d'inscription (individuel/groupe)
- **News** : liste + détail avec image
- **Annonces** : liste avec filtre texte et tri (date/prix), page détail avec commentaires
- **Pubs** : publicités gratuites postées par les membres connectés

### Authentification
- Inscription libre (étudiant ou enseignant)
- Connexion unifiée — redirection admin automatique
- Commentaires publiés directement si connecté, en attente de modération si visiteur

### Admin (`/admin/`)
- **Concours** : créer, modifier
- **News** : créer, modifier, supprimer + upload image
- **Annonces** : créer, modifier, supprimer + upload image
- **Pubs** : voir, supprimer
- **Inscriptions** : liste groupée par concours
- **Modération** : approuver/supprimer les commentaires en attente

---

## Base de données

| Table | Description |
|-------|-------------|
| `utilisateurs` | Comptes (admin / etudiant / enseignant) |
| `concours` | Concours du mois (un seul actif à la fois) |
| `inscriptions` | Inscriptions aux concours |
| `news` | Articles de news avec image optionnelle |
| `annonces` | Petites annonces avec image optionnelle |
| `commentaires` | Commentaires sur annonces (approuve=0/1) |
| `pubs` | Publicités gratuites des membres |

---

## Contact

Association InfoHub CPNV — [CPNV_Infohub@eduvaud.ch](mailto:CPNV_Infohub@eduvaud.ch)

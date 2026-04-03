# InfoHub Concours — Contexte du projet

## Vue d'ensemble

**Projet :** Site web dynamique pour l'Assoce Info (association des étudiants en informatique du CPNV)
**URL cible :** https://www.infohub.mycpnv.ch/
**Contact :** CPNV_Infohub@eduvaud.ch
**École :** CPNV — Centre de formation professionnelle Nord Vaudois, Sainte-Croix (VD), Suisse

---

## Le concours (cahier des charges)

**Prix :** CHF 250.-
**Deadline :** 20 avril 2026
**Ouvert à :** Étudiants de la section informatique de Sainte-Croix (individuel ou en groupe)
**Jury :** Enseignants
**Critère clé :** La réalisation doit être exploitable rapidement pour être déployée sur infohub.mycpnv.ch

### Ce que le site doit faire

1. **Afficher** le concours du mois
2. **Afficher** les news
3. **Afficher** les annonces
4. Toutes les données viennent d'**une base de données**
5. **Outils de publication** (back-office/CMS) pour poster les différents contenus

### Contraintes techniques
- Site **dynamique**
- Site **responsive**
- Prototype fonctionnel

---

## Stack technique

- **PHP** natif (sans framework)
- **MySQL** via PDO
- **Bootstrap 5** + Bootstrap Icons (CDN)
- Environnement local : **Laragon** (Apache + MySQL + phpMyAdmin)

---

## Structure des fichiers

```
/
├── config.php              # Connexion PDO + BASE_URL auto-détecté + session_start() + isAdmin()
├── index.php               # Accueil : concours actif + 3 news + 6 annonces
├── concours.php            # Concours du mois (détail + formulaire d'inscription)
├── inscription.php         # Traitement POST inscription concours
├── news.php                # Liste des news + détail article (?id=X)
├── annonces.php            # Liste de toutes les annonces
├── annonce.php             # Détail annonce : image, description, commentaires
├── includes/
│   ├── header.php          # <head> + navbar + barre admin (si connecté)
│   └── footer.php          # Footer + lien admin + scripts Bootstrap
├── admin/
│   ├── login.php           # Connexion admin (bcrypt)
│   ├── logout.php          # Déconnexion
│   └── index.php           # Dashboard : onglets Concours / News / Annonces
└── sql/
    ├── infohub-structure.sql   # DROP + CREATE DB + tables (importer en 1er)
    └── infohub-data.sql        # Données de test (importer en 2e)
```

---

## Base de données

| Table | Colonnes principales |
|-------|---------------------|
| `utilisateurs` | id, nom, email, mot_de_passe (bcrypt), role (admin/etudiant/enseignant) |
| `concours` | id, titre, description, prix, deadline, pdf_url, actif |
| `inscriptions` | id, concours_id, nom, email, type (individuel/groupe), membres |
| `news` | id, titre, contenu, auteur, image |
| `annonces` | id, titre, description, prix, image, contact_email, actif |
| `commentaires` | id, annonce_id, auteur, contenu |

**BASE_URL** est calculé automatiquement depuis `DOCUMENT_ROOT` — fonctionne avec `.test` et `localhost/sous-dossier/`.

---

## Identifiants de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@cpnv.ch | admin123 |
| Étudiant | etudiant@cpnv.ch | etudiant123 |

---

## Fonctionnalités implémentées

### Front-end public
- [x] Accueil avec concours actif, dernières news, dernières annonces
- [x] Page concours du mois (description, countdown deadline, formulaire d'inscription)
- [x] Liste des news + article complet
- [x] Liste des annonces + page détail par annonce
- [x] Page annonce : image, description, contact vendeur, section commentaires
- [x] Barre admin jaune en haut de page si connecté (lien dashboard + déconnexion)
- [x] Bouton "Gérer dans l'admin" + suppression de commentaire si admin

### Back-office admin
- [x] Login sécurisé (bcrypt + session)
- [x] Onglet Concours : ajouter un concours, historique (actif/archivé)
- [x] Onglet News : ajouter / supprimer une news
- [x] Onglet Annonces : ajouter / supprimer, avec date, nb commentaires, statut, lien public

---

## À faire (prochaines étapes)

- [ ] Design poussé (couleurs, typographie, mise en page)
- [ ] Page Pubs (publicités gratuites étudiants/enseignants)
- [ ] Upload d'images pour annonces et news
- [ ] Gestion des inscriptions dans l'admin (voir qui s'est inscrit au concours)

---

## Historique des sessions

### Session 1 — 2026-04-02
- Prise de connaissance du projet (lecture des 4 PDFs)
- Compréhension du cahier des charges
- Création de ce fichier CONTEXT.md

### Session 2 — 2026-04-03
- Choix du stack : PHP + MySQL + Bootstrap
- Création de la base minimale (config, index, admin login/dashboard, SQL)
- Pages détail : concours, news, annonces, annonce individuelle
- Ajout commentaires sur les annonces
- Barre admin sur les pages publiques
- BASE_URL auto-détecté pour compatibilité vhost/.test/localhost
- README.md réécrit en UTF-8

---

## Notes personnelles (touches à apporter)

- Design soigné, moderne, adapté à une communauté d'étudiants en informatique
- Dark mode possible
- Système de like/réaction sur les news
- Formulaire d'inscription au concours directement sur le site (déjà fait)

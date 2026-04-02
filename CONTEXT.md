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

## Structure des contenus

### 1. Concours du mois
- Un concours actif à la fois (ou plusieurs ?)
- Exemple actuel : Hackathon "Site pour l'association" — CHF 250.-, deadline 20 avril
- Doit inclure : description, prix, deadline, lien vers PDF descriptif, formulaire d'inscription

### 2. News
- Articles rédigés par étudiants/enseignants
- Exemples :
  - "MA-Métiers : des pitchs et des prix" (JP Chavey, fév 2026) — récit d'un événement scolaire
  - "Expo sur le racisme" (JP Chavey, fév 2026) — compte-rendu d'exposition dans les couloirs

### 3. Annonces
- Réservées en priorité aux étudiants, ensuite aux enseignants
- Format : photo + titre + description + prix + contact mail + mode de paiement (Twint)
- Paiement à l'association si affaire conclue
- Réponse le vendredi suivant l'annonce

### 4. Pubs (publicités gratuites)
- Gratuites pour étudiants et enseignants, payantes pour externes

---

## Décisions d'architecture (à définir)

### Stack technique envisagée
- À définir selon les compétences (PHP/MySQL classique ? Laravel ? Node.js ? Python/Django ?)
- Base de données : MySQL ou PostgreSQL
- Front-end : HTML/CSS/JS vanilla ou framework léger (Bootstrap pour le responsive)

### Pages front-end minimales
- `/` — Accueil
- `/concours` — Concours du mois
- `/news` — Liste des news
- `/news/:id` — Article complet
- `/annonces` — Liste des annonces
- `/pubs` — Publicités

### Back-office (admin)
- Connexion sécurisée
- CRUD pour chaque type de contenu

---

## Historique des sessions

### Session 1 — 2026-04-02
- Prise de connaissance du projet (lecture des 4 PDFs)
- Compréhension du cahier des charges
- Création de ce fichier CONTEXT.md
- Prochaine étape : définir le stack technique et initier la structure du projet

---

## Notes personnelles

- Design soigné, moderne, adapté à une communauté d'étudiants en informatique
- Dark mode possible
- Système de like/réaction sur les news
- Formulaire d'inscription au concours directement sur le site
# ComptoFlow — Plateforme comptable Laravel

Application web de comptabilité professionnelle pour les cabinets comptables en Côte d'Ivoire.
Conforme au plan comptable **SYSCOHADA révisé** avec TVA à 18%, BIC 25%.

---

## Fonctionnalités

| Module | Description |
|---|---|
| **Tableau de bord** | KPIs trésorerie, recettes, charges, résultat net, alertes |
| **Écritures** | Saisie partie double, validation équilibre, grand livre |
| **Factures** | Clients & fournisseurs, paiements partiels, relances |
| **Rapprochement** | Pointage bancaire ligne à ligne, validation écart zéro |
| **Bilan** | Actif / Passif OHADA, équilibre automatique |
| **Résultat** | Produits / Charges, BIC 25%, résultat net |
| **TVA** | Calcul automatique collectée − déductible, historique |
| **Tiers** | Clients & fournisseurs, encours, soldes |
| **Calendrier** | Échéances fiscales, TVA, CNPS, BIC |
| **Paramètres** | Société, utilisateurs (RBAC), notifications |

---

## Stack technique

- **Backend** : Laravel 11 (PHP 8.2+)
- **Frontend** : Blade + Tailwind CSS 3 + Alpine.js
- **Base de données** : MySQL 8 / PostgreSQL 14+
- **Assets** : Vite 5 + Tabler Icons
- **Tests** : PHPUnit 11 (Feature + Unit)

---

## Installation

### 1. Prérequis

```bash
PHP >= 8.2
Composer >= 2.6
Node.js >= 20
MySQL 8+ ou PostgreSQL 14+
```

### 2. Cloner et installer

```bash
git clone https://github.com/votre-repo/comptoflow.git
cd comptoflow

# Dépendances PHP
composer install

# Dépendances Node
npm install

# Copier la configuration
cp .env.example .env
php artisan key:generate
```

### 3. Configurer `.env`

```env
APP_NAME=ComptoFlow
APP_URL=http://localhost:8000
APP_LOCALE=fr

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=comptoflow
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
MAIL_FROM_ADDRESS=noreply@comptoflow.ci
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Migrations et données de base

```bash
# Créer la base de données
mysql -u root -p -e "CREATE DATABASE comptoflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Migrations
php artisan migrate

# Seeder (société demo + plan comptable OHADA complet + users)
php artisan db:seed

# Ou tout en une commande
php artisan migrate:fresh --seed
```

### 5. Compiler les assets

```bash
# Développement (avec hot reload)
npm run dev

# Production
npm run build
```

### 6. Lancer le serveur

```bash
php artisan serve
# → http://localhost:8000
```

---

## Comptes de connexion (après seed)

| Email | Mot de passe | Rôle |
|---|---|---|
| `admin@comptoflow.ci` | `password` | Administrateur |
| `editeur@comptoflow.ci` | `password` | Éditeur |

---

## Structure du projet

```
comptoflow/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── EcritureController.php
│   │   │   ├── FactureController.php
│   │   │   ├── TvaController.php
│   │   │   ├── BilanController.php
│   │   │   ├── RapprochementController.php
│   │   │   ├── TiersController.php
│   │   │   ├── CalendrierController.php
│   │   │   └── ParametresController.php
│   │   └── Requests/
│   │       ├── StoreEcritureRequest.php  ← validation équilibre
│   │       └── StoreFactureRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Societe.php
│   │   ├── Compte.php          ← plan OHADA
│   │   ├── Tiers.php
│   │   ├── Ecriture.php        ← partie double
│   │   ├── LigneEcriture.php
│   │   ├── Facture.php
│   │   ├── LigneFacture.php
│   │   ├── DeclarationTva.php
│   │   ├── Rapprochement.php
│   │   ├── LigneRapprochement.php
│   │   └── Evenement.php
│   ├── Policies/               ← contrôle d'accès RBAC
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── AuthServiceProvider.php
│   └── Services/
│       ├── DashboardService.php
│       ├── EcritureService.php  ← grand livre, validation
│       ├── FactureService.php   ← numérotation, paiements
│       ├── TvaService.php       ← calcul TVA nette
│       └── RapprochementService.php
│
├── database/
│   ├── factories/          ← 7 factories
│   ├── migrations/         ← 9 migrations
│   └── seeders/
│       └── DatabaseSeeder.php  ← 80+ comptes OHADA
│
├── resources/
│   ├── css/app.css         ← Tailwind + composants
│   ├── js/app.js           ← utilitaires JS
│   └── views/
│       ├── layouts/app.blade.php
│       ├── components/     ← nav-item, card, modal, badge...
│       ├── dashboard/
│       ├── ecritures/      ← index, create, show, grand-livre
│       ├── factures/       ← index, create, show
│       ├── bilan/          ← index (bilan), resultat
│       ├── tva/            ← index, show
│       ├── tiers/          ← index, create, show, edit
│       ├── rapprochement/  ← index, create, show
│       ├── calendrier/
│       └── parametres/
│
├── routes/web.php
├── tests/
│   ├── Feature/
│   │   ├── EcritureTest.php
│   │   └── FactureTest.php
│   └── Unit/
│       └── EcritureServiceTest.php  ← + TvaServiceTest
│
├── tailwind.config.js
├── vite.config.js
└── package.json
```

---

## Tests

```bash
# Tous les tests
php artisan test

# Avec couverture (nécessite Xdebug ou PCOV)
php artisan test --coverage

# Un seul fichier
php artisan test tests/Feature/EcritureTest.php

# Un seul test
php artisan test --filter test_création_écriture_équilibrée
```

Résultats attendus : **22 tests**, 100% de succès.

---

## Rôles utilisateurs (RBAC)

| Rôle | Dashboard | Saisie | Validation | Paramètres |
|---|---|---|---|---|
| `admin` | ✅ | ✅ | ✅ | ✅ |
| `editeur` | ✅ | ✅ | ✅ | ❌ |
| `lecteur` | ✅ | ❌ | ❌ | ❌ |

---

## Règles métier importantes

### Écriture comptable (partie double)
- Minimum **2 lignes** par écriture
- **Total débit = Total crédit** obligatoire (vérification front + back)
- Une écriture `validee` ne peut plus être modifiée
- Numérotation automatique : `JOURNAL-ANNEE-XXXX` (ex: `BQ-2025-0042`)

### Factures
- Numérotation auto : `FAC-ANNEE-XXXX` (clients) / `FF-ANNEE-XXXX` (fournisseurs)
- Statuts : `brouillon` → `emise` → `partielle` → `payee` / `en_retard` / `annulee`
- TVA 18% calculée automatiquement sur chaque ligne
- Paiements partiels supportés avec historique

### TVA
- Taux standard CI : **18%**
- TVA nette = TVA collectée (ventes) − TVA déductible (achats)
- Déclaration mensuelle avec date d'échéance automatique (fin de mois + 15j)

### Rapprochement bancaire
- Import des lignes du relevé bancaire
- Pointage ligne à ligne (AJAX sans rechargement)
- Validation bloquée si écart ≠ 0

---

## Conformité OHADA

- Plan comptable **SYSCOHADA révisé** — 7 classes, 80+ comptes standards
- Bilan au format **Actif / Passif** OHADA
- Compte de résultat avec **BIC 25%** (Côte d'Ivoire)
- Devise **FCFA (XOF)** par défaut
- Numéro contribuable au format CI (`CI-ABJ-XXXX-XXXXX-X`)

---

## Déploiement production

```bash
# Optimisations Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Assets production
npm run build

# Permissions storage
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Licence

MIT — Développé pour les cabinets comptables en Côte d'Ivoire.

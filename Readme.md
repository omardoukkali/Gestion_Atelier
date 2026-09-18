# 🛠️ Gestion d'Atelier — Application Web + IA

**Application de gestion d'atelier mécanique** — planification des tâches, suivi des interventions, gestion des stocks et **maintenance prédictive par Machine Learning**.

<p>
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white" alt="Laravel 10">
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vuedotjs&logoColor=white" alt="Vue 3">
  <img src="https://img.shields.io/badge/Inertia.js-1.0-9553E9" alt="Inertia">
  <img src="https://img.shields.io/badge/PostgreSQL-15-4169E1?logo=postgresql&logoColor=white" alt="PostgreSQL 15">
  <img src="https://img.shields.io/badge/FastAPI-ML-009688?logo=fastapi&logoColor=white" alt="FastAPI">
  <img src="https://img.shields.io/badge/scikit--learn-1.9-F7931E?logo=scikitlearn&logoColor=white" alt="scikit-learn">
  <img src="https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white" alt="Docker">
</p>

*Auteur : Omar Doukkali — Projet de fin de module (Laravel • Vue.js • UML • DevOps • IA)*

---

## 📝 Présentation

Cette application centralise l'ensemble des activités d'un atelier au sein d'une interface moderne. Elle couvre trois missions :

* **L'entretien** du parc de véhicules ;
* La réalisation de **travaux de fabrication** spécifiques ;
* La **gestion des stocks** de matériel et de pièces.

Elle intègre en plus un **service d'intelligence artificielle** qui prédit les besoins de maintenance et estime l'usure des pièces, pour passer d'une maintenance corrective à une maintenance **prédictive**.

---

## ✨ Nouveautés — Module de Maintenance Prédictive (IA)

Un microservice **FastAPI + scikit-learn** conteneurisé, appelé par Laravel via HTTP interne. Il expose **deux modèles** :

| Modèle | Type | Endpoint (service ML) | Sortie |
|---|---|---|---|
| **Besoin de maintenance** | Classification (régression logistique) | `POST /predict` | `need_maintenance` (0/1) + `probability` |
| **Durée de vie des plaquettes** | Régression | `POST /predict-lifespan` | `duree_vie_km` (km estimés) |

**Modèle 1 — Besoin de maintenance** prend en entrée l'état du véhicule (kilométrage, âge, historique de pannes, état des freins/pneus/batterie, etc.) et renvoie une probabilité qu'une maintenance soit nécessaire.

**Modèle 2 — Durée de vie des plaquettes** estime le kilométrage restant à partir du profil d'usage (`pct_ville`, `style_conduite`, `type_vehicule`, `transmission`, `relief`, `qualite_plaquette`).

### Comment c'est branché

```
Vue 3  ──►  Laravel API  ──►  MlService  ──HTTP──►  ml-service (FastAPI)  ──►  model.pkl
(Maintenance/Predict)   (MaintenanceController)                              model_lifespan.pkl
```

* Côté Laravel, `App\Services\MlService` appelle l'URL définie par `ML_SERVICE_URL` (par défaut `http://ml-service:8000`).
* Routes exposées (`routes/api.php`) :
  * `POST /api/predict-maintenance`
  * `POST /api/predict-lifespan`
* Interface utilisateur : page Vue `Maintenance/Predict.vue`, accessible via la route `prediction.plaquettes` (`/prediction-plaquettes`).

### Ré-entraîner un modèle

Le script `ml-training/train.py` construit un pipeline scikit-learn complet (standardisation des variables numériques + `OneHotEncoder` pour les catégorielles + régression logistique), l'évalue (`classification_report`, matrice de confusion, ROC-AUC) puis exporte le modèle et son « contrat d'entrée » :

```bash
cd ml-training
python train.py
# → produit model.pkl + model_columns.json
# à copier ensuite dans DevOps/ml-service/
```

---

## 🎯 Périmètre fonctionnel

* **Demandes** — soumission des demandes d'entretien / fabrication, validation, priorisation.
* **Tâches** — planification, affectation aux ouvriers, démarrage / clôture.
* **Fiches travail** — saisie des interventions réalisées.
* **Devis** — lignes de devis, validation / refus par le chef, export **PDF** (dompdf).
* **Véhicules** — gestion du parc.
* **Achats** — commandes fournisseurs et réception des livraisons.
* **Inventaire** — stock de pièces avec seuils.
* **IA** — prédiction de maintenance et durée de vie des plaquettes.
* **Sécurité** — authentification (Sanctum) et gestion des rôles.

## 👥 Acteurs et rôles

1. **Chef d'atelier** — valide les demandes, priorise, affecte les tâches, gère les commandes et les fournisseurs, valide les devis.
2. **Ouvrier** — consulte ses tâches du jour, saisit les fiches travail, établit des devis, réceptionne les livraisons.
3. **Employé** — soumet des demandes et suit leur statut.

Le tableau de bord agit comme un « aiguilleur » : chaque rôle est redirigé vers sa vue métier après connexion.

---

## 💻 Stack technique

| Couche | Technologies |
|---|---|
| **Backend** | Laravel 10 (PHP 8.1+), Eloquent, Sanctum, dompdf, Ziggy |
| **Frontend** | Vue.js 3 + Inertia.js, Vite, Tailwind CSS |
| **Base de données** | PostgreSQL 15 |
| **Service IA** | FastAPI, Uvicorn, scikit-learn 1.9, pandas, joblib |
| **DevOps** | Docker Compose (Nginx, PHP-FPM, Node, PostgreSQL, ML) |

---

## 🏗️ Architecture des conteneurs

| Service | Rôle | Port (hôte → conteneur) |
|---|---|---|
| `webserver` (nginx) | Point d'entrée de l'app | **8001 → 80** |
| `app` (php-fpm) | Backend Laravel | interne (9000) |
| `database` (postgres:15) | Base de données | **5436 → 5432** |
| `node` (vite) | Compilation front (profil `dev`) | **5173 → 5173** |
| `ml-service` (fastapi) | Inférence IA | **8010 → 8000** *(dev)* |

Tous les services partagent le réseau `atelier_network`.

---

## 🚀 Installation & Lancement (Docker)

> Prérequis : **Docker** et **Docker Compose** installés.

**1. Cloner le dépôt**
```bash
git clone https://github.com/omardoukkali/Gestion_Atelier.git
cd Gestion_Atelier
```

**2. Préparer le fichier d'environnement Laravel**
Copie l'exemple puis adapte la connexion PostgreSQL aux valeurs du `docker-compose` :
```bash
cd Backend_Frontend
cp .env.example .env
```
Renseigne dans `.env` (le host `database` = nom du conteneur DB) :
```env
DB_CONNECTION=pgsql
DB_HOST=database
DB_PORT=5432
DB_DATABASE=gestion_atelier
DB_USERNAME=root
DB_PASSWORD=rootpassword

ML_SERVICE_URL=http://ml-service:8000
```

**3. Démarrer les conteneurs**
Depuis le dossier `DevOps` (le profil `dev` inclut Vite) :
```bash
cd ../DevOps
docker compose --profile dev up -d --build
```
> `--build` reconstruit les images (utile à la première installation), `-d` lance en arrière-plan.

**4. Installer les dépendances et initialiser l'application**
On exécute les commandes *dans* le conteneur `app` :
```bash
# Dépendances PHP
docker compose exec app composer install

# Clé d'application Laravel
docker compose exec app php artisan key:generate

# Créer les tables + comptes de démonstration
docker compose exec app php artisan migrate --seed
```

**5. Compiler le front (une fois, ou laisser Vite en watch)**
```bash
docker compose exec node npm install
docker compose exec node npm run dev      # mode développement (hot reload)
# ou : docker compose exec node npm run build   # build de production
```

**6. Accéder à l'application**
* Application : **http://localhost:8001**
* Documentation interactive du service IA (Swagger) : **http://localhost:8010/docs**

### 🔑 Comptes de démonstration

| Rôle | Email | Mot de passe |
|---|---|---|
| Chef d'atelier | `chef@atelier.com` | `password` |
| Ouvrier | `ouvrier@atelier.com` | `password` |
| Employé | `employe@atelier.com` | `password` |

---

## 📂 Structure du dépôt

```
Gestion_Atelier/
├── Backend_Frontend/     # Application Laravel 10 + Vue 3 (Inertia)
│   ├── app/              #   Models, Controllers, Services (dont MlService)
│   ├── routes/           #   web.php (Inertia) + api.php (endpoints IA)
│   ├── resources/js/     #   Pages Vue (dont Maintenance/Predict.vue)
│   └── database/         #   Migrations + seeders
├── DevOps/
│   ├── docker-compose.yml
│   ├── nginx/ php/ node/ #   Configs des conteneurs
│   └── ml-service/       #   Microservice FastAPI (main.py, model*.pkl, Dockerfile)
├── ml-training/          # Script d'entraînement des modèles (train.py)
└── UML/                  # Diagrammes (cas d'utilisation, classes, séquence…)
```

---

## 🧪 Tester le service IA rapidement

Prédiction du besoin de maintenance :
```bash
curl -X POST http://localhost:8010/predict \
  -H "Content-Type: application/json" \
  -d '{"Vehicle_Model":"Truck","Mileage":120000,"Maintenance_History":"Poor","Reported_Issues":3,"Vehicle_Age":8,"Fuel_Type":"Diesel","Transmission_Type":"Manual","Engine_Size":2000,"Odometer_Reading":120000,"Owner_Type":"Second","Insurance_Premium":500,"Service_History":2,"Accident_History":1,"Fuel_Efficiency":12.5,"Tire_Condition":"Worn","Brake_Condition":"Worn","Battery_Status":"Weak"}'
```

Estimation de la durée de vie des plaquettes :
```bash
curl -X POST http://localhost:8010/predict-lifespan \
  -H "Content-Type: application/json" \
  -d '{"pct_ville":70,"style_conduite":"agressif","type_vehicule":"SUV","transmission":"auto","relief":"montagne","qualite_plaquette":"standard"}'
```

---

*Version 2.0 — ajout du module de maintenance prédictive (IA).*

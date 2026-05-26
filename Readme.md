# 🛠️ Application Web — Gestion d'Atelier

[cite_start]**Projet de Fin de Module : Laravel • Vue.js • UML • DevOps** [cite: 75, 76]  
[cite_start]*Auteur : Omar Doukkali — Étudiant en développement web* [cite: 77]

---

## 📝 Présentation du Projet

[cite_start]Ce projet vise à concevoir et développer une application web complète pour la gestion d'un atelier[cite: 84]. [cite_start]L'objectif est d'automatiser et de centraliser les activités de l'entreprise au sein d'une interface moderne et intuitive[cite: 86]. 

[cite_start]L'atelier assure trois missions principales[cite: 85]:
* [cite_start]L'entretien du parc de véhicules[cite: 85].
* [cite_start]La réalisation de travaux de fabrication spécifiques[cite: 85].
* [cite_start]La gestion des stocks de matériel et de pièces[cite: 85].

## 🎯 Périmètre Fonctionnel

[cite_start]L'application couvre les domaines suivants[cite: 88]:
* [cite_start]**Tâches :** Gestion des demandes (entretien et fabrication), planification et priorisation[cite: 89, 90].
* [cite_start]**Interventions :** Suivi des travaux réalisés par les ouvriers via des fiches travail[cite: 91].
* [cite_start]**Achats :** Gestion des commandes de matériel et réception des livraisons[cite: 92].
* [cite_start]**Inventaire :** Gestion des stocks avec alertes de seuil[cite: 93].
* [cite_start]**Sécurité :** Authentification et gestion des rôles utilisateurs[cite: 94].

## 👥 Acteurs et Rôles

[cite_start]Le système implique trois acteurs principaux internes[cite: 96]:
1. [cite_start]**Chef d'atelier :** Valide les demandes, priorise, affecte les tâches, et gère les commandes[cite: 97].
2. [cite_start]**Ouvrier :** Consulte ses tâches, saisit les fiches travail, établit des devis, et réceptionne les livraisons[cite: 97].
3. [cite_start]**Employé :** Soumet des demandes de tâches ou de fabrication et suit leur statut[cite: 97, 115, 116].

## 💻 Technologies Utilisées

[cite_start]Ce projet repose sur une architecture MVC moderne, conteneurisée et intégrant une démarche CI/CD[cite: 147, 151, 172]:
* [cite_start]**Backend :** Laravel 10 (PHP 8.2), Eloquent ORM[cite: 79, 145, 148].
* [cite_start]**Frontend :** Vue.js 3 intégré via Vite, Tailwind CSS, Blade[cite: 79, 145].
* [cite_start]**Base de données :** PostgreSQL[cite: 79, 145].
* [cite_start]**DevOps :** Docker (docker-compose), GitHub Actions[cite: 79].

## 📂 Structure du Dépôt

Le projet est divisé en trois dossiers principaux :
* [cite_start]`/UML` : Contient l'analyse complète (Diagrammes de Cas d'Utilisation, Classes, Séquence, Composants, Déploiement)[cite: 187].
* [cite_start]`/Backend_FrontEnd` : Contient le code source de l'application Laravel et les composants Vue.js[cite: 150, 158].
* [cite_start]`/DevOps` : Contient les fichiers de configuration Docker et les pipelines CI/CD[cite: 187].

---
*Date : 26 mai 2026 | [cite_start]Version : 1.0* [cite: 80, 81]
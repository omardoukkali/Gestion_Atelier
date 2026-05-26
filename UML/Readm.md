# 📊 Analyse Technique — Gestion d'Atelier

Ce document regroupe les modélisations UML indispensables à la compréhension de la base de données et des processus métier.

## 1. Diagramme de Classes (Modèle de Données)
Le diagramme suivant définit les entités Eloquent (Models) et leurs relations au sein de l'application Laravel.

```mermaid
classDiagram
    direction BT
    
    namespace Authentification {
        class User {
            +int id
            +string nom
            +string email
            +string role
            +login()
        }
    }

    namespace Atelier {
        class Vehicule {
            +int id
            +string immatriculation
            +string modele
            +int km_actuel
        }
        class Tache {
            +int id
            +string statut
            +string priorite
            +updateStatus()
        }
    }

    namespace Logistique {
        class StockPiece {
            +int id
            +string designation
            +int quantite
            +isLowStock()
        }
    }

    User "1" -- "*" Tache : est assigné
    Vehicule "1" -- "*" Tache : concerne
    Tache "1" -- "*" StockPiece : utilise
    
    style User fill:#f9f,stroke:#333,stroke-width:2px
    style Tache fill:#bbf,stroke:#333,stroke-width:2px
    style StockPiece fill:#dfd,stroke:#333,stroke-width:2px

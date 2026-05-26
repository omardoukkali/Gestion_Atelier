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
            +checkRole()
        }
    }

    namespace Atelier {
        class Vehicule {
            +int id
            +string immatriculation
            +string marque
            +string modele
            +int km_actuel
        }
        class Tache {
            +int id
            +string type
            +string statut
            +string priorite
            +text description
            +updateStatus()
        }
        class FicheTravail {
            +int id
            +date date_realisation
            +string observations
            +int temps_passe
        }
    }

    namespace Logistique {
        class StockPiece {
            +int id
            +string designation
            +int quantite
            +int seuil_alerte
            +isLowStock()
        }
        class Commande {
            +int id
            +string statut
            +date date_commande
            +valider()
        }
        class Fournisseur {
            +int id
            +string nom
            +string contact
        }
    }

    User "1" -- "*" Tache : est assigné
    Vehicule "1" -- "*" Tache : concerne
    Tache "1" -- "*" StockPiece : utilise
    
    style User fill:#f9f,stroke:#333,stroke-width:2px
    style Tache fill:#bbf,stroke:#333,stroke-width:2px
    style StockPiece fill:#dfd,stroke:#333,stroke-width:2px

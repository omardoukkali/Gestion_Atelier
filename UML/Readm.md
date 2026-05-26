classDiagram
    %% Configuration du style
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

    %% Relations avec labels clairs
    User "1" -- "*" Tache : est assigné
    Vehicule "1" -- "*" Tache : possède
    Tache "1" -- "1" FicheTravail : génère
    FicheTravail "*" -- "*" StockPiece : consomme
    Commande "1" -- "*" StockPiece : inclut
    Fournisseur "1" -- "*" Commande : traite

    %% Styles visuels (Couleurs)
    style User fill:#f9f,stroke:#333,stroke-width:2px
    style Tache fill:#bbf,stroke:#333,stroke-width:2px
    style StockPiece fill:#dfd,stroke:#333,stroke-width:2px

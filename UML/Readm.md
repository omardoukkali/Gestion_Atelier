classDiagram
    class User {
        +int id
        +string nom
        +string email
        +string role
        +login()
    }
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
        +description()
    }
    class StockPiece {
        +int id
        +string designation
        +int quantite
        +int seuil_alerte
    }
    class Commande {
        +int id
        +string statut
        +date date_commande
    }

    User "1" -- "*" Tache : est assigné
    Vehicule "1" -- "*" Tache : possède
    Tache "1" -- "*" FicheTravail : génère
    FicheTravail "*" -- "*" StockPiece : utilise
    Commande "1" -- "*" StockPiece : contient
    Fournisseur "1" -- "*" Commande : reçoit

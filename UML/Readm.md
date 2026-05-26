# 🛠️ Application Web — Gestion d'Atelier
Ce projet démontre une architecture moderne de gestion industrielle utilisant **Laravel 10** et **Vue.js 3**, entièrement conteneurisée avec **Docker**.

## 🏗️ Architecture du Système
Ce diagramme illustre le flux de données entre les conteneurs Docker, la logique métier Laravel et l'interface utilisateur dynamique.

```mermaid
graph TD
    subgraph "Infrastructure Layer (Docker Compose)"
        A[Nginx Web Server] -- "Proxy Pass" --> B[PHP-FPM Container]
        B -- "Queries" --> C[(PostgreSQL Database)]
        D[Node.js Worker] -- "Vite Build" --> B
    end

    subgraph "Application Layer (Laravel MVC)"
        B --> E[Models: User, Tache, Stock]
        E --> F[Controllers & Policies]
        F --> G[Blade Templates]
    end

    subgraph "User Interface (Vue.js 3)"
        G -- "Mounts" --> H[Vue Components]
        H -- "Interactions" --> I[TaskBoard / StockTable]
        I -- "Forms (Inertia/Vite)" --> F
    end

    subgraph "DevOps & CI/CD (GitHub Actions)"
        J[Git Push] --> K{Pipeline CI/CD}
        K --> L[Lint & PHPUnit Tests]
        L --> M[Build Assets]
    end

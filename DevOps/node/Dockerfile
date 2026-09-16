FROM node:20-alpine

WORKDIR /var/www

# Copier d'abord les fichiers de dépendances (pour le cache Docker)
COPY Backend_Frontend/package*.json ./

# Installer node_modules DANS l'image (système Linux natif = rapide)
RUN npm install

# Copier le reste du code frontend
COPY Backend_Frontend/ ./

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]
#!/bin/bash

# Assurez-vous que le dossier "migrations" existe
mkdir -p migrations/versions

# Déplace le contenu du dossier "migrations" dans le dossier "versions"
mv migrations/* migrations/versions/

php bin/console doctrine:migrations:sync-metadata-storage
php bin/console cache:clear
# Exécute la commande make:migration
php bin/console make:migration
yes | php bin/console doctrine:migrations:migrate --no-interaction

echo "Migration completed."
    
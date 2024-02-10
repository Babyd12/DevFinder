#!/bin/bash

# Assurez-vous que le dossier "migrations" existe
# mkdir -p migrations/versions

# Déplace le contenu du dossier "migrations" dans le dossier "versions"
# mv migrations/* migrations/versions/
# rm -r migrations/*

# yes | php bin/console doctrine:d:drop --force --if-exists
# yes | php bin/console doctrine:d:create
# php bin/console doctrine:migrations:sync-metadata-storage
# php bin/console cache:clear
# Exécute la commande make:migration
php bin/console make:migration
yes | php bin/console doctrine:migrations:migrate --no-interaction

php bin/console doctrine:fixtures:load --no-interaction
echo "Installation complete vous pouvez éxécuter : php -S localhost:8000 -t public "



#for functional test syfony
#composer require --dev symfony/browser-kit symfony/http-client
#php bin/console doctrine:database:create --env=test
#php bin/console d:m:m --env=test --no-interaction
#php bin/console d:f:l --env=test --no-interaction


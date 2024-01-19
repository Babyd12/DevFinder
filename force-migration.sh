#!/bin/bash

# Exécute la commande doctrine:database:drop avec l'option --force
php bin/console doctrine:database:drop --force

# Exécute la commande doctrine:database:create
php bin/console doctrine:database:create

php bin/console make:migration

php bin/console doctrine:migrations:migrate 
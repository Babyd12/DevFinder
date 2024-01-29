
#installatino de composer
composer install

#jwt token generation
mkdir config/jwt
echo "JWT_PASSPHRASE" | openssl genrsa -out config/jwt/private.pem -aes256 -passout stdin 4096

openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem -passin pass:JWT_PASSPHRASE

#Ces commandes supprimeront la ligne contenant "JWT_PASSPHRASE" dans le fichier .env et ajouteront la nouvelle ligne avec la valeur contenu dans le "echo"
sed -i '/JWT_PASSPHRASE/d' .env
echo 'JWT_PASSPHRASE=JWT_PASSPHRASE' >> .env


#migration on database

# Exécute la commande doctrine:database:create
php bin/console doctrine:database:create

php bin/console make:migration --no-interaction

php bin/console doctrine:migrations:migrate --no-interaction

php bin/console cache:clear --no-interaction
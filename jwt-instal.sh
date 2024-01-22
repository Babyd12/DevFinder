#!/bin/bash

#Installation de jwt 
composer require lexik/jwt-authentication-bundle

#Dans cette commande, echo "JWT_PASSPHRASE" génère la passphrase et la passe à la commande openssl via stdin.
echo "JWT_PASSPHRASE" | openssl genrsa -out config/jwt/private.pem -aes256 -passout stdin 4096

openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem -passin pass:JWT_PASSPHRASE




#Ces commandes supprimeront la ligne contenant "JWT_PASSPHRASE" dans le fichier .env et ajouteront la nouvelle ligne avec la valeur contenu dans le "echo"
sed -i '/JWT_PASSPHRASE/d' .env
echo 'JWT_PASSPHRASE=JWT_PASSPHRASE' >> .env


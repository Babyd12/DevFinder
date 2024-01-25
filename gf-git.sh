#!/bin/bash

# Demander à l'utilisateur d'entrer le nom de la branche
read -rp "Entrez le nom de la branche : " branch_name

# Préfixer le nom de la branche
prefixed_branch_name="feature/gf-$branch_name"


# URL du dépôt
repo_url="https://github.com/Babyd12/gofast-symfonyBundle.git"

# Exécuter la commande git clone avec le nom de la branche préfixé sans spécifier le répertoire cible
git clone -b "$prefixed_branch_name" --single-branch "$repo_url" 

# Supprimer le répertoire .git créé par git clone (si vous ne le souhaitez pas)
# rm -rf .git

# Copier le fichier .htaccess du dossier gofast-symfonyBundle vers le répertoire courant
cp "gofast-symfonyBundle/.htaccess" .


# Supprimer le répertoire gofast-symfonyBundle (si vous ne le souhaitez pas)
read -rp "Voulez-vous supprimer le répertoire gofast-symfonyBundle ? (oui/non) : " confirmation
if [ "$confirmation" == "oui" ]; then
  rm -rf "gofast-symfonyBundle"
fi


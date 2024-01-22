#!/bin/bash

# Demander à l'utilisateur d'entrer le nom de la branche
read -rp "Entrez le nom de la branche : " branch_name
# read -rp "Entrez le nom de la branche : " repository_name

# URL du dépôt
repo_url="https://github.com/Babyd12/d-click-dev.git"

# Exécuter la commande git clone avec le nom de la branche
git clone -b "$branch_name" --single-branch "$repo_url"



#nano ~/.bashrc
#"/c/Users/simplon/Downloads/Mcire/go-fast/votre_script.sh"

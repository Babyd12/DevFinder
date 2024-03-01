#!/bin/bash

# Récupère le premier argument passé en ligne de commande et l'assigne à la variable "message"
message="$1"

# Vérifie si le message de commit est vide
if [ -z "$message" ]; then
  # Affiche un message d'erreur si le message de commit est vide
  echo "Erreur : Veuillez fournir un message de commit."
  echo "Utilisation : $0 <message_de_commit>"
  exit 1
fi

# Ajoute tous les fichiers modifiés ou nouveaux à la zone de staging
git add .

# Commit les modifications avec le message passé en argument
git commit -m "$message"

git push origin master
# Sortie avec succès (0 indique une sortie réussie)
exit 0

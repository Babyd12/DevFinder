#!/bin/bash

# Ajoute tous les fichiers à l'index
git add .

# Effectue un commit avec le message spécifié
git commit -m "testFunctionnel"

# Publie la fonctionnalité avec Git Flow
git flow feature publish

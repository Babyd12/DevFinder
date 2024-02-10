<?php

namespace App\Entity;

enum ProjetStatu: string
{
    case en_attente = "En attente";
    case en_cours = "En cours";
    case terminé = "Terminé";
}

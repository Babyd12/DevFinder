<?php

namespace App\Entity;

enum ProjetStatu: string
{
    case Draft = "Terminé";
    case PendingModerated = "En cours";
    case Published = "En attente";
}

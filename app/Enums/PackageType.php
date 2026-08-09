<?php

namespace App\Enums;

enum PackageType: string
{
    case Carton = 'carton';
    case Caisse = 'caisse';
    case Palette = 'palette';
    case Conteneur = 'conteneur';
    case Enveloppe = 'enveloppe';
    case Fut = 'fut';
}

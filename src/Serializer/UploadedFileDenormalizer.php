<?php
// api/src/Serializer/UploadedFileDenormalizer.php

namespace App\Serializer;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UploadedFileDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, string $format = null, array $context = []): UploadedFile
    {
        // $allowedExtensions = ['pdf']; // Ajoutez ici les extensions autorisées

        // $originalName = $data->getClientOriginalName();
        // $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // if (!in_array(strtolower($extension), $allowedExtensions)) {
        //     throw new \InvalidArgumentException('Le type de fichier n\'est pas autorisé. Utilisez uniquement des fichiers PDF.');
        // }

        return $data;
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $data instanceof UploadedFile;
    }
    
    public function getSupportedTypes()
    {
        return [
            // Exemple avec un type spécifique
            'App\Entity\Apprenant' => true,
            // 'App\Entity\Projet' => true,

            // // Exemple avec une interface
            // 'MonInterface' => true,

            // // Exemple avec tous les types (utile pour déclarer un support générique)
            '*' => true,
        ];
    }
}
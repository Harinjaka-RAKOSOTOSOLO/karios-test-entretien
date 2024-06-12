<?php

namespace App\Normalizer\DemandeClinique;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\DemandeClinique\Reponse;

class ReponseNormalizer implements NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var Reponse $object */
        return [
            'id' => $object->getId(),
            'date_creation' => $object->getDateCreation()->format('Y-m-d H:i:s'),
            'titre' => $object->getTitre(),
            'description' => $object->getDescription(),
            'depot' => $object->getDepot()->getId(),
            'type' => $object->getType(),
            'isValidate' => $object->isIsValidate() ?? false, // Si la donnée est null dans la db, on force la valeur boolean à false ici
            'reason' => $object->getValidationReason(),
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Reponse;
    }
}
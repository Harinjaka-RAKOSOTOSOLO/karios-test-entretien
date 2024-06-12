<?php

namespace App\Normalizer\DemandeClinique\tests\units;

use atoum\atoum;

class ReponseNormalizer extends atoum\test
{
    public function testNormalize()
    {
        $this
            ->assert('Test de normalisation OK')
            ->given(
                $reponse = $this->getReponse()
            )
            ->if(
                $reponseNormalizer = $this->getTestedInstance()
            )
            ->then
            ->array($reponseNormalizer->normalize($reponse))
            ->isEqualTo([
                'id' => 1,
                'date_creation' => '2019-01-01 00:00:00',
                'titre' => 'titre',
                'description' => 'description',
                'type' => 1,
                'depot' => 1,
                'isValidate' => false,
                'reason' => null,
            ])
        ;
    }

    private function getReponse()
    {
        $reponse = new \mock\App\Entity\DemandeClinique\Reponse();
        $this->calling($reponse)->getId = 1;
        $this->calling($reponse)->getDateCreation = new \DateTime('2019-01-01 00:00:00');
        $this->calling($reponse)->getTitre = 'titre';
        $this->calling($reponse)->getDescription = 'description';
        $this->calling($reponse)->getType = 1;
        $this->calling($reponse)->getDepot = $this->getDepot();
        $this->calling($reponse)->isIsValidate = false;
        $this->calling($reponse)->getValidationReason = null;

        return $reponse;
    }

    private function getDepot()
    {
        $depot = new \mock\App\Entity\DemandeClinique\Depot();
        $this->calling($depot)->getId = 1;

        return $depot;
    }

    private function getTestedInstance()
    {
        return $this->newTestedInstance();
    }
}
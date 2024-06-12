<?php

namespace App\Manager\DemandeClinique;

use App\Entity\DemandeClinique\Depot;
use App\Entity\DemandeClinique\Reponse;
use App\Repository\DemandeClinique\ReponseRepository;
use App\Validator\DemandeClinique\ReponseValidator;
use App\Factory\DemandeClinique\ReponseFactory;
use Doctrine\ORM\EntityManagerInterface;

class ReponseManager
{
    /** @var ReponseFactory $reponseFactory */
    private $reponseFactory;

    /** @var EntityManagerInterface $entityManagerInterface */
    private $entityManagerInterface;

    /** @var ReponseValidator $reponseValidator */
    private $reponseValidator;
    /**
     * @var ReponseRepository
     */
    private $reponseRepository;

    public function __construct(
        ReponseFactory $reponseFactory,
        EntityManagerInterface $entityManagerInterface,
        ReponseValidator $reponseValidator,
        ReponseRepository  $reponseRepository
    )
    {
        $this->reponseFactory = $reponseFactory;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->reponseValidator = $reponseValidator;
        $this->reponseRepository = $reponseRepository;
    }

    public function creer(Depot $depot, string $titre, string $description, int $type): Reponse
    {
        $reponse = $this->reponseFactory->creer($depot, $titre, $description, $type);

        $this->reponseValidator->valider($reponse);

        $this->entityManagerInterface->persist($reponse);
        $this->entityManagerInterface->flush();

        return $reponse;
    }

    public function valider(array $ids, string $validationReason) {
        $reponses = $this->reponseRepository->findCollectionById($ids);

        /** @var Reponse $reponse */
        foreach ($reponses as $reponse) {
            $reponse
                ->setIsValidate(true)
                ->setValidationReason($validationReason);

            $this->entityManagerInterface->persist($reponse);
        }
        $this->entityManagerInterface->flush();
    }
}

<?php

namespace App\Tests\phpunit\Manger\DemandeClinique;

use App\Entity\DemandeClinique\Depot;
use App\Entity\DemandeClinique\Reponse;
use App\Factory\DemandeClinique\ReponseFactory;
use App\Manager\DemandeClinique\ReponseManager;
use App\Repository\DemandeClinique\DepotRepository;
use App\Repository\DemandeClinique\ReponseRepository;
use App\Validator\DemandeClinique\ReponseValidator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReponseManagerTest extends KernelTestCase
{
    private $entityManager;
    private $reponseFactory;
    private $reponseValidator;
    private $reponseRepository;
    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->reponseFactory = new ReponseFactory();

        $this->reponseValidator = $this->createMock(ReponseValidator::class);
        $this->reponseRepository = $this->createMock(ReponseRepository::class);

    }

    public function testValiderWithFlush()
    {

        $depotRepository = self::$kernel->getContainer()->get('doctrine')->getManager()->getRepository(Depot::class);
        $depot = $depotRepository->find(1);

        // La fonction findCollectionById devra être appellé une fois
        $this->reponseRepository
            ->expects(self::once())
            ->method('findCollectionById')
            ->willReturn([
                $this->reponseFactory->creer($depot, "titre 1", "description", 1),
                $this->reponseFactory->creer($depot, "titre 2", "description", 1)
            ])
        ;

        // Persist devra être appellé 2 fois car on a deux réponses de findCOllectionById
        $this->entityManager
            ->expects(self::exactly(2))
            ->method('persist');

        // Flush toujours appelé car à l'extérieur de la boucle
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $reponseManager = new ReponseManager(
            $this->reponseFactory,
            $this->entityManager,
            $this->reponseValidator,
            $this->reponseRepository
        );

        $reponseManager->valider([1,2], "lorem");
    }

    public function testValiderWithoutFlush() {

        // La fonction findCollectionById devra être appellé une fois
        $this->reponseRepository
            ->expects(self::once())
            ->method('findCollectionById')
            ->willReturn([])
        ;

        // Persist ne devra pas être appellé car findCOllectionById renvoie un tableau vide
        $this->entityManager
            ->expects(self::never())
            ->method('persist');

        // Flush toujours appelé car à l'extérieur de la boucle
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $reponseManager = new ReponseManager(
            $this->reponseFactory,
            $this->entityManager,
            $this->reponseValidator,
            $this->reponseRepository
        );

        $reponseManager->valider([1,2], "lorem");
    }
}
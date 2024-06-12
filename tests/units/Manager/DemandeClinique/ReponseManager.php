<?php

namespace App\Manager\DemandeClinique\tests\units;

use App\Entity\DemandeClinique\Reponse;
use App\Repository\DemandeClinique\ReponseRepository;
use atoum\atoum;

class ReponseManager extends atoum\test
{
    private $entityManagerInterface;
    private $reponseFactory;
    private $reponseValidator;
    private $reponseRepository;

    public function beforeTestMethod($testMethod)
    {
        $this->entityManagerInterface = new \mock\Doctrine\ORM\EntityManagerInterface();

        $this->reponseFactory = new \mock\App\Factory\DemandeClinique\ReponseFactory();
        $this->reponseFactory->getMockController()->creer = $this->getReponse();

        $this->reponseValidator = new \mock\App\Validator\DemandeClinique\ReponseValidator();
        $this->reponseValidator->getMockController()->valider = null;

        $this->reponseRepository  = new class extends ReponseRepository {
            public function __construct()
            {
            }

            public function findCollectionById($ids)
            {
                return [ new Reponse(), new Reponse()];
            }
        };

    }

    public function testCreerOk()
    {
        $this
            ->assert('Test de création OK')
            ->given(
                $depot = $this->getDepot(),
                $titre = 'titre',
                $description = 'description',
                $type = 1
            )
            ->if(
                $reponseManager = $this->getTestedInstance()
            )
            ->then
                ->object($reponseManager->creer($depot, $titre, $description, $type))
                    ->isInstanceOf(\App\Entity\DemandeClinique\Reponse::class)
                ->mock($this->reponseFactory)
                    ->call('creer')
                        ->withArguments($depot, $titre, $description, $type)
                        ->once()
                ->mock($this->reponseValidator)
                    ->call('valider')
                        ->withArguments($this->getReponse())
                        ->once()
                ->mock($this->entityManagerInterface)
                    ->call('persist')
                        ->withArguments($this->getReponse())
                        ->once()
                    ->call('flush')
                        ->once()
        ;
    }

    public function testCreerKo()
    {
        $this->assert('Test de création KO')
            ->given(
                $depot = $this->getDepot(),
                $titre = 'titre',
                $description = 'description',
                $type = 5
            )
            ->if(
                $this->reponseValidator->getMockController()->valider = function () {
                    throw new \Exception('Erreur');
                },
                $reponseManager = $this->getTestedInstance()
            )
            ->then
                ->exception(function () use ($reponseManager, $depot, $titre, $description, $type) {
                    $reponseManager->creer($depot, $titre, $description, $type);
                })
                    ->isInstanceOf(\Exception::class)
                    ->hasMessage('Erreur')
                ->mock($this->reponseFactory)
                    ->call('creer')
                        ->withArguments($depot, $titre, $description, $type)
                        ->once()
                ->mock($this->reponseValidator)
                    ->call('valider')
                        ->withArguments($this->getReponse())
                        ->once()
                ->mock($this->entityManagerInterface)
                    ->call('persist')
                        ->never()
                    ->call('flush')
                        ->never()
        ;
    }
//
//    public function testValiderOk()
//    {
//        $this->assert('Test validation réponse')
//            ->given(
//                $ids = [1,2],
//                $reason = 'lorem'
//            )
//            ->if(
//                $manager = $this->getTestedInstance()
//            )
//            ->then
//                ->mock($this->entityManagerInterface)
//                    ->call('persist')
//                        ->withArguments($this->getReponse())
//                        ->once()
//                    ->call('flush')
//                        ->once()
//        ;
//    }

    private function getReponse()
    {
        return new \mock\App\Entity\DemandeClinique\Reponse();
    }

    private function getDepot()
    {
        return new \mock\App\Entity\DemandeClinique\Depot();
    }

    private function getTestedInstance()
    {
        return $this->newTestedInstance(
            $this->reponseFactory,
            $this->entityManagerInterface,
            $this->reponseValidator,
            $this->reponseRepository
        );
    }
}
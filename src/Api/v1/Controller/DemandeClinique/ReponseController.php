<?php

namespace App\Api\v1\Controller\DemandeClinique;

use App\Manager\DemandeClinique\ReponseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/demande-clinique")
 */
class ReponseController extends AbstractController
{

    /**
     * @Route("/reponses/valider", name="api_v1_valider_reponses", methods={"POST"})
     */
    public function creerReponse(Request $request, ReponseManager $reponseManager): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $reponseManager->valider($content['ids'], $content['reason']);

        return $this->json([], Response::HTTP_CREATED);
    }

}
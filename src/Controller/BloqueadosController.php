<?php

namespace App\Controller;

use App\Entity\Bloqueados;
use App\Repository\BloqueadosRepository;
use App\Utils\JsonResponseConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BloqueadosController extends AbstractController
{

    #[Route('/bloqueados', name: 'bloqueados')]
    public function listarbloqueados(BloqueadosRepository $bloqueadosRepository): JsonResponse
    {
        $jsonConverter = new JsonResponseConverter();

        $listLogin = $bloqueadosRepository->findAll();

        $listJson = $jsonConverter->toJson($listLogin);

        return new JsonResponse($listJson, 200, [], true);

    }

}
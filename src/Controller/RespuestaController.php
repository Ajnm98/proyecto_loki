<?php

namespace App\Controller;
use App\Repository\RespuestaRepository;
use App\Utils\JsonResponseConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
class RespuestaController extends AbstractController
{
    #[Route('/respuesta', name: 'respuesta')]
    public function listar(RespuestaRepository $respuestaRepository): JsonResponse
    {
        $jsonConverter = new JsonResponseConverter();

        $listLogin = $respuestaRepository->findAll();
//        return $this->json($listLogin);

        $listJson = $jsonConverter->toJson($listLogin);

        return new JsonResponse($listJson, 200, [], true);
    }
}
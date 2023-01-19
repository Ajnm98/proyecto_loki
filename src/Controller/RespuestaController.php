<?php

namespace App\Controller;
use App\Repository\RespuestaRepository;
use App\Utils\JsonResponseConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class RespuestaController extends AbstractController
{
    #[Route('/respuesta', name: 'respuesta')]
    public function listar(RespuestaRepository $respuestaRepository): JsonResponse
    {

        $listRespuesta = $respuestaRepository->findAll();

        return $this->json($listRespuesta, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);
//        return $this->json($listLogin);
//        $jsonConverter = new JsonResponseConverter();
//        $listJson = $jsonConverter->toJson($listLogin);
//        return new JsonResponse($listJson, 200, [], true);
    }
}
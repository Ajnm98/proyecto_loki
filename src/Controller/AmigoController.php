<?php

namespace App\Controller;

use App\Repository\AmigosRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Prueba;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AmigoController extends AbstractController
{
    #[Route('/amigos', name: 'amigos')]
    public function listar(AmigosRepository $amigosRepository): JsonResponse
    {
        $jsonConverter = new JsonResponseConverter();

        $listAmigos = $amigosRepository->findAll();
        $listJson = $jsonConverter->toJson($listAmigos);
        return new JsonResponse($listJson, 200, [], true);
    }

}
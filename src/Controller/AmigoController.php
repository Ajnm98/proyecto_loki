<?php

namespace App\Controller;

use App\Repository\AmigosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $listAmigos = $amigosRepository->findAll();
        return $this->json($listAmigos);

    }
    public function toJson($data): string
    {
        //Inicialización de serializador
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        //Conversion a JSON
        $json = $serializer->serialize($data, 'json');

        return $json;
    }
}
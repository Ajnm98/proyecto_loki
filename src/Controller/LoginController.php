<?php

namespace App\Controller;

use App\Repository\LoginRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function listar(LoginRepository $loginRepository): JsonResponse
    {
        $listLogin = $loginRepository->findAll();
//        return $this->json($listLogin);

        $listJson = $this->toJson($listLogin);

        return new JsonResponse($listJson, 200, [], true);
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
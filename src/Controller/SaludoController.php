<?php

namespace App\Controller;

use App\Repository\LoginRepository;
use App\Repository\MensajeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SaludoController extends AbstractController
{
    #[Route('/mensaje', name: 'app_mensaje')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SaludoController.php',
        ]);
    }
    #[Route('/mensaje/list', name: 'app_mensaje')]
    public function listar(LoginRepository $loginRepository): JsonResponse
    {
        $listLogin = $loginRepository->findAll();

        $listJson = $this->toJson($listLogin);

        return new JsonResponse($listJson, 200, [], true);

    }
}
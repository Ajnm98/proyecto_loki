<?php

namespace App\Controller;

use App\Repository\LoginRepository;
use App\Utils\JsonResponseConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function listar(LoginRepository $loginRepository): JsonResponse
    {
        $listLogin = $loginRepository->findAll();

        return $this->json($listLogin, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);
//        $jsonConverter = new JsonResponseConverter();
//        return $this->json($listLogin);
//        $listJson = $jsonConverter->toJson($listLogin);
//        return new JsonResponse($listJson, 200, [], true);
    }

}
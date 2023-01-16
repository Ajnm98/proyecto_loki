<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Prueba;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function PHPUnit\Framework\isEmpty;

class UsuarioController extends AbstractController
{
    #[Route('/usuario', name: 'usuario')]
public function listar(UsuarioRepository $usuarioRepository): JsonResponse
{
    $jsonConverter = new JsonResponseConverter();

    $listLogin = $usuarioRepository->findAll();
//        return $this->json($listLogin);

    $listJson = $jsonConverter->toJson($listLogin);

    return new JsonResponse($listJson, 200, [], true);
}
}
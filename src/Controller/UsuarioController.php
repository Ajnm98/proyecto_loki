<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Prueba;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


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
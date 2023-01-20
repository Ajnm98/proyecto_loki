<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;



class UsuarioController extends AbstractController
{
    #[Route('/usuario/list', name: 'usuarioListar')]
public function listar(UsuarioRepository $usuarioRepository): JsonResponse
{

    $listLogin = $usuarioRepository->findAll();


    return $this->json($listLogin, 200, [], [
        AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
    ]);
//    $jsonConverter = new JsonResponseConverter();
//        return $this->json($listLogin);
//    $listJson = $jsonConverter->toJson($listLogin);
//    return new JsonResponse($listJson, 200, [], true);
}
    #[Route('/usuario/buscar', name: 'app_usuario_buscar', methods: ['GET'])]
    public function buscarPorNombre(UsuarioRepository $usuarioRepository,
                                    Request $request): JsonResponse
    {
        $nombre = $request->query->get("usuario");

        $parametrosBusqueda = array(
            'usuario' => $nombre
        );

        $listUsuarios = $usuarioRepository->findBy($parametrosBusqueda);

//        $listJson = $utilidades->toJson($listUsuarios);

        return $this->json($listUsuarios, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);

    }





}
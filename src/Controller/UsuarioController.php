<?php

namespace App\Controller;

use App\Repository\AmigosRepository;
use App\Repository\BloqueadosRepository;
use App\Repository\ChatRepository;
use App\Repository\LoginRepository;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\UsuarioRepository;
use JMS\Serializer\Annotation\MaxDepth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Annotation\Groups;
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
    #[Route('/usuario/buscar', name: 'app_usuario_buscar_nombre', methods: ['GET'])]
    public function buscarPorNombre(UsuarioRepository $usuarioRepository,
                                    Request $request): JsonResponse
    {
        $nombre = $request->query->get("usuario");

        $parametrosBusqueda = array(
            'usuario' => $nombre
        );

        $listUsuarios = $usuarioRepository->findBy($parametrosBusqueda);


        return $this->json($listUsuarios, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],

        ]);

    }

    #[Route('/usuario/delete', name: 'respuesta_delete', methods: ['POST'])]
    public function delete(Request $request,ChatRepository $chatRepository,
                           PublicacionRepository $publicacionRepository,RespuestaRepository $respuestaRepository,
                           LoginRepository $loginRepository,UsuarioRepository $usuarioRepository,
                            AmigosRepository $amigosRepository,BloqueadosRepository $bloqueadosRepository): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];
        $bloqueadosRepository->borrarBloqueadosPorUsuario($id);
        $amigosRepository->borrarAmigosPorUsuario($id);
        $chatRepository->borrarChatPorUsuario($id);
        $respuestaRepository->borrarRespuestaPorUsuario($id);
        $publicacionRepository->borrarPublicacionPorUsuario($id);
        $usuarioRepository->borrarUsuario($id);
        $loginRepository->borrarLogin($id);


        return new JsonResponse("{ mensaje: Usuario borrado correctamente }", 200, [], true);

    }



}
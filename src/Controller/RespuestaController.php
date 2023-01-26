<?php

namespace App\Controller;
use App\Repository\RespuestaRepository;
use App\Utils\JsonResponseConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    }

    #[Route('/respuesta/delete', name: 'respuesta_delete', methods: ['POST'])]
    public function delete(Request $request,RespuestaRepository $respuestaRepository): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];
        $respuestaRepository->borrarRespuesta($id);


        return new JsonResponse("{ mensaje: Respuesta borrada correctamente }", 200, [], true);

    }


    #[Route('/respuesta/like', name: 'publicacion_delete', methods: ['POST'])]
    public function sumarLikeRespuesta(Request $request,RespuestaRepository $respuestaRepository): JsonResponse
    {
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];

        $parametrosBusqueda = array(
            'id' => $id
        );

        $publicacion = $respuestaRepository->findOneBy($parametrosBusqueda);


        $likesSumado = $publicacion->getLikes()+1 ;

        $respuestaRepository->sumarLikeRespuesta($id, $likesSumado);

        return new JsonResponse("{ mensaje: Like sumado correctamente }", 200, [], true);


    }
}
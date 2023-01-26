<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\AmigosRepository;
use App\Repository\ChatRepository;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PublicacionController extends AbstractController
{

    #[Route('/publicacion', name: 'publicacion')]
    public function listarpublicacion(PublicacionRepository $publicacionRepository): JsonResponse
    {
        $listPublicacion = $publicacionRepository->findAll();
        return $this->json($listPublicacion, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);

    }

    #[Route('/publicaciones/usuario',  methods: ['GET', 'HEAD'])]
    public function listarPublicacionUsuario(Request $request, PublicacionRepository $publicacionRepository): JsonResponse
    {

        $id = $request->query->get("usuario_id");
        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listPublicacion1 = $publicacionRepository->findBy($parametrosBusqueda);
        return $this->json($listPublicacion1);
    }

    #[Route('/publicaciones/usuario/amigo',  methods: ['GET', 'HEAD'])]
    public function listarPublicacionUsuarioAmigos(Request $request,AmigosRepository $amigosRepository, PublicacionRepository $publicacionRepository)//: JsonResponse
    {

        $json = json_decode($request->getContent(), true);
        $array = array();

        $id = $json['usuario_id'];

        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listAmigos = $amigosRepository->findBy($parametrosBusqueda);

        foreach ($listAmigos as $amigo){


            $valoramigo = $amigo->getAmigoId();

            $parametrosBusqueda2 = array(
                'usuario_id' => $valoramigo
            );

            array_push($array, $publicacionRepository->findBy($parametrosBusqueda2,[]));
        }


        return $this->json($array, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);
    }


    //BORRA PUBLICACION CON LAS RESPUESTAS ASOCIADAS
    #[Route('/publicacion/delete', name: 'publicacion_delete', methods: ['POST'])]
    public function delete(Request $request,PublicacionRepository $publicacionRepository,RespuestaRepository $respuestaRepository): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];
        $respuestaRepository->borrarTodasRespuestasPorPublicacion($id);
        $publicacionRepository->borrarPublicacion($id);

        return new JsonResponse("{ mensaje: Publicacion borrada correctamente }", 200, [], true);

    }


    #[Route('/publicacion/like', name: 'publicacion_delete', methods: ['POST'])]
    public function sumarLike(Request $request,PublicacionRepository $publicacionRepository): JsonResponse
    {
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];

        $parametrosBusqueda = array(
            'id' => $id
        );

        $publicacion = $publicacionRepository->findOneBy($parametrosBusqueda);

        $likesSumado = $publicacion->getLikes()+1 ;

        $publicacionRepository->sumarLike($id, $likesSumado);

        return new JsonResponse("{ mensaje: Like sumado correctamente }", 200, [], true);


    }




}
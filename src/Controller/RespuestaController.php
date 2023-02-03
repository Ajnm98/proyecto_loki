<?php

namespace App\Controller;
use App\Entity\Respuesta;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\UsuarioRepository;
use App\Utils\JsonResponseConverter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class RespuestaController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }
    #[Route('/respuesta/list', name: 'respuesta_listar', methods: ['GET'])]
    public function listar(RespuestaRepository $respuestaRepository): JsonResponse
    {

        $listRespuesta = $respuestaRepository->findAll();

        return $this->json($listRespuesta, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
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
    #[Route('/respuesta/save', name: 'respuesta_save', methods: ['POST'])]
    public function save(PublicacionRepository $publicacionRepository,UsuarioRepository $usuarioRepository,RespuestaRepository $respuestaRepository,Request $request): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $nuevaRespuesta = new Respuesta();

        $usuarioId = $json['usuario_id'];
        $publicacionId = $json['publicacion_id'];

        $usuario= $usuarioRepository->findOneBy(array("id"=>$usuarioId));
        $publicacion= $publicacionRepository->findOneBy(array("id"=>$publicacionId));

        $nuevaRespuesta->setUsuarioId($usuario);
        $nuevaRespuesta->setPublicacionId($publicacion);
        $nuevaRespuesta->setTexto($json['texto']);
        $nuevaRespuesta->setFecha(date('Y-m-d H:i:s'));
        $nuevaRespuesta->setFoto($json['foto']);
        //GUARDAR
        $em = $this-> doctrine->getManager();
        $em->persist($nuevaRespuesta);
        $em-> flush();

        return new JsonResponse("{ mensaje: Respuesta publicada correctamente }", 200, [], true);
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
    #[Route('/respuesta/buscar-por-publicacion', name: 'respuesta_buscar_por_publicacion', methods: ['GET'])]
    public function buscarPorNombre(RespuestaRepository $respuestaRepository,
                                    Request $request): JsonResponse
    {
        $id = $request->query->get("publicacion_id");

        $parametrosBusqueda = array(
            'publicacion_id' => $id
        );

        $listRespuestas = $respuestaRepository->findBy($parametrosBusqueda);

        return $this->json($listRespuestas, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);
    }


}
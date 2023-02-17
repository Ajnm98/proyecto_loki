<?php

namespace App\Controller;
use App\Dto\BorrarRespuestaDTO;
use App\Dto\CrearRespuestaDTO;
use App\Dto\DtoConverters;
use App\Dto\RespuestaDTO;
use App\Entity\Respuesta;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\UsuarioRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Utilidades;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use ReallySimpleJWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use OpenApi\Attributes as OA;

class RespuestaController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }
    #[Route('/api/respuesta/list', name: 'respuesta_listar', methods: ['GET'])]
    #[OA\Tag(name: 'Respuesta')]
//    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: RespuestaDTO::class))))]
//    #[OA\Response(response: 401,description: "Unauthorized")]
    public function listar(RespuestaRepository $respuestaRepository,Utilidades $utils, Request $request,
                           DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
//        if($utils->comprobarPermisos($request, 0)) {
        $listRespuesta = $respuestaRepository->findAll();

        foreach ($listRespuesta as $user) {
            $usuarioDto = $converters->respuestaToDto($user);
            $json = $jsonResponseConverter->toJson($usuarioDto, null);
            $listJson[] = json_decode($json);
        }

        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                return $obj->getId();
            },
        ]);
//    }
//        else{return new JsonResponse("{ message: Unauthorized}", 401,[],false);}

    }

    #[Route('/api/respuesta/delete', name: 'respuestaDelete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Respuesta')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto de la respuesta", required: true, content: new OA\JsonContent(ref: new Model(type:BorrarRespuestaDTO::class)))]
    #[OA\Response(response: 200,description: "Respuesta borrada correctamente")]
    #[OA\Response(response: 300,description: "No se pudo borrar correctamente")]
    #[OA\Response(response: 400,description: "No puedes borrar respuestas de otro usuario")]
    public function delete(Request $request,RespuestaRepository $respuestaRepository,Utilidades $utils): JsonResponse
    {

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);

        $id = $json['id'];
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];

        if ($utils->comprobarPermisos($request, 0)) {
            $respuestaRepository->borrarRespuesta($id);

            return new JsonResponse("{ mensaje: Respuesta borrada correctamente }", 200, [], true);
        } elseif ($utils->comprobarPermisos($request, 1)) {

            if ($id != $idu) {
                return new JsonResponse("{ mensaje: No puedes borrar respuestas de otro usuario}", 400, [], true);
            } else {
                $respuestaRepository->borrarRespuesta($id);
                return new JsonResponse("{ mensaje: Respuesta borrada correctamente }", 200, [], true);
            }

        } else {
            return new JsonResponse("{ mensaje: No se pudo borrar correctamente }", 300, [], true);
        }
    }


    #[Route('/api/respuesta/save', name: 'respuestaSave', methods: ['POST'])]
    #[OA\Tag(name: 'Respuesta')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto de la respuesta", required: true, content: new OA\JsonContent(ref: new Model(type:CrearRespuestaDTO::class)))]
    #[OA\Response(response: 200,description: "Respuesta publicada correctamente")]
    #[OA\Response(response: 300,description: "No se pudo guardar correctamente")]
    #[OA\Response(response: 400,description: "No puedes publicar respuestas de otro usuario")]
    public function save(PublicacionRepository $publicacionRepository,Utilidades $utils,
                         UsuarioRepository $usuarioRepository,RespuestaRepository $respuestaRepository,Request $request): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $nuevaRespuesta = new Respuesta();

        $usuarioId = $json['usuarioId'];
        $publicacionId = $json['publicacionId'];
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];

        if ($utils->comprobarPermisos($request, 0)) {
            $usuario = $usuarioRepository->findOneBy(array("id" => $usuarioId));
            $publicacion = $publicacionRepository->findOneBy(array("id" => $publicacionId));

            $nuevaRespuesta->setUsuarioId($usuario);
            $nuevaRespuesta->setPublicacionId($publicacion);
            $nuevaRespuesta->setTexto($json['texto']);
            $nuevaRespuesta->setFecha(date('Y-m-d H:i:s'));
            $nuevaRespuesta->setFoto($json['foto']);
            //GUARDAR
            $em = $this->doctrine->getManager();
            $em->persist($nuevaRespuesta);
            $em->flush();

            return new JsonResponse("{ mensaje: Respuesta publicada correctamente }", 200, [], true);
        }
        elseif ($utils->comprobarPermisos($request, 1)) {
            if ($usuarioId != $idu) {
                return new JsonResponse("{ mensaje: No puedes publicar respuestas de otro usuario}", 400, [], true);
            }
            else{
                $usuario = $usuarioRepository->findOneBy(array("id" => $usuarioId));
                $publicacion = $publicacionRepository->findOneBy(array("id" => $publicacionId));

                $nuevaRespuesta->setUsuarioId($usuario);
                $nuevaRespuesta->setPublicacionId($publicacion);
                $nuevaRespuesta->setTexto($json['texto']);
                $nuevaRespuesta->setFecha(date('Y-m-d H:i:s'));
                $nuevaRespuesta->setFoto($json['foto']);
                //GUARDAR
                $em = $this->doctrine->getManager();
                $em->persist($nuevaRespuesta);
                $em->flush();

                return new JsonResponse("{ mensaje: Respuesta publicada correctamente }", 200, [], true);
            }
        }
        else {
            return new JsonResponse("{ mensaje: No se pudo guardar correctamente }", 300, [], true);
        }

    }


    #[Route('/api/respuesta/like', name: 'respuestaLike', methods: ['POST'])]
    #[OA\Tag(name: 'Respuesta')]
    #[OA\RequestBody(description: "Dto de la respuesta", required: true, content: new OA\JsonContent(ref: new Model(type:BorrarRespuestaDTO::class)))]
    #[OA\Response(response: 200,description: "Like sumado correctamente")]
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
    #[Route('/api/respuesta/buscar-por-publicacion', name: 'respuestaBuscarPorPublicacion', methods: ['GET'])]
    #[OA\Tag(name: 'Respuesta')]
    #[OA\Parameter(name: "publicacion_id", description: "Id de la publicacion", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: RespuestaDTO::class))))]
    public function buscarPorNombre(RespuestaRepository $respuestaRepository,
                                    Request $request,DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        $id = $request->query->get("publicacion_id");

        $parametrosBusqueda = array(
            'publicacion_id' => $id
        );

        $listRespuestas = $respuestaRepository->findBy($parametrosBusqueda);

        foreach($listRespuestas as $user){
            $usuarioDto = $converters->respuestaToDto($user);
            $json = $jsonResponseConverter->toJson($usuarioDto,null);
            $listJson[] = json_decode($json);
        }

        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);
    }


    #[Route('/api/respuesta/dislike', name: 'respuestaDislike', methods: ['POST'])]
    #[OA\Tag(name: 'Respuesta')]
    #[OA\RequestBody(description: "Dto de la respuesta", required: true, content: new OA\JsonContent(ref: new Model(type:BorrarRespuestaDTO::class)))]
    #[OA\Response(response: 200,description: "Like restado correctamente")]
    public function restarLikeRespuesta(Request $request,RespuestaRepository $respuestaRepository): JsonResponse
    {
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];

        $parametrosBusqueda = array(
            'id' => $id
        );

        $publicacion = $respuestaRepository->findOneBy($parametrosBusqueda);


        $likesSumado = $publicacion->getLikes()-1 ;

        $respuestaRepository->sumarLikeRespuesta($id, $likesSumado);

        return new JsonResponse("{ mensaje: Like restado correctamente }", 200, [], true);


    }


}
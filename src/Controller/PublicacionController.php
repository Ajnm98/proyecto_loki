<?php

namespace App\Controller;

use App\Dto\BorrarPublicacionDTO;
use App\Dto\CrearPublicacionDTO;
use App\Dto\DtoConverters;
use App\Dto\PublicacionDTO;
use App\Dto\SumarRestarLikeDTO;
use App\Entity\Likes;
use App\Entity\LikesUsuario;
use App\Entity\Publicacion;
use App\Entity\PublicacionTags;
use App\Entity\Tags;
use App\Entity\Usuario;
use App\Repository\AmigosRepository;
use App\Repository\ChatRepository;
use App\Repository\LikesRepository;
use App\Repository\LikesUsuarioRepository;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\TagsRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
use App\Utils\Utilidades;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Util;
use ReallySimpleJWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Constraints\Date;
use OpenApi\Attributes as OA;
use function PHPUnit\Framework\isEmpty;

class PublicacionController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('/api/publicacion/list', name: 'listar_publicacion', methods: ['GET'])]
    #[OA\Tag(name: 'Publicacion')]
//    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: PublicacionDTO::class))))]
//    #[OA\Response(response: 401,description: "Unauthorized")]
    public function listarpublicacion(PublicacionRepository $publicacionRepository,Utilidades $utils, Request $request,
                                      DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
//        if($utils->comprobarPermisos($request, 0)) {
            $listPublicacion = $publicacionRepository->findAll();

            foreach ($listPublicacion as $user) {
                $usuarioDto = $converters->publicacionToDto($user);
                $json = $jsonResponseConverter->toJson($usuarioDto, null);
                $listJson[] = json_decode($json);
            }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                },
            ]);
//        }else{return new JsonResponse("{ message: Unauthorized}", 401,[],false);}
    }

    #[Route('/api/publicaciones/usuario',  methods: ['GET'])]
    #[OA\Tag(name: 'Publicacion')]
    #[Security(name: "apikey")]
    #[OA\Parameter(name: "usuario_id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: PublicacionDTO::class))))]
    #[OA\Response(response: 300,description: "No se puede ver las publicaciones")]
    public function listarPublicacionUsuario(Request $request, PublicacionRepository $publicacionRepository, Utilidades $utils,
                                             DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {

        $id = $request->query->get("usuario_id");
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];


        if($utils->comprobarPermisos($request, 0)) {
            $parametrosBusqueda = array(
                'usuario_id' => $id
            );

            $listPublicacion1 = $publicacionRepository->findBy($parametrosBusqueda);

            foreach ($listPublicacion1 as $user) {
                $usuarioDto = $converters->publicacionToDto($user);
                $json = $jsonResponseConverter->toJson($usuarioDto, null);
                $listJson[] = json_decode($json);
            }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                },

            ]);
        }
        if($utils->comprobarPermisos($request, 1)) {
            $parametrosBusqueda = array(
                'usuario_id' => $idu
            );

            $listPublicacion1 = $publicacionRepository->findBy($parametrosBusqueda);

            foreach ($listPublicacion1 as $user) {
                $usuarioDto = $converters->publicacionToDto($user);
                $json = $jsonResponseConverter->toJson($usuarioDto, null);
                $listJson[] = json_decode($json);
            }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                },

            ]);
        }
        else{
            return new JsonResponse("{ mensaje: No se puede ver las publicaciones }", 300, [], true);
        }
    }

    #[Route('/api/publicaciones/usuario/amigo',  methods: ['GET'])]
    #[OA\Tag(name: 'Publicacion')]
    #[Security(name: "apikey")]
    #[OA\Parameter(name: "usuario_id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: PublicacionDTO::class))))]
    #[OA\Response(response: 400,description: "No puedes ver las publicaciones de los amigos de otro usuario")]
    #[OA\Response(response: 300,description: "No se puede ver las publicaciones")]
    public function listarPublicacionUsuarioAmigos(Request $request,AmigosRepository $amigosRepository, Utilidades $utils,
                                                   PublicacionRepository $publicacionRepository,DtoConverters $converters, JsonResponseConverter $jsonResponseConverter)//: JsonResponse
    {

//        $json = json_decode($request->getContent(), true);

        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];;
        $id = $request->query->get("usuario_id");
        $array = array();


        if($utils->comprobarPermisos($request, 0)) {

            $parametrosBusqueda = array(
                'usuario_id' => $id
            );

            $listAmigos = $amigosRepository->findBy($parametrosBusqueda);

            foreach ($listAmigos as $amigo) {
                $valoramigo = $amigo->getAmigoId();
                array_push($array, $valoramigo);
            }

            $parametrosBusqueda2 = array(
                'usuario_id' => $array
            );

            $array2 =$publicacionRepository->findBy($parametrosBusqueda2, []);

            return $this->json($array2, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                },

            ]);
        }
        elseif($utils->comprobarPermisos($request, 1)){
//            if($idu!=$id){
//                return new JsonResponse("{ mensaje: No puedes ver las publicaciones de los amigos de otro usuario}", 400, [], true);
//            }
//            else{
                $parametrosBusqueda = array(
                    'usuario_id' => $idu
                );

                $listAmigos = $amigosRepository->findBy($parametrosBusqueda);

                foreach ($listAmigos as $amigo) {
                    $valoramigo = $amigo->getAmigoId();
                    array_push($array, $valoramigo);
                }

                $parametrosBusqueda2 = array(
                    'usuario_id' => $array
                );

                $array2 =$publicacionRepository->findBy($parametrosBusqueda2, []);

                return $this->json($array2, 200, [], [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                    ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                        return $obj->getId();
                    },

                ]);
//            }
        }
        else{
            return new JsonResponse("{ mensaje: No se puede ver las publicaciones }", 300, [], true);
        }
    }


    //BORRA PUBLICACION CON LAS RESPUESTAS ASOCIADAS
    #[Route('/api/publicacion/delete', name: 'publicaciondelete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Publicacion')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:BorrarPublicacionDTO::class)))]
    #[OA\Response(response: 200,description: "Publicacion borrada correctamente")]
    #[OA\Response(response: 300,description: "No se pudo borrar correctamente")]
    #[OA\Response(response: 400,description: "No puedes borrar publicaciones de otro usuario")]
    public function delete(Request $request,PublicacionRepository $publicacionRepository,
                           Utilidades $utils,RespuestaRepository $respuestaRepository): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        $id = $json['id'];
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];

        if($utils->comprobarPermisos($request, 0)) {
            $respuestaRepository->borrarTodasRespuestasPorPublicacion($id);
            $publicacionRepository->borrarPublicacion($id);
            return new JsonResponse("{ mensaje: Publicacion borrada correctamente }", 200, [], true);
        }
        elseif($utils->comprobarPermisos($request, 1)) {

                if ($id != $idu) {
                    return new JsonResponse("{ mensaje: No puedes borrar publicaciones de otro usuario}", 400, [], true);
                } else {
                    $respuestaRepository->borrarTodasRespuestasPorPublicacion($id);
                    $publicacionRepository->borrarPublicacion($id);
                    return new JsonResponse("{ mensaje: Publicacion borrada correctamente }", 200, [], true);
                }
            }
        else{
                return new JsonResponse("{ mensaje: No se pudo borrar correctamente }", 300, [], true);
            }
        }

#[Route('/api/publicacion/save', name: 'publicacion_crear', methods: ['POST'])]
#[OA\Tag(name: 'Publicacion')]
#[Security(name: "apikey")]
#[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CrearPublicacionDTO::class)))]
#[OA\Response(response: 200,description: "Publicacion creada correctamente")]
#[OA\Response(response: 300,description: "No se pudo crear correctamente")]
#[OA\Response(response: 400,description: "No puedes crear publicaciones de otro usuario")]
    public function save(UsuarioRepository $usuarioRepository,Request $request,
                         Utilidades $utils,PublicacionRepository $publicacionRepository,
                        TagsRepository $tagsRepository): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $publicacionNuevo = new Publicacion();
        $tagsNuevo = new Tags();
        $publicacionTagsNuevo = new PublicacionTags();
        $usuarioid = $json['usuarioId'];
        $tags = $json['tags'];

        if($utils->comprobarPermisos($request, 0)) {
            $usuario = $usuarioRepository->findOneBy(array("id" => $usuarioid));
            $fecha = date('Y-m-d H:i:s');

            $publicacionNuevo->setUsuarioId($usuario);
            $publicacionNuevo->setTexto($json['texto']);
            $publicacionNuevo->setFecha(date('Y-m-d H:i:s'));
            $publicacionNuevo->setFoto($json['foto']);

            //GUARDAR
            $em = $this->doctrine->getManager();
            $em->persist($publicacionNuevo);
            $em->flush();

            //obtenemos esta publicacion y le adjuntamos los tags



            return new JsonResponse("{ mensaje: Publicacion creada correctamente }", 200, [], true);
        }
        elseif($utils->comprobarPermisos($request, 1)) {
//            if ($usuarioid != $idu) {
//                return new JsonResponse("{ mensaje: No puedes crear publicaciones de otro usuario}", 400, [], true);
//            } else {
                $usuario = $usuarioRepository->findOneBy(array("id" => $idu));
                $fecha = date('Y-m-d H:i:s');

                $publicacionNuevo->setUsuarioId($usuario);
                $publicacionNuevo->setTexto($json['texto']);
                $publicacionNuevo->setFecha(date('Y-m-d H:i:s'));
                $publicacionNuevo->setFoto($json['foto']);

                //GUARDAR
                $em = $this->doctrine->getManager();
                $em->persist($publicacionNuevo);
                $em->flush();

                //creamos el tag
                $tagsNuevo->setNombre($tags);
                $tagsNuevo->setContador(1);
                $tagsNuevo->setFechaExpiracion(date("Y-m-d H:i:s", strtotime('+48 hours')));

                $em = $this->doctrine->getManager();
                $em->persist($tagsNuevo);
                $em->flush();

                //adjuntamos a la tabla intermedia


                $publicacionTagsNuevo->setPublicacionId($publicacionRepository->findOneBy(array("usuario_id"=>$usuario)));
                $publicacionTagsNuevo->setTagsId($tagsRepository->findOneBy(array("nombre"=>$tags)));
                $em = $this->doctrine->getManager();
                $em->persist($publicacionTagsNuevo);
                $em->flush();


                return new JsonResponse("{ mensaje: Publicacion creada correctamente }", 200, [], true);
//            }
            }else{
            return new JsonResponse("{ mensaje: No se pudo crear correctamente }", 300, [], true);
        }

    }
    #[Route('/api/publicacion/like', name: 'publicacionlike', methods: ['POST'])]
    #[OA\Tag(name: 'Publicacion')]
    #[OA\RequestBody(description: "ID publicacion", required: true, content: new OA\JsonContent(ref: new Model(type:SumarRestarLikeDTO::class)))]
    #[OA\Response(response: 200,description: "Like sumado correctamente")]
    public function sumarLike(Request $request,PublicacionRepository $publicacionRepository,
                              UsuarioRepository $usuarioRepository): JsonResponse
    {

        $json  = json_decode($request->getContent(), true);
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];
        $id = $json['id'];


        $parametrosBusqueda = array(
            'id' => $id
        );


        $parametrosBusqueda2 = array(
            'id' => $idu
        );

        $publicacion = $publicacionRepository->findOneBy($parametrosBusqueda);

        $usuario = $usuarioRepository->findOneBy($parametrosBusqueda2);




        $likesSumado = $publicacion->getLikes()+1 ;

        $publicacionRepository->sumarLike($idu, $likesSumado);

        return new JsonResponse("{ mensaje: Like sumado correctamente }", 200, [], true);
    }

    #[Route('/api/publicaciones/mis-publicaciones',  methods: ['GET'])]
    #[OA\Tag(name: 'Publicacion')]
    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: PublicacionDTO::class))))]
    public function listarMisPublicaciones(Request $request, PublicacionRepository $publicacionRepository,
                                             DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        $apikey = $request->headers->get('apikey');
        $id = Token::getPayload($apikey)["user_id"];
        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listPublicacion1 = $publicacionRepository->findBy($parametrosBusqueda);
        if(!isEmpty($listPublicacion1)){
            return new JsonResponse("No tienes Publicaciones",200,[],true);
        }
        foreach($listPublicacion1 as $user){
            $usuarioDto = $converters->publicacionToDto($user);
            $json = $jsonResponseConverter->toJson($usuarioDto,null);
            $listJson[] = json_decode($json);
        }

        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},

        ]);
    }

    #[Route('/api/publicacion/dislike', name: 'publicacionDislike', methods: ['POST'])]
    #[OA\Tag(name: 'Publicacion')]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:SumarRestarLikeDTO::class)))]
    #[OA\Response(response: 200,description: "Like restado correctamente")]
    public function restarLike(Request $request,PublicacionRepository $publicacionRepository): JsonResponse
    {
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];

        $parametrosBusqueda = array(
            'id' => $id
        );

        $publicacion = $publicacionRepository->findOneBy($parametrosBusqueda);

        $likesSumado = $publicacion->getLikes()-1 ;

        $publicacionRepository->sumarLike($id, $likesSumado);

        return new JsonResponse("{ mensaje: Like restado correctamente }", 200, [], true);
    }

    #[Route('/api/publicaciones/publicaciones-por-id',  methods: ['GET'])]
    #[OA\Tag(name: 'Publicacion')]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: PublicacionDTO::class))))]
    public function listarPublicacionesPorId(Request $request, PublicacionRepository $publicacionRepository,
                                           DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        $id = $request->query->get("id");

        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listPublicacion1 = $publicacionRepository->findBy($parametrosBusqueda);
        if(!isEmpty($listPublicacion1)){
            return new JsonResponse("No tienes Publicaciones",200,[],true);
        }
        foreach($listPublicacion1 as $user){
            $usuarioDto = $converters->publicacionToDto($user);
            $json = $jsonResponseConverter->toJson($usuarioDto,null);
            $listJson[] = json_decode($json);
        }

        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},

        ]);
    }


}
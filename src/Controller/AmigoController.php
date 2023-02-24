<?php

namespace App\Controller;

use App\Dto\CrearAmigoDTO;
use App\Dto\DtoConverters;
use App\Dto\UsuarioDTO;
use App\Entity\Amigos;
use App\Entity\Login;
use App\Repository\AmigosRepository;
use App\Repository\UsuarioRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Prueba;
use App\Utils\Utilidades;
use Exception;
use MongoDB\BSON\Undefined;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use ReallySimpleJWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Dto\AmigosDTO;
use function PHPUnit\Framework\isEmpty;

class AmigoController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('api/amigos/list', name: 'amigos' ,methods: ['GET'])]
    #[OA\Tag(name:'Amigos')]
    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: AmigosDTO::class))))]
    #[OA\Response(response: 401,description: "Unauthorized")]
    public function listar(AmigosRepository $amigosRepository,Utilidades $utils, Request $request,
                           DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {

        if($utils->comprobarPermisos($request, 0)) {
            $listAmigos = $amigosRepository->findAll();

            foreach ($listAmigos as $user) {
                $usarioDto = $converters->amigosToDto($user);
                $json = $jsonResponseConverter->toJson($usarioDto, null);
                $listJson[] = json_decode($json);
            }

            return new JsonResponse($listJson, 200, [], false);
        }
        else{return new JsonResponse("{ message: Unauthorized}", 401,[],false);}
//        $jsonConverter = new JsonResponseConverter();
//        $listJson = $jsonConverter->toJson($listAmigos);
//        return new JsonResponse($listJson, 200, [], true);
    }

    #[Route('/api/amigos/save', name: 'amigos_save', methods: ['POST'])]
    #[OA\Tag(name: 'Amigos')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CrearAmigoDTO::class)))]
    #[OA\Response(response: 200,description: "Amigos guardado correctamente")]
    #[OA\Response(response: 300,description: "No se pudo añadir el amigo correctamente")]
    #[OA\Response(response: 400,description: "No puedes añadir amigos a otros usuario")]
    public function save(UsuarioRepository $usuarioRepository,Request $request, Utilidades $utils): JsonResponse
    {


        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $amigoNuevo = new Amigos();

        $id = $json['usuarioId'];
        $amigo = $json['amigoId'];
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];

        if($utils->comprobarPermisos($request, 0)) {

            $parametrosBusqueda = array(
                'id' => $id
            );
            $usuario = $usuarioRepository->findOneBy($parametrosBusqueda);
            $amigoid = $usuarioRepository->findOneBy(array("id" => $amigo));
//        $amigoNuevo->setUsuario_Id($json['usuario_id']);
            $amigoNuevo->setUsuario_Id($usuario);
            $amigoNuevo->setAmigo_Id($amigoid);

            //GUARDAR
            $em = $this->doctrine->getManager();
            $em->persist($amigoNuevo);
            $em->flush();

            return new JsonResponse("Amigo enlazado correctamente ", 200, [], true);

        }
        elseif($utils->comprobarPermisos($request, 1)){

                $parametrosBusqueda = array(
                    'id' => $idu
                );
                $usuario = $usuarioRepository->findOneBy($parametrosBusqueda);
                $amigoid = $usuarioRepository->findOneBy(array("id" => $amigo));
//        $amigoNuevo->setUsuario_Id($json['usuario_id']);
                $amigoNuevo->setUsuario_Id($usuario);
                $amigoNuevo->setAmigo_Id($amigoid);

                //GUARDAR
                $em = $this->doctrine->getManager();
                $em->persist($amigoNuevo);
                $em->flush();

                return new JsonResponse("Amigo enlazado correctamente ", 200, [], true);
            }
        else{
            return new JsonResponse("{ mensaje: No se pudo añadir el amigo correctamente }", 300, [], true);
        }


    }
// BUSCA POR ID DE LA RELACION EN LA BBDD, CAMBIAR A BUSCAR POR NOMBRE
    #[Route('/api/amigos/buscar', name: 'amigos_buscar', methods: ['GET'])]
    #[OA\Tag(name: 'Amigos')]
    #[Security(name: "apikey")]
    #[OA\Parameter(name: "id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]
    #[OA\Response(response: 300,description: "No se pudo encontrar los amigos correctamente ")]
    public function buscarPorId(AmigosRepository $amigosRepository, Request $request, Utilidades $utils,
                                DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        $id = $request->query->get("id");
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];


        if($utils->comprobarPermisos($request, 0)) {
            $parametrosBusqueda = array(
                'id' => $id
            );

            $listAmigos = $amigosRepository->findBy($parametrosBusqueda);

            foreach ($listAmigos as $user) {
                $usarioDto = $converters->amigosToDto($user);
                $json = $jsonResponseConverter->toJson($usarioDto, null);
                $listJson[] = json_decode($json);
            }


//        $listJson = $utilidades->toJson($listUsuarios);

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);
        }
        elseif($utils->comprobarPermisos($request, 1)){
            $parametrosBusqueda = array(
                'id' => $idu
            );

            $listAmigos = $amigosRepository->findBy($parametrosBusqueda);

            foreach ($listAmigos as $user) {
                $usarioDto = $converters->amigosToDto($user);
                $json = $jsonResponseConverter->toJson($usarioDto, null);
                $listJson[] = json_decode($json);
            }


//        $listJson = $utilidades->toJson($listUsuarios);

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);

        }else{ return new JsonResponse("{ mensaje: No se pudo encontrar los amigos correctamente }", 300, [], true);}



    }
    #[Route('/api/amigos/delete', name: 'amigos_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Amigos')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CrearAmigoDTO::class)))]
    #[OA\Response(response: 200,description: "Amigo borrado correctamente")]
    #[OA\Response(response: 300,description: "No se pudo borrar el amigo correctamente")]
    #[OA\Response(response: 400,description: "No puedes borrar amigos de otro usuario")]
    public function delete(UsuarioRepository $usuarioRepository,
                           Utilidades $utils, Request $request,AmigosRepository $amigosRepository): JsonResponse
    {

        $json  = json_decode($request->getContent(), true);
        $apikey = $request->headers->get('apikey');
        $idu = $json['usuarioId'];
        $amigo = $json['amigoId'];

        if($utils->comprobarPermisos($request, 0)) {
            //CREAR NUEVO USUARIO A PARTIR DEL JSON
            $amigoNuevo = new Amigos();

            $id = $json['usuarioId'];
            $amigo = $json['amigoId'];

            $amigosRepository->borrarAmigo($id, $amigo);

            return new JsonResponse("{ mensaje: Amigo borrado correctamente }", 200, [], true);
        }
        elseif($utils->comprobarPermisos($request, 1)){

                $amigosRepository->borrarAmigo($idu, $amigo);
                return new JsonResponse("{ mensaje: Amigo borrado correctamente }", 200, [], true);

            }
        else{
            return new JsonResponse("{ mensaje: No se pudo borrar el amigo correctamente }", 300, [], true);
        }
    }

    #[Route('/api/amigos/mis-amigos', name: 'mis-amigos', methods: ['GET'])]
    #[OA\Tag(name: 'Amigos')]
    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]
    #[OA\Response(response: 300,description: "Sin amigos")]
    public function buscarMisAmigos(AmigosRepository $amigosRepository, Request $request,  DtoConverters $converters,
                                    JsonResponseConverter $jsonResponseConverter, Utilidades $utils): JsonResponse
    {

            $apikey = $request->headers->get('apikey');
            $id = Token::getPayload($apikey)["user_id"];

            $parametrosBusqueda = array(
                'usuario_id' => $id
            );

            $listAmigos = $amigosRepository->findBy($parametrosBusqueda);
            if(count($listAmigos)==0){
                return new JsonResponse("Sin amigos",300,[],true);
            }

            foreach($listAmigos as $user){
                $usarioDto = $converters-> amigosToDto($user);
                $usuario2 = $usarioDto->getAmigoId();
                $json = $jsonResponseConverter->toJson($usuario2,null);
                $listJson[] = json_decode($json);
            }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();}
            ]);

        }

    #[Route('/api/amigos/buscarAmigo', name: 'amigo_buscar_id', methods: ['GET'])]
    #[OA\Tag(name: 'Amigos')]
    #[Security(name: "apikey")]
    #[OA\Parameter(name: "usuario_id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Parameter(name: "usuario_amigo", description: "Nombre de usuario del amigo", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]
    #[OA\Response(response: 300,description: "No existe el usuario amigo")]
    #[OA\Response(response: 400,description: "No se pudo buscar el usuario amigo")]
    public function buscarAmigo(AmigosRepository $amigosRepository, Utilidades $utils,
                                    Request $request,DtoConverters $converters, UsuarioRepository $usuarioRepository, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {

        $json = json_decode($request->getContent(), true);

        $id_usuario = $request->query->get("usuario_id");
        $amigo = $request->query->get("usuario_amigo");
        $apikey = $request->headers->get('apikey');
        $id = Token::getPayload($apikey)["user_id"];

        if ($utils->comprobarPermisos($request, 0)) {
            $parametrosBusqueda = array(
                'usuario' => $amigo
            );

            $amigo = $usuarioRepository->findOneBy($parametrosBusqueda, []);

            if ($amigo != null) {
                $amigo_id = $amigo->getId();
            } else {


                return new JsonResponse("{ mensaje: No existe el usuario amigo }", 200, [], true);
            }
            $parametrosBusqueda2 = array(
                'usuario_id' => $id_usuario,
                'amigo_id' => $amigo_id
            );

            $listAmigo = $amigosRepository->findBy($parametrosBusqueda2, []);

            foreach ($listAmigo as $user) {
                $usarioDto = $converters->amigosToDto($user);
                $usuario2 = $usarioDto->getAmigoId();
                $json = $jsonResponseConverter->toJson($usuario2, null);
                $listJson[] = json_decode($json);
            }
            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);
        }
        elseif($utils->comprobarPermisos($request, 1)){
            $parametrosBusqueda = array(
                'usuario' => $amigo
            );

            $amigo = $usuarioRepository->findOneBy($parametrosBusqueda, []);

            if ($amigo != null) {
                $amigo_id = $amigo->getId();
            } else {
                return new JsonResponse("{ mensaje: No existe el usuario amigo }", 300, [], true);
            }
            $parametrosBusqueda2 = array(
                'usuario_id' => $id,
                'amigo_id' => $amigo_id
            );

            $listAmigo = $amigosRepository->findBy($parametrosBusqueda2, []);

            foreach ($listAmigo as $user) {
                $usarioDto = $converters->amigosToDto($user);
                $usuario2 = $usarioDto->getAmigoId();
                $json = $jsonResponseConverter->toJson($usuario2, null);
                $listJson[] = json_decode($json);
            }
            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);
        }
        return new JsonResponse("{ mensaje: No se pudo buscar el usuario amigo }", 400, [], true);
        }




}
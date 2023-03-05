<?php

namespace App\Controller;

use App\Dto\BloqueadosDTO;
use App\Dto\CrearBloqueadoDTO;
use App\Dto\DtoConverters;
use App\Entity\Bloqueados;
use App\Entity\Usuario;
use App\Repository\BloqueadosRepository;
use App\Repository\ChatRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
use App\Utils\Utilidades;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use ReallySimpleJWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use OpenApi\Attributes as OA;


class BloqueadosController extends AbstractController
{



    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/api/bloqueados/list', name: 'bloqueados',methods: ['GET'])]
    #[OA\Tag(name:'Bloqueados')]
    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: BloqueadosDTO::class))))]
    #[OA\Response(response: 401,description: "Unauthorized")]
    public function listarbloqueados(BloqueadosRepository $bloqueadosRepository,Utilidades $utils, Request $request,
                                     DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        if($utils->comprobarPermisos($request, 0)) {
            $listbloqueados = $bloqueadosRepository->findAll();

            foreach ($listbloqueados as $user) {
                $usarioDto = $converters->BloqueadoToDto($user);
                $json = $jsonResponseConverter->toJson($usarioDto, null);
                $listJson[] = json_decode($json);
            }


            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                },
            ]);
        }
        else{return new JsonResponse("{ message: Unauthorized}", 401,[],false);}
//        $jsonConverter = new JsonResponseConverter();
//        $listJson = $jsonConverter->toJson($listLogin);
//       return new JsonResponse($listJson, 200, [], true);

    }

    #[Route('/api/bloqueados/bloquear',  methods: ['POST'])]
    #[OA\Tag(name: 'Bloqueados')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CrearBloqueadoDTO::class)))]
    #[OA\Response(response: 200,description: "Usuario bloqueado correctamente")]
    #[OA\Response(response: 300,description: "No se pudo bloquear correctamente")]
    #[OA\Response(response: 400,description: "No puedes bloquear usuarios a otros usuario")]
    public function bloquearUsuario(Request $request, UsuarioRepository $usuarioRepository,Utilidades $utils,
                                    BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {

        $json = json_decode($request->getContent(), true);
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];;
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $bloqueadosNuevo = new Bloqueados();

        $id = $json['usuarioId'];
        $bloqueado_id = $json['bloqueadoId'];

        if($utils->comprobarPermisos($request, 0)) {
            $parametrosBusqueda = array(
                'id' => $id
            );

            $parametrosBusqueda2 = array(
                'id' => $bloqueado_id
            );


            $usuario1 = $usuarioRepository->findOneBy($parametrosBusqueda);
            $usuario2 = $usuarioRepository->findOneBy($parametrosBusqueda2);


            $bloqueadosNuevo->setUsuarioId($usuario1);
            $bloqueadosNuevo->setBloqueadoId($usuario2);

            $em = $this->doctrine->getManager();
            $em->persist($bloqueadosNuevo);
            $em->flush();

            return new JsonResponse(" Bloqueado enlazado correctamente ", 200, [], true);
        }
        elseif($utils->comprobarPermisos($request, 1)) {

            if ($idu != $id) {
                return new JsonResponse("{ mensaje: No puedes bloquear usuarios a otros usuario}", 400, [], true);
            }
            else {
                $parametrosBusqueda = array(
                    'id' => $id
                );

                $parametrosBusqueda2 = array(
                    'id' => $bloqueado_id
                );


                $usuario1 = $usuarioRepository->findOneBy($parametrosBusqueda);
                $usuario2 = $usuarioRepository->findOneBy($parametrosBusqueda2);


                $bloqueadosNuevo->setUsuarioId($usuario1);
                $bloqueadosNuevo->setBloqueadoId($usuario2);

                $em = $this->doctrine->getManager();
                $em->persist($bloqueadosNuevo);
                $em->flush();

                return new JsonResponse(" Bloqueado enlazado correctamente ", 200, [], true);
            }
        }
        else{
            return new JsonResponse("{ mensaje: No se pudo bloquear correctamente }", 300, [], true);
        }
    }

    #[Route('/api/bloqueados/listUser', name: 'bloqueadosUsuario',methods: ['GET'])]
    #[OA\Tag(name:'Bloqueados')]
    #[Security(name: "apikey")]
    #[OA\Parameter(name: "id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: BloqueadosDTO::class))))]
    #[OA\Response(response: 300,description: "No se pudo bloquear correctamente")]
    #[OA\Response(response: 400,description: "No hay bloqueados")]
    public function listarbloqueadosUsuario(Request $request,BloqueadosRepository $bloqueadosRepository, Utilidades $utils,
                                            DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {

//        $json = json_decode($request->getContent(), true);

        $id = $request->query->get("id");
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];

        if($utils->comprobarPermisos($request, 0)) {
            $parametrosBusqueda = array(
                'usuario_id' => $id
            );

            $listbloqueados = $bloqueadosRepository->findBy($parametrosBusqueda, []);

            if(count($listbloqueados)==0){
                return new JsonResponse("{ mensaje: No hay bloqueados }", 400, [], true);
            }

            foreach ($listbloqueados as $user) {
                $usarioDto = $converters->BloqueadoToDto($user);
                $usuario2 = $usarioDto->getBloqueadoId();
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
        elseif($utils->comprobarPermisos($request, 1)) {
            $parametrosBusqueda = array(
                'usuario_id' => $idu
            );

            $listbloqueados = $bloqueadosRepository->findBy($parametrosBusqueda, []);

            if(count($listbloqueados)==0){
                return new JsonResponse("{ mensaje: No hay bloqueados }", 400, [], true);
            }

            foreach ($listbloqueados as $user) {
                $usarioDto = $converters->BloqueadoToDto($user);
                $usuario2 = $usarioDto->getBloqueadoId();
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
        else{
            return new JsonResponse("{ mensaje: No se pudo mostrar correctamente }", 300, [], true);
        }
    }


    #[Route('/api/bloqueados/desbloquear',  methods: ['DELETE'])]
    #[OA\Tag(name: 'Bloqueados')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CrearBloqueadoDTO::class)))]
    #[OA\Response(response: 200,description: "Usuario desbloqueado correctamente")]
    #[OA\Response(response: 300,description: "No se pudo desbloquear correctamente")]
    #[OA\Response(response: 400,description: "No puedes desbloquear usuarios de otro usuario")]
    public function desbloquearUsuario(Request $request, UsuarioRepository $usuarioRepository, Utilidades $utils,
                                       BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        $apikey = $request->headers->get('apikey');
        $id_usuario = $json['usuarioId'];
        $id_desbloqueado =$json['bloqueadoId'];
        $idu = Token::getPayload($apikey)["user_id"];

        if($utils->comprobarPermisos($request, 0)) {
            $bloqueadosRepository->desbloquear($id_usuario, $id_desbloqueado);
            return new JsonResponse(" Usuario desbloqueado correctamente ", 200, [], true);
        }
        elseif($utils->comprobarPermisos($request, 1)){
            if($id_usuario!=$idu){
                return new JsonResponse("{ mensaje: No puedes desbloquear usuarios de otro usuario}", 400, [], true);
            }
            else {
                $bloqueadosRepository->desbloquear($id_usuario, $id_desbloqueado);
                return new JsonResponse(" Usuario desbloqueado correctamente ", 200, [], true);
            }
        }

        else{
            return new JsonResponse("{ mensaje: No se pudo desbloquear correctamente }", 300, [], true);
        }
    }

    #[Route('/api/bloqueados/buscar',  methods: ['GET'])]
    #[OA\Tag(name:'Bloqueados')]
    #[Security(name: "apikey")]
    #[OA\Parameter(name: "usuarioId", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Parameter(name: "usuarioBloqueado", description: "El usuario buscado bloqueado", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: BloqueadosDTO::class))))]
    #[OA\Response(response: 100,description: "No existe usuario bloqueado")]
    #[OA\Response(response: 300,description: "No es posible buscar")]
    public function buscarUsuarioBloqueado(Request $request, UsuarioRepository $usuarioRepository,
                                           BloqueadosRepository $bloqueadosRepository, Utilidades $utils,
                                           DtoConverters $converters, JsonResponseConverter $jsonResponseConverter)//: JsonResponse
    {
//        $json  = json_decode($request->getContent(), true);

        $id_usuario = $request->query->get("usuarioId");
        $usuario_bloqueado = $request->query->get("usuarioBloqueado");
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];


        if ($utils->comprobarPermisos($request, 0)) {
            $parametrosBusqueda = array(
                'usuario' => $usuario_bloqueado
            );

            $bloqueado = $usuarioRepository->findOneBy($parametrosBusqueda, []);

            if ($bloqueado = null) {
                $bloqueado_id = $bloqueado->getId();
            } else {

                return new JsonResponse("{ mensaje: No existe usuario bloqueado }", 100, [], true);
            }

            $parametrosBusqueda2 = array(
                'usuario_id' => $id_usuario,
                'bloqueado_id' => $bloqueado_id
            );

            $listbloqueados = $bloqueadosRepository->findBy($parametrosBusqueda2, []);

            foreach ($listbloqueados as $user) {
                $usuarioDto = $converters->BloqueadoToDto($user);
                $usuario = $usuarioDto->getBloqueadoId();
                $json = $jsonResponseConverter->toJson($usuario, null);
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
                'usuario' => $usuario_bloqueado
            );

            $bloqueado = $usuarioRepository->findOneBy($parametrosBusqueda, []);

            if ($bloqueado != null) {
                $bloqueado_id = $bloqueado->getId();
            } else {

                return new JsonResponse("{ mensaje: No existe usuario bloqueado }", 100, [], true);
            }

            $parametrosBusqueda2 = array(
                'usuario_id' => $idu,
                'bloqueado_id' => $bloqueado_id
            );

            $listbloqueados = $bloqueadosRepository->findBy($parametrosBusqueda2, []);

            foreach ($listbloqueados as $user) {
                $usuarioDto = $converters->BloqueadoToDto($user);
                $usuario = $usuarioDto->getBloqueadoId();
                $json = $jsonResponseConverter->toJson($usuario, null);
                $listJson[] = json_decode($json);
            }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);
        }
        else{return new JsonResponse("{ mensaje: No es posible buscar }", 300, [], true);}
        
    }
    
    
    }
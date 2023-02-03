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
use Nelmio\ApiDocBundle\Annotation\Model;
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
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: BloqueadosDTO::class))))]
    public function listarbloqueados(BloqueadosRepository $bloqueadosRepository,  DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {

        $listbloqueados = $bloqueadosRepository->findAll();

        foreach($listbloqueados as $user){
            $usarioDto = $converters->BloqueadoToDto($user);
            $json = $jsonResponseConverter->toJson($usarioDto,null);
            $listJson[] = json_decode($json);
        }


        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);
//        $jsonConverter = new JsonResponseConverter();
//        $listJson = $jsonConverter->toJson($listLogin);
//       return new JsonResponse($listJson, 200, [], true);

    }

    #[Route('/api/bloqueados/bloquear',  methods: ['POST'])]
    #[OA\Tag(name: 'Bloqueados')]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CrearBloqueadoDTO::class)))]
    #[OA\Response(response: 200,description: "Usuario bloqueado correctamente")]
    public function bloquearUsuario(Request $request, UsuarioRepository $usuarioRepository,
                                    BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {

        $json = json_decode($request->getContent(), true);

        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $bloqueadosNuevo = new Bloqueados();

        $id = $json['usuarioId'];
        $bloqueado_id = $json['bloqueadosId'];

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

    #[Route('/api/bloqueados/listUser', name: 'bloqueadosUsuario',methods: ['GET'])]
    #[OA\Tag(name:'Bloqueados')]
    #[OA\Parameter(name: "id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: BloqueadosDTO::class))))]
    public function listarbloqueadosUsuario(Request $request,BloqueadosRepository $bloqueadosRepository,
                                            DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {

//        $json = json_decode($request->getContent(), true);

        $id = $request->query->get("id");

        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listbloqueados = $bloqueadosRepository->findBy($parametrosBusqueda, []);

        foreach($listbloqueados as $user){
            $usarioDto = $converters->BloqueadoToDto($user);
            $usuario2 = $usarioDto->getBloqueadoId();
            $json = $jsonResponseConverter->toJson($usuario2,null);
            $listJson[] = json_decode($json);
        }

        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();}
        ]);

    }


    #[Route('/api/bloqueados/desbloquear',  methods: ['POST'])]
    #[OA\Tag(name: 'Bloqueados')]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CrearBloqueadoDTO::class)))]
    #[OA\Response(response: 200,description: "Usuario desbloqueado correctamente")]
    public function desbloquearUsuario(Request $request, UsuarioRepository $usuarioRepository, BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        $id_usuario = $json['usuarioId'];
        $id_desbloqueado =$json['bloqueadoId'];


        $bloqueadosRepository->desbloquear($id_usuario, $id_desbloqueado);


        return new JsonResponse(" Usuario desbloqueado correctamente ", 200, [], true);

    }

    #[Route('/api/bloqueados/buscar',  methods: ['GET'])]
    #[OA\Tag(name:'Bloqueados')]
    #[OA\Parameter(name: "usuarioId", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Parameter(name: "usuarioBloqueado", description: "El nombre del usuario buscado bloqueado", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: BloqueadosDTO::class))))]
    #[OA\Response(response:100,description:"wrong operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: BloqueadosDTO::class))))]
    public function buscarUsuarioBloqueado(Request $request, UsuarioRepository $usuarioRepository,
                                           BloqueadosRepository $bloqueadosRepository,
                                           DtoConverters $converters, JsonResponseConverter $jsonResponseConverter)//: JsonResponse
    {
//        $json  = json_decode($request->getContent(), true);

        $id_usuario = $request->query->get("usuarioId");
        $usuario_bloqueado = $request->query->get("usuarioBloqueado");;

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
            'usuario_id' => $id_usuario,
            'bloqueado_id'=> $bloqueado_id
        );

        $listbloqueados = $bloqueadosRepository->findBy($parametrosBusqueda2, []);

        foreach($listbloqueados as $user){
            $usuarioDto = $converters->BloqueadoToDto($user);
            $usuario = $usuarioDto->getBloqueadoId();
            $json = $jsonResponseConverter->toJson($usuario,null);
            $listJson[] = json_decode($json);
        }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();}
            ]);
    }


    }
<?php

namespace App\Controller;

use App\Dto\BorrarPublicacionDTO;
use App\Dto\DtoConverters;
use App\Dto\PublicacionDTO;
use App\Entity\Publicacion;
use App\Entity\Usuario;
use App\Repository\AmigosRepository;
use App\Repository\ChatRepository;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Constraints\Date;
use OpenApi\Attributes as OA;

class PublicacionController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('/api/publicacion/list', name: 'listar_publicacion', methods: ['GET'])]
    #[OA\Tag(name: 'Publicacion')]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: PublicacionDTO::class))))]
    public function listarpublicacion(PublicacionRepository $publicacionRepository,
                                      DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        $listPublicacion = $publicacionRepository->findAll();

        foreach($listPublicacion as $user){
            $usuarioDto = $converters->publicacionToDto($user);
            $json = $jsonResponseConverter->toJson($usuarioDto,null);
            $listJson[] = json_decode($json);
        }

        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);

    }

    #[Route('/api/publicaciones/usuario',  methods: ['GET'])]
    #[OA\Tag(name: 'Publicacion')]
    #[OA\Parameter(name: "usuario_id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: PublicacionDTO::class))))]
    public function listarPublicacionUsuario(Request $request, PublicacionRepository $publicacionRepository,
                                             DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {

        $id = $request->query->get("usuario_id");
        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listPublicacion1 = $publicacionRepository->findBy($parametrosBusqueda);

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

    #[Route('/api/publicaciones/usuario/amigo',  methods: ['GET'])]
    #[OA\Tag(name: 'Publicacion')]
    #[OA\Parameter(name: "usuario_id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: PublicacionDTO::class))))]
    public function listarPublicacionUsuarioAmigos(Request $request,AmigosRepository $amigosRepository, PublicacionRepository $publicacionRepository)//: JsonResponse
    {

//        $json = json_decode($request->getContent(), true);
        $array = array();

        $id = $request->query->get("usuario_id");

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
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},

        ]);
    }


    //BORRA PUBLICACION CON LAS RESPUESTAS ASOCIADAS
    #[Route('/api/publicacion/delete', name: 'publicaciondelete', methods: ['POST'])]
    #[OA\Tag(name: 'Publicacion')]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:BorrarPublicacionDTO::class)))]
    #[OA\Response(response: 200,description: "Publicacion borrada correctamente")]
    public function delete(Request $request,PublicacionRepository $publicacionRepository,RespuestaRepository $respuestaRepository): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];
        $respuestaRepository->borrarTodasRespuestasPorPublicacion($id);
        $publicacionRepository->borrarPublicacion($id);

        return new JsonResponse("{ mensaje: Publicacion borrada correctamente }", 200, [], true);

    }

#[Route('/publicacion/save', name: 'publicacion_crear', methods: ['POST'])]
#[OA\Tag(name: 'Publicacion')]
#[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:BorrarPublicacionDTO::class)))]
#[OA\Response(response: 200,description: "Publicacion creada correctamente")]
    public function save(UsuarioRepository $usuarioRepository,Request $request): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $publicacionNuevo = new Publicacion();
        $usuarioid = $json['usuario_id'];
        $usuario = $usuarioRepository->findOneBy(array("id"=>$usuarioid));
        $fecha = date('Y-m-d H:i:s');

        $publicacionNuevo->setUsuarioId($usuario);
        $publicacionNuevo->setTexto($json['texto']);
        $publicacionNuevo->setFecha(date('Y-m-d H:i:s'));
        $publicacionNuevo->setFoto($json['foto']);

        //GUARDAR
        $em = $this-> doctrine->getManager();
        $em->persist($publicacionNuevo);
        $em-> flush();

        return new JsonResponse("{ mensaje: Publicacion creada correctamente }", 200, [], true);
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
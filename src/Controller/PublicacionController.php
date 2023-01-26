<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\Usuario;
use App\Repository\AmigosRepository;
use App\Repository\ChatRepository;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Constraints\Date;

class PublicacionController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('/publicacion/list', name: 'listar_publicacion')]
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

        $id = $request->query->get("id");
        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listPublicacion1 = $publicacionRepository->findBy($parametrosBusqueda);
        return $this->json($listPublicacion1);
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

#[Route('/publicacion/save', name: 'publicacion_crear', methods: ['POST'])]
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

}
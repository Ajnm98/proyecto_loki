<?php

namespace App\Controller;

use App\Entity\Bloqueados;
use App\Entity\Usuario;
use App\Repository\BloqueadosRepository;
use App\Repository\ChatRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class BloqueadosController extends AbstractController
{



    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/bloqueados/list', name: 'bloqueados', methods: ['GET'])]
    public function listarbloqueados(BloqueadosRepository $bloqueadosRepository): JsonResponse
    {

        $listbloqueados = $bloqueadosRepository->findAll();
        return $this->json($listbloqueados, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);
//        $jsonConverter = new JsonResponseConverter();
//        $listJson = $jsonConverter->toJson($listLogin);
//        return new JsonResponse($listJson, 200, [], true);

    }

    #[Route('/bloqueados/bloquear',  methods: ['POST'])]
    public function bloquearUsuario(Request $request, UsuarioRepository $usuarioRepository, BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {

        $json = json_decode($request->getContent(), true);

        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $bloqueadosNuevo = new Bloqueados();

        $id = $json['usuario_id'];
        $bloqueado_id = $json['bloqueados_id'];

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

        return new JsonResponse("{ mensaje: Bloqueado enlazado correctamente }", 200, [], true);

    }

    #[Route('/bloqueados/listUser', name: 'bloqueadosUsuario', methods: ['GET'])]
    public function listarbloqueadosUsuario(Request $request,BloqueadosRepository $bloqueadosRepository): JsonResponse
    {

        $json = json_decode($request->getContent(), true);

        $id = $json['usuario_id'];

        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listbloqueados = $bloqueadosRepository->findBy($parametrosBusqueda, []);

        return $this->json($listbloqueados, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);

    }


#[Route('/bloqueados/desbloquear',  methods: ['POST'])]
    public function desbloquearUsuario(Request $request, UsuarioRepository $usuarioRepository, BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        $id_usuario = $json['usuario_id'];
        $id_desbloqueado =$json['bloqueado_id'];


        $bloqueadosRepository->desbloquear($id_usuario, $id_desbloqueado);


        return new JsonResponse("{ mensaje: Usuario desbloqueado correctamente }", 200, [], true);

    }

    #[Route('/bloqueados/buscar',  methods: ['GET'])]
    public function buscarUsuarioBloqueado(Request $request, UsuarioRepository $usuarioRepository, BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {
        $json  = json_decode($request->getContent(), true);

        $id_usuario = $json['usuario_id'];
        $usuario_bloqueado =$json['usuario_bloqueado'];

        $parametrosBusqueda = array(
            'usuario' => $usuario_bloqueado
        );

        $bloqueado = $usuarioRepository->findOneBy($parametrosBusqueda, []);

        if ($bloqueado != null) {
            $bloqueado_id = $bloqueado->getId();
        } else {

            return new JsonResponse("{ mensaje: No existe usuario bloqueado }", 200, [], true);
        }

        $parametrosBusqueda2 = array(
            'usuario_id' => $id_usuario,
            'bloqueado_id'=> $bloqueado_id
        );

        $listbloqueados = $bloqueadosRepository->findBy($parametrosBusqueda2, []);


            return $this->json($listbloqueados, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
            ]);
    }


    }
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

class BloqueadosController extends AbstractController
{

    private $doctrine;

    #[Route('/bloqueados', name: 'bloqueados')]
    public function listarbloqueados(BloqueadosRepository $bloqueadosRepository): JsonResponse
    {
        $jsonConverter = new JsonResponseConverter();

        $listLogin = $bloqueadosRepository->findAll();

        $listJson = $jsonConverter->toJson($listLogin);

        return new JsonResponse($listJson, 200, [], true);

    }

    #[Route('/bloqueados/bloquear',  methods: ['GET', 'HEAD'])]
    public function bloquearUsuario(Request $request, Request $request2, UsuarioRepository $usuarioRepository)//: JsonResponse
    {


        $usuario_id = $request->query->get("id");

        $usuario_bloqueado = $request2->query->get("id");


        $BloqueadoNuevo = new Bloqueados();

        $parametrosBusqueda = array(
            'id' => $usuario_id
        );

        $Usuario1 = $usuarioRepository->findBy($parametrosBusqueda);

        $parametrosBusqueda2 = array(
            'id' => $usuario_bloqueado
        );

        $Usuario2 = $usuarioRepository->findBy($parametrosBusqueda2);

        $BloqueadoNuevo->setUsuarioId($Usuario1);
        $BloqueadoNuevo->setBloqueadoId($Usuario2['id']);

        $em = $this-> doctrine->getManager();
        $em->persist($BloqueadoNuevo);
        $em-> flush();

        return $this->json("{ mensaje: Usuario creado correctamente }");

    }

}
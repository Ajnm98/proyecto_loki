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


class BloqueadosController extends AbstractController
{



    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/bloqueados', name: 'bloqueados')]
    public function listarbloqueados(BloqueadosRepository $bloqueadosRepository): JsonResponse
    {

        $listbloqueados = $bloqueadosRepository->findAll();
        return $this->json($listbloqueados, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);
//        $jsonConverter = new JsonResponseConverter();
//        $listJson = $jsonConverter->toJson($listLogin);
//        return new JsonResponse($listJson, 200, [], true);

    }

    #[Route('/bloqueados/bloquear',  methods: ['GET', 'HEAD'])]
    public function bloquearUsuario(Request $request, Request $request2, UsuarioRepository $usuarioRepository, BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {

        $usuario_id = $request->query->get("usuario_id");

        $usuario_bloqueado = $request2->query->get("bloqueados_id");


        $BloqueadoNuevo = new Bloqueados();


        $parametrosBusqueda = array(
            'id' => $usuario_id
        );

        $Usuario1 = $usuarioRepository->findBy($parametrosBusqueda,[], limit: 1);

        $parametrosBusqueda2 = array(
            'id' => $usuario_bloqueado
        );

        $Usuario2 = $usuarioRepository->findBy($parametrosBusqueda2,[], limit: 1);

        $BloqueadoNuevo->setUsuarioId($Usuario1[0]);
        $BloqueadoNuevo->setBloqueadoId($Usuario2[0]);

        $em = $this-> doctrine->getManager();
        $bloqueadosRepository->save($BloqueadoNuevo);
        $em-> flush();

        return $this->json("{ mensaje: Usuario creado correctamente }");

    }

}
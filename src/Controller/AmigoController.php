<?php

namespace App\Controller;

use App\Entity\Amigos;
use App\Entity\Login;
use App\Repository\AmigosRepository;
use App\Repository\UsuarioRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Prueba;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AmigoController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('/amigos/list', name: 'amigos')]
    public function listar(AmigosRepository $amigosRepository): JsonResponse
    {
        $listAmigos = $amigosRepository->findAll();

        return $this->json($listAmigos, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);
//        $jsonConverter = new JsonResponseConverter();
//        $listJson = $jsonConverter->toJson($listAmigos);
//        return new JsonResponse($listJson, 200, [], true);
    }

    #[Route('/amigos/save', name: 'amigos_save', methods: ['POST'])]
    public function save(UsuarioRepository $usuarioRepository,Request $request): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $amigoNuevo = new Amigos();

        $id = $json['usuario_id'];
        $amigo = $json['amigo_id'];

        $parametrosBusqueda = array(
            'id' => $id
        );
        $usuario = $usuarioRepository->findOneBy($parametrosBusqueda);
        $amigoid = $usuarioRepository->findOneBy(array("id"=>$amigo));
//        $amigoNuevo->setUsuario_Id($json['usuario_id']);
        $amigoNuevo->setUsuario_Id($usuario);
        $amigoNuevo->setAmigo_Id($amigoid);

        //GUARDAR
        $em = $this-> doctrine->getManager();
        $em->persist($amigoNuevo);
        $em-> flush();

        return new JsonResponse("{ mensaje: Amigo enlazado correctamente }", 200, [], true);


    }

    #[Route('/amigos/buscar', name: 'amigo_buscar_id', methods: ['GET'])]
    public function buscarPorNombre(AmigosRepository $amigosRepository,
                                    Request $request): JsonResponse
    {
        $id = $request->query->get("id");

        $parametrosBusqueda = array(
            'id' => $id
        );

        $listAmigos = $amigosRepository->findBy($parametrosBusqueda);

//        $listJson = $utilidades->toJson($listUsuarios);

        return $this->json($listAmigos, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);

    }

}
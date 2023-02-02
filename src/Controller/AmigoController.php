<?php

namespace App\Controller;

use App\Entity\Amigos;
use App\Entity\Login;
use App\Repository\AmigosRepository;
use App\Repository\UsuarioRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Prueba;
use Exception;
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
// BUSCA POR ID DE LA RELACION EN LA BBDD, CAMBIAR A BUSCAR POR NOMBRE
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
    #[Route('/amigos/delete', name: 'amigos_delete', methods: ['POST'])]
    public function delete(UsuarioRepository $usuarioRepository,Request $request,AmigosRepository $amigosRepository): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $amigoNuevo = new Amigos();

        $id = $json['usuario_id'];
        $amigo = $json['amigo_id'];

        $amigosRepository->borrarAmigo($id,$amigo);

        return new JsonResponse("{ mensaje: Amigo borrado correctamente }", 200, [], true);

    }

    #[Route('/amigos/mis-amigos', name: 'mis-amigos', methods: ['GET'])]
    public function buscarMisAmigos(AmigosRepository $amigosRepository,
                                    Request $request): JsonResponse
    {
        $id = $request->query->get("usuario_id");

        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listAmigos = $amigosRepository->findBy($parametrosBusqueda);

//        $listJson = $utilidades->toJson($listUsuarios);

        return $this->json($listAmigos, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);
    }

    #[Route('/amigos/buscarAmigo', name: 'amigo_buscar_id', methods: ['GET'])]
    public function buscarAmigo(AmigosRepository $amigosRepository,
                                    Request $request, UsuarioRepository $usuarioRepository): JsonResponse
    {

        $json = json_decode($request->getContent(), true);

        $id_usuario = $json['usuario_id'];
        $amigo = $json['usuario_amigo'];

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
                 'amigo_id'=> $amigo_id
        );

        $listAmigo = $amigosRepository->findBy($parametrosBusqueda2, []);


            return $this->json($listAmigo, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ]);




    }


}
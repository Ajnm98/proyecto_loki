<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\AmigosRepository;
use App\Repository\ChatRepository;
use App\Repository\PublicacionRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PublicacionController extends AbstractController
{

    #[Route('/publicacion', name: 'publicacion')]
    public function listarpublicacion(PublicacionRepository $publicacionRepository)//: JsonResponse
    {
        $listPublicacion = $publicacionRepository->findAll();
        return $this->json($listPublicacion, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);

    }

    #[Route('/publicaciones/usuario',  methods: ['GET', 'HEAD'])]
    public function listarPublicacionUsuario(Request $request, PublicacionRepository $publicacionRepository)//: JsonResponse
    {

        $id = $request->query->get("usuario_id");
        $parametrosBusqueda = array(
            'usuario_id' => $id
        );

        $listPublicacion1 = $publicacionRepository->findBy($parametrosBusqueda);
        return $this->json($listPublicacion1);
    }


}
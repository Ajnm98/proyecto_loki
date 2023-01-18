<?php

namespace App\Controller;

use App\Repository\ChatRepository;
use App\Repository\PublicacionRepository;
use App\Utils\ArraySort;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PublicacionController extends AbstractController
{

    #[Route('/publicacion', name: 'publicacion')]
    public function listarpublicacion(PublicacionRepository $publicacionRepository)//: JsonResponse
    {
        $listLogin = $publicacionRepository->findAll();
        return $this->json($listLogin);

    }

    #[Route('/publicaciones/usuario',  methods: ['GET', 'HEAD'])]
    public function listarPublicacionUsuario(Request $request, PublicacionRepository $publicacionRepository)//: JsonResponse
    {

        $id = $request->query->get("id");

        $parametrosBusqueda = array(
            'usuario_id' => $id
        );


        $listChat1 = $publicacionRepository->findBy($parametrosBusqueda);


        return $this->json($listChat1);

    }


}
<?php

namespace App\Controller;

use App\Repository\ChatRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{

    #[Route('/chat', name: 'chat')]
    public function listarchat(ChatRepository $chatRepository)//: JsonResponse
    {
        $listLogin = $chatRepository->findAll();
        return $this->json($listLogin);

    }

    #[Route('/chat/privado',  methods: ['GET', 'HEAD'])]
    public function listarchatUsuario(Request $request, ChatRepository $chatRepository)//: JsonResponse
    {

        $arraySort = new ArraySort();

        $id = $request->query->get("id");

        $parametrosBusqueda = array(
            'usuario_id_emisor' => $id
        );

        $parametrosBusqueda2 = array(
            'usuario_id_receptor' => $id
        );


        $listChat1 = $chatRepository->findBy($parametrosBusqueda);

        $listChat2 = $chatRepository->findBy($parametrosBusqueda2);

        $resultado = array_merge($listChat1, $listChat2);

        $listArraySort = $arraySort->array_sort($resultado, 'fecha', SORT_ASC);

        return $this->json($listArraySort);

    }




}
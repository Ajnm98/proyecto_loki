<?php

namespace App\Controller;

use App\Repository\ChatRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
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

    #[Route('/chat/{id}',  methods: ['GET', 'HEAD'])]
    public function listarchatUsuario(int $id, ChatRepository $chatRepository)//: JsonResponse
    {

        $arraySort = new ArraySort();

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
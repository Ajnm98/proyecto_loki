<?php

namespace App\Controller;

use App\Repository\ChatRepository;
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
        $listChat = $chatRepository->findAll();



        $parametrosBusqueda = array(
            'usuario_id_emisor' => $id,
            'usuario_id_receptor' => $id
        );

        $listChat = $chatRepository->findBy($parametrosBusqueda);

        return $this->json($listChat);

    }


}
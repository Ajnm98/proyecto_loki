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

}
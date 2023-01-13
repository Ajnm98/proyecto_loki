<?php

namespace App\Controller;

use App\Repository\LoginRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MensajeRepository;

class LoginController
{
    #[Route('/login', name: 'login')]
    public function listar(LoginRepository $loginRepository)//: JsonResponse
    {
        $listLogin = $loginRepository->findAll();
        return $this->json($listLogin);

    }

}
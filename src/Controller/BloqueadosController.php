<?php

namespace App\Controller;

use App\Entity\Bloqueados;
use App\Repository\BloqueadosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BloqueadosController extends AbstractController
{

    #[Route('/bloqueados', name: 'bloqueados')]
    public function listarbloqueados(BloqueadosRepository $bloqueadosRepository)//: JsonResponse
    {
        $listLogin = $bloqueadosRepository->findAll();
        return $this->json($listLogin);

    }

}
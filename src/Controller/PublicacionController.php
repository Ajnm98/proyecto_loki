<?php

namespace App\Controller;

use App\Repository\PublicacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PublicacionController extends AbstractController
{

    #[Route('/publicacion', name: 'publicacion')]
    public function listarpublicacion(PublicacionRepository $publicacionRepository)//: JsonResponse
    {
        $listLogin = $publicacionRepository->findAll();
        return $this->json($listLogin);

    }


}
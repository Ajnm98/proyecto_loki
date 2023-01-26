<?php

namespace App\Controller;

use App\Entity\Login;
use App\Repository\LoginRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;



class LoginController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}
    #[Route('/login/list', name: 'login')]
    public function listar(LoginRepository $loginRepository): JsonResponse
    {
        $listLogin = $loginRepository->findAll();

        return $this->json($listLogin, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);

    }
//    #[Route('/login/save', name: 'login_crear', methods: ['POST'])]
//    public function save(Request $request): JsonResponse
//    {
//
//        //Obtener Json del body
//        $json  = json_decode($request->getContent(), true);
//        //CREAR NUEVO USUARIO A PARTIR DEL JSON
//        $loginNuevo = new Login();
//
//        $loginNuevo->setEmail($json['email']);
//        $loginNuevo->setPassword($json['password']);
//        $loginNuevo->setRol($json['rol']);
//
//        //GUARDAR
//        $em = $this-> doctrine->getManager();
//        $em->persist($loginNuevo);
//        $em-> flush();
//
//        return new JsonResponse("{ mensaje: Usuario creado correctamente }", 200, [], true);
//    }



}
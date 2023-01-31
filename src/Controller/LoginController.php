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
    private ManagerRegistry $doctrine;
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }
    #[Route('/login/list', name: 'login')]
    public function listar(LoginRepository $loginRepository): JsonResponse
    {
        $listLogin = $loginRepository->findAll();

        return $this->json($listLogin, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);

    }

    #[Route('/api/login', name: 'app_login', methods: ["POST"])]
    #[OA\Tag(name: 'Login')]
    #[OA\RequestBody(description: "Dto de autentificación", content: new OA\JsonContent(ref: new Model(type: LoginDto::class)))]
    public function login(Request $request, Utils $utils): JsonResponse
    {

        //CARGAR REPOSITORIOS
        $em = $this-> doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $apikeyRepository = $em->getRepository(ApiKey::class);



        //Cargar datos del cuerpo
        $json_body = json_decode($request->getContent(), true);

        //Datos Usuario
        $username = $json_body["username"];
        $password = $json_body["password"];

        //Validar que los credenciales son correcto
        if($username != null and $password !=null){

            $user = $userRepository->findOneBy(array("username"=> $username));


            if($user != null){
                $verify = $utils-> verify($password, $user->getPassword());
                if($verify){

                    $token = $apikeyRepository-> findApiKeyValida($user);

                    if($token != null){
                        return $this->json([
                            'token' => $token->getToken()
                        ]);
                    }else{
                        $tokenNuevo = $utils->generateApiToken($user, $apikeyRepository);
                        return $this->json([
                            'token' => $tokenNuevo
                        ]);
                    }
                }else{
                    return $this->json([
                        'message' => "Contraseña no válida" ,
                    ]);
                }

            }
            return $this->json([
                'message' => "Usuario no válido" ,
            ]);
        }else{
            return $this->json([
                'message' => "No ha indicado usuario y contraseña" ,
            ]);
        }
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
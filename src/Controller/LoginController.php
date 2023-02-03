<?php

namespace App\Controller;

use App\Dto\ApiKeyDTO;
use App\Dto\CrearLoginDTO;
use App\Dto\DtoConverters;
use App\Dto\LoginDTO;
use App\Entity\ApiKey;
use App\Entity\Login;
use App\Entity\Usuario;
use App\Repository\LoginRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Utilidades;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use OpenApi\Attributes as OA;

class LoginController extends AbstractController
{
    private ManagerRegistry $doctrine;
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }
    #[Route('/api/login/list', name: 'login', methods: ['GET'])]
    #[OA\Tag(name: 'Login')]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: LoginDTO::class))))]
    public function listar(LoginRepository $loginRepository,
                           DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        $listLogin = $loginRepository->findAll();

        foreach($listLogin as $user){
            $usuarioDto = $converters-> loginToDto($user);
            $json = $jsonResponseConverter->toJson($usuarioDto,null);
            $listJson[] = json_decode($json);
        }

        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);
    }

    #[Route('/api/login', name: 'app_login', methods: ["POST"])]
    #[OA\Tag(name: 'Login')]
    #[OA\RequestBody(description: "Dto de autentificación", content: new OA\JsonContent(ref: new Model(type: CrearLoginDTO::class)))]
    #[OA\Response(response:100,description:"successful operation" ,content: new OA\JsonContent(type: "string"))]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "string"))]
    #[OA\Response(response: 300,description: "Password no valida")]
    #[OA\Response(response: 400,description: "Usuario no valido")]
    #[OA\Response(response: 500,description: "No ha indicado usuario y password")]

    public function login(Request $request, Utilidades $utils): JsonResponse
    {
        //CARGAR REPOSITORIOS
        $em = $this-> doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $apikeyRepository = $em->getRepository(ApiKey::class);

        //Cargar datos del cuerpo
        $json_body = json_decode($request->getContent(), true);

        //Datos Usuario
        $username = $json_body["usuario"];
        $password = $json_body["password"];

        //Validar que los credenciales son correcto
        if($username != null and $password !=null){

            $user = $userRepository->findOneBy(array("usuario"=> $username));


            if($user != null){
                $verify = $utils-> verify($password, $user->getLogin()->getPassword());
                if($verify){

                    $token = $apikeyRepository-> findApiKeyValida($user->getId());

                    if(!empty($token)){
                        return $this->json([
                            'token' => $token->getToken(), 100
                        ]);
                    }else{
                        $tokenNuevo = $utils->generateApiToken($user, $apikeyRepository);
                        return $this->json([
                            'token' => $tokenNuevo, 200
                        ]);
                    }
                }else{
                    return $this->json([
                        'message' => "Password no valida" , 300
                    ]);
                }

            }
            return $this->json([
                'message' => "Usuario no valido" , 400
            ]);
        }else{
            return $this->json([
                'message' => "No ha indicado usuario y password" , 500
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
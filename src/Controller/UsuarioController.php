<?php

namespace App\Controller;

use App\Entity\ApiKey;
use App\Entity\Login;
use App\Entity\Usuario;
use App\Repository\AmigosRepository;
use App\Repository\BloqueadosRepository;
use App\Repository\ChatRepository;
use App\Repository\LoginRepository;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\UsuarioRepository;
use App\Utils\Utilidades;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class UsuarioController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }
    #[Route('/usuario/list', name: 'usuarioListar', methods: ['GET'])]
public function listar(UsuarioRepository $usuarioRepository): JsonResponse
{

    $listLogin = $usuarioRepository->findAll();

    return $this->json($listLogin, 200, [], [
        AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
    ]);
}
    #[Route('/usuario/buscar', name: 'app_usuario_buscar_nombre', methods: ['GET'])]
    public function buscarPorNombre(UsuarioRepository $usuarioRepository,
                                    Request $request): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        $nick = $json['nombre'];
        $a = "%";
        $final= $a.$nick.$a;

        $usuario = $usuarioRepository->buscarNombre($final);

        return $this->json($usuario, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);

    }

    #[Route('/usuario/delete', name: 'respuesta_delete', methods: ['POST'])]
    public function delete(Request $request,ChatRepository $chatRepository,
                           PublicacionRepository $publicacionRepository,RespuestaRepository $respuestaRepository,
                           LoginRepository $loginRepository,UsuarioRepository $usuarioRepository,
                            AmigosRepository $amigosRepository,BloqueadosRepository $bloqueadosRepository): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);

        $id = $json['id'];
        $bloqueadosRepository->borrarBloqueadosPorUsuario($id);
        $amigosRepository->borrarAmigosPorUsuario($id);
        $chatRepository->borrarChatPorUsuario($id);
        $respuestaRepository->borrarRespuestaPorUsuario($id);
        $publicacionRepository->borrarPublicacionPorUsuario($id);
        $usuarioRepository->borrarUsuario($id);
        $loginRepository->borrarLogin($id);


        return new JsonResponse("{ mensaje: Usuario borrado correctamente }", 200, [], true);
    }
    #[Route('/usuario/registrar', name: 'usuario_save_corto', methods: ['POST'])]
    public function save(LoginRepository $loginRepository,Utilidades $utilidades,
                         UsuarioRepository $usuarioRepository,Request $request): JsonResponse
    {

        //Obtener Json del body
        $json  = json_decode($request->getContent(), true);
        //CREAR NUEVO USUARIO A PARTIR DEL JSON
        $usuarioNuevo = new Usuario();
        $loginNuevo = new Login();

        $usuario = $json['usuario'];
        $email = $json['email'];
        $password = $utilidades->hashPassword($json['password']);


        //primero guardamos el login

        $loginNuevo->setEmail($email);
        $loginNuevo->setPassword($password);
        $loginNuevo->setRol(1);

        //GUARDAR
        $em = $this-> doctrine->getManager();
        $em->persist($loginNuevo);
        $em-> flush();

        //buscamos el login y obtenemos el id para incorporarlo al usuario

        $login = $loginRepository->findOneBy(array("email"=>$email));

        //guardamos el usuario
        $usuarioNuevo->setUsuario($usuario);
        $usuarioNuevo->setLogin($login);

        //GUARDAR
        $em = $this-> doctrine->getManager();
        $em->persist($usuarioNuevo);
        $em-> flush();

        return new JsonResponse("{ mensaje: usuario creado correctamente }", 200, [], true);
    }
    #[Route('/usuario/buscarNick', name: 'app_usuario_buscar_nick', methods: ['GET'])]
    public function buscarPorNick(UsuarioRepository $usuarioRepository,
                                  Request $request): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        $nick = $json['nick'];
        $a = "%";
        $final= $a.$nick.$a;

        $usuario = $usuarioRepository->buscarNick($final);


        return $this->json($usuario, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);
    }

    #[Route('/usuario/mi-usuario', name: 'app_mi_usuario', methods: ['GET'])]
    public function miUsuario(UsuarioRepository $usuarioRepository,
                                  Request $request,Utilidades $utils): JsonResponse
    {
        $em = $this-> doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $apikeyRepository = $em->getRepository(ApiKey::class);

        $apikey = $request->query->get("api_key");
        $compruebaAcceso = $utils->esApiKeyValida($apikey, 1, $apikeyRepository, $userRepository);


        if ($compruebaAcceso) {

            $usuario = $usuarioRepository->findAll();

            return $this->json($usuario, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
            ]);

        } else {
            return $this->json([
                'message' => "No tiene permiso",
            ]);
        }



    }


}
<?php

namespace App\Controller;

use App\Dto\BorrarUsuarioDTO;
use App\Dto\CrearUsuarioDTO;
use App\Dto\DtoConverters;
use App\Dto\UsuarioDTO;
use App\Entity\ApiKey;
use App\Entity\Login;
use App\Entity\Usuario;
use App\Repository\AmigosRepository;
use App\Repository\ApiKeyRepository;
use App\Repository\BloqueadosRepository;
use App\Repository\ChatRepository;
use App\Repository\LoginRepository;
use App\Repository\PublicacionRepository;
use App\Repository\RespuestaRepository;
use App\Repository\UsuarioRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Utilidades;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Security;
use ReallySimpleJWT\Token;
use Nelmio\ApiDocBundle\Annotation\Model;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use OpenApi\Attributes as OA;

class UsuarioController extends AbstractController
{

    private ManagerRegistry $doctrine;
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('/api/usuario/list', name: 'usuarioListar', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]
    #[OA\Response(response: 401,description: "Unauthorized")]
    public function listar(UsuarioRepository $usuarioRepository,Utilidades $utils, Request $request,
                           DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
{
    if($utils->comprobarPermisos($request, 0)) {
        $listLogin = $usuarioRepository->findAll();

        foreach ($listLogin as $user) {
            $usuarioDto = $converters->usuarioToDto($user);
            $json = $jsonResponseConverter->toJson($usuarioDto, null);
            $listJson[] = json_decode($json);
        }

        return $this->json($listJson, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                return $obj->getId();
            },
        ]);
    }else{return new JsonResponse("{ message: Unauthorized}", 401,[],false);}
}


    #[Route('/api/usuario/buscar', name: 'appUsuarioBuscarNombre', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
    #[OA\Parameter(name: "nombre", description: "Nombre Usuario", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]

    public function buscarPorNombre(UsuarioRepository $usuarioRepository,
                                    Request $request): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        $nick = $request->query->get("nombre");
        $a = "%";
        $final= $a.$nick.$a;

        $usuario = $usuarioRepository->buscarNombre($final);

        return $this->json($usuario, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);

    }

    #[Route('/api/usuario/delete', name: 'respuestaDelete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Usuario')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto de la respuesta", required: true, content: new OA\JsonContent(ref: new Model(type:BorrarUsuarioDTO::class)))]
    #[OA\Response(response: 200,description: "Usuario borrado correctamente")]
    #[OA\Response(response: 300,description: "No se pudo borrar correctamente")]
    #[OA\Response(response: 400,description: "No puedes borrar a otro usuario")]
    public function delete(Request $request,ChatRepository $chatRepository,Utilidades $utils,
                           PublicacionRepository $publicacionRepository,RespuestaRepository $respuestaRepository,
                           LoginRepository $loginRepository,UsuarioRepository $usuarioRepository,
                            AmigosRepository $amigosRepository,BloqueadosRepository $bloqueadosRepository,
                            ApiKeyRepository $apiKeyRepository): JsonResponse
    {

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);
        $id = $json['id'];
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];

        if ($utils->comprobarPermisos($request, 0)) {
            $bloqueadosRepository->borrarBloqueadosPorUsuario($id);
            $amigosRepository->borrarAmigosPorUsuario($id);
            $chatRepository->borrarChatPorUsuario($id);
            $respuestaRepository->borrarRespuestaPorUsuario($id);
            $publicacionRepository->borrarPublicacionPorUsuario($id);
            $apiKeyRepository->borrarApiKeyUsuario($id);
            $usuarioRepository->borrarUsuario($id);
            $loginRepository->borrarLogin($id);


            return new JsonResponse("{ mensaje: Usuario borrado correctamente }", 200, [], true);
        }
        elseif($utils->comprobarPermisos($request, 1)) {

//            if ($id != $idu) {
//                return new JsonResponse("{ mensaje: No puedes borrar a otro usuario}", 400, [], true);
//            } else {
                $bloqueadosRepository->borrarBloqueadosPorUsuario($idu);
                $amigosRepository->borrarAmigosPorUsuario($idu);
                $chatRepository->borrarChatPorUsuario($idu);
                $respuestaRepository->borrarRespuestaPorUsuario($idu);
                $publicacionRepository->borrarPublicacionPorUsuario($idu);
                $apiKeyRepository->borrarApiKeyUsuario($idu);
                $usuarioRepository->borrarUsuario($idu);
                $loginRepository->borrarLogin($idu);

                return new JsonResponse("{ mensaje: Usuario borrado correctamente }", 200, [], true);
            }
//        }
        else{
            return new JsonResponse("{ mensaje: No se pudo borrar correctamente }", 300, [], true);
        }
    }

    #[Route('/api/usuario/registrar', name: 'usuarioSaveCorto', methods: ['POST'])]
    #[OA\Tag(name: 'Usuario')]
    #[OA\RequestBody(description: "Dto de la respuesta", required: true, content: new OA\JsonContent(ref: new Model(type:CrearUsuarioDTO::class)))]
    #[OA\Response(response: 200,description: "Usuario creado correctamente")]
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
    #[Route('/api/usuario/buscarNick', name: 'appUsuarioBuscarNick', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
    #[OA\Parameter(name: "nick", description: "Nick Usuario", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]
    public function buscarPorNick(UsuarioRepository $usuarioRepository,
                                  Request $request,DtoConverters $converters, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        $nick = $request->query->get("nick");
        $a = "%";
        $final= $a.$nick.$a;

        $usuario = $usuarioRepository->buscarNick($final);


        return $this->json($usuario, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);
    }

    #[Route('/api/usuario/mi-usuario', name: 'app_mi_usuario', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]
    public function miUsuario(UsuarioRepository $usuarioRepository,
                                  Request $request,Utilidades $utils): JsonResponse
    {

//        if ($utils->comprobarPermisos($request,1)) {
            $apikey = $request->headers->get("apikey");
            $id_usuario = Token::getPayload($apikey)["user_id"];
            $usuario = $usuarioRepository->findOneBy(array("id"=>$id_usuario));

            return $this->json($usuario, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
            ]);

//        } else {
//            return $this->json([
//                'message' => "No tiene permiso",
//            ]);
//        }

    }

    #[Route('/api/usuario/buscarid', name: 'appUsuarioBuscarid', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
    #[OA\Parameter(name: "nombre", description: "Nombre Usuario", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]

    public function buscarPorId(UsuarioRepository $usuarioRepository,
                                    Request $request): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        $id = $request->query->get("id");


        $usuario = $usuarioRepository->findOneBy(array("id"=>$id));

        return $this->json($usuario, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);

    }



}
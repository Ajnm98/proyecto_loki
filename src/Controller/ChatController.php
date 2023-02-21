<?php

namespace App\Controller;

use App\Dto\BorrarChatDTO;
use App\Dto\ChatDTO;
use App\Dto\CrearChatDTO;
use App\Dto\DtoConverters;
use App\Dto\UsuarioDTO;
use App\Entity\Chat;
use App\Entity\Publicacion;
use App\Repository\ChatRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
use App\Utils\Utilidades;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ChatController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine) {}
    #[Route('/api/chat', name: 'chat',methods: ['GET'])]
    #[OA\Tag(name:'Chat')]
    #[Security(name: "apikey")]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: ChatDTO::class))))]
    #[OA\Response(response: 401,description: "Unauthorized")]
    public function listarchat(ChatRepository $chatRepository,Utilidades $utils, Request $request,
                               DtoConverters $converters, JsonResponseConverter $jsonResponseConverter)//: JsonResponse
    {

        if($utils->comprobarPermisos($request, 0)) {
            $listChat = $chatRepository->findAll();

            foreach ($listChat as $user) {
                $usuarioDto = $converters->chatToDto($user);
                $json = $jsonResponseConverter->toJson($usuarioDto, null);
                $listJson[] = json_decode($json);
            }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);
        }
        else{return new JsonResponse("{ message: Unauthorized}", 401,[],false);}


    }

    #[Route('/api/chat/privado',  methods: ['GET'])]
    #[OA\Tag(name:'Chat')]
    #[Security(name: "apikey")]
    #[OA\Parameter(name: "id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: ChatDTO::class))))]
    #[OA\Response(response: 400,description: "No se pudo listar")]
    public function listarchatUsuario(Request $request, ChatRepository $chatRepository, Utilidades $utils,
          DtoConverters $converters, JsonResponseConverter $jsonResponseConverter)//: JsonResponse
    {

        $arraySort = new ArraySort();
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];
        $id = $request->query->get("id");


        if($utils->comprobarPermisos($request, 0)) {
            $parametrosBusqueda = array(
                'usuario_id_emisor' => $id
            );

            $parametrosBusqueda2 = array(
                'usuario_id_receptor' => $id
            );


            $listChat1 = $chatRepository->findBy($parametrosBusqueda);

            $listChat2 = $chatRepository->findBy($parametrosBusqueda2);

            $resultado = array_merge($listChat1, $listChat2);

            $listArraySort = $arraySort->array_sort($resultado, 'fecha', SORT_ASC);

            foreach ($listArraySort as $user) {
                $usuarioDto = $converters->chatToDto($user);
                $json = $jsonResponseConverter->toJson($usuarioDto, null);
                $listJson[] = json_decode($json);
            }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);
        }
        elseif($utils->comprobarPermisos($request, 1)){

            $parametrosBusqueda = array(
                'usuario_id_emisor' => $idu
            );

            $parametrosBusqueda2 = array(
                'usuario_id_receptor' => $idu
            );


            $listChat1 = $chatRepository->findBy($parametrosBusqueda);

            $listChat2 = $chatRepository->findBy($parametrosBusqueda2);

            $resultado = array_merge($listChat1, $listChat2);

            $listArraySort = $arraySort->array_sort($resultado, 'fecha', SORT_ASC);

            foreach ($listArraySort as $user) {
                $usuarioDto = $converters->chatToDto($user);
                $json = $jsonResponseConverter->toJson($usuarioDto, null);
                $listJson[] = json_decode($json);
            }

            return $this->json($listJson, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);
        }

        else{
            return new JsonResponse("{ message: No se pudo listar}", 400,[],false);
        }
    }

    #[Route('/api/chat/listachatsUsuario', name: 'chats_usuario',  methods: ['GET'])]
    #[OA\Tag(name:'Chat')]
    #[Security(name: "apikey")]
    #[OA\Parameter(name: "usuario_id", description: "Tu id de usuario", in: "query", required: true, schema: new OA\Schema(type: "integer") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Model(type: UsuarioDTO::class))))]
    #[OA\Response(response: 400,description: "No se pudo listar")]
    public function listarchatsAbiertosUsuario(Request $request, ChatRepository $chatRepository, Utilidades $utils): JsonResponse
    {

        $id = $request->query->get("usuario_id");
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];
        $array = array();


        if($utils->comprobarPermisos($request, 0)) {
            $parametrosBusqueda = array(
                'usuario_id_emisor' => $id
            );

            $parametrosBusqueda2 = array(
                'usuario_id_receptor' => $id
            );

            $listChats1 = $chatRepository->findBy($parametrosBusqueda, []);
            $listChats2 = $chatRepository->findBy($parametrosBusqueda2, []);

            $listChats = array_merge($listChats1, $listChats2);

            foreach ($listChats as $chat) {
                $parametrosBusqueda3 = array(
                    'id' => $id
                );

                if ($chat->getUsuarioIdEmisor()->getId() != $parametrosBusqueda3) {
                    $chat1 = $chat->getUsuarioIdEmisor();
                    array_push($array, $chat1);
                } else {
                    $chat2 = $chat->getUsuarioIdReceptor();
                    array_push($array, $chat2);
                }

            }

            for ($i = 0; $i < count($array); ++$i) {
                if ($id = $array[$i]->getId()) {
                    unset($array[$i]);
                }
            }


            return $this->json($array, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }

            ]);
        }
        elseif($utils->comprobarPermisos($request, 1)){
            $parametrosBusqueda = array(
                'usuario_id_emisor' => $idu
            );

            $parametrosBusqueda2 = array(
                'usuario_id_receptor' => $idu
            );

            $listChats1 = $chatRepository->findBy($parametrosBusqueda, []);
            $listChats2 = $chatRepository->findBy($parametrosBusqueda2, []);

            $listChats = array_merge($listChats1, $listChats2);

            foreach ($listChats as $chat) {
                $parametrosBusqueda3 = array(
                    'id' => $idu
                );

                if ($chat->getUsuarioIdEmisor()->getId() != $parametrosBusqueda3) {
                    $chat1 = $chat->getUsuarioIdEmisor();
                    array_push($array, $chat1);
                } else {
                    $chat2 = $chat->getUsuarioIdReceptor();
                    array_push($array, $chat2);
                }

            }

            for ($i = 0; $i < count($array); ++$i) {
                if ($id = $array[$i]->getId()) {
                    unset($array[$i]);
                }
            }


            return $this->json($array, 200, [], [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }

            ]);
        }
        else{
            return new JsonResponse("{ message: No se pudo listar}", 400,[],false);
        }
    }


    #[Route('/api/chat/enviarMensaje', name: 'chat_usuario',  methods: ['POST'])]
    #[OA\Tag(name:'Chat')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type:CrearChatDTO::class)))]
    #[OA\Response(response: 200,description: "Mensaje enviado correctamente")]
    #[OA\Response(response: 300,description: "No se pudo enviar el mensaje correctamente")]
    #[OA\Response(response: 400,description: "No puedes enviar mensaje de otro usuario")]
    public function enviarMensaje(Request $request, ChatRepository $chatRepository,Utilidades $utils,
                                  UsuarioRepository $usuarioRepository): JsonResponse
    {

        $json  = json_decode($request->getContent(), true);
        $apikey = $request->headers->get('apikey');
        $idu = Token::getPayload($apikey)["user_id"];;
        $id_emisor = $json['usuarioIdEmisor'];
        $id_receptor = $json['usuarioIdReceptor'];
        $texto = $json['texto'];
        $fecha = date('d-m-Y H:i:s');
        $foto = $json['foto'];

        if($utils->comprobarPermisos($request, 0)) {
            $chat = new Chat();

            $parametrosBusqueda1 = array(
                'id' => $id_emisor
            );

            $parametrosBusqueda2 = array(
                'id' => $id_receptor
            );

            $usuarioemisor = $usuarioRepository->findOneBy($parametrosBusqueda1);
            $usuarioreceptor = $usuarioRepository->findOneBy($parametrosBusqueda2);


            $chat->setUsuarioIdEmisor($usuarioemisor);
            $chat->setUsuarioIdReceptor($usuarioreceptor);
            $chat->setTexto($texto);
            $chat->setFecha($fecha);
            //setFechaNacimiento((date_create_from_format('Y/d/m H:i:s',$json['fecha_nacimiento'])));
            $chat->setFoto($foto);

            $em = $this->doctrine->getManager();
            $em->persist($chat);
            $em->flush();
//        $chatRepository->enviarMensaje($id_emisor, $id_receptor, $texto, $fecha, $foto);
            return new JsonResponse(" Mensaje enviado correctamente ", 200, []);
        }
        elseif($utils->comprobarPermisos($request, 1)){
            if($idu!=$id_emisor){
                return new JsonResponse("{ mensaje: No puedes enviar mensaje de otro usuario}", 400, [], true);
            }
            else {
                $chat = new Chat();

                $parametrosBusqueda1 = array(
                    'id' => $id_emisor
                );

                $parametrosBusqueda2 = array(
                    'id' => $id_receptor
                );

                $usuarioemisor = $usuarioRepository->findOneBy($parametrosBusqueda1);
                $usuarioreceptor = $usuarioRepository->findOneBy($parametrosBusqueda2);


                $chat->setUsuarioIdEmisor($usuarioemisor);
                $chat->setUsuarioIdReceptor($usuarioreceptor);
                $chat->setTexto($texto);
                $chat->setFecha($fecha);
                //setFechaNacimiento((date_create_from_format('Y/d/m H:i:s',$json['fecha_nacimiento'])));
                $chat->setFoto($foto);

                $em = $this->doctrine->getManager();
                $em->persist($chat);
                $em->flush();
//        $chatRepository->enviarMensaje($id_emisor, $id_receptor, $texto, $fecha, $foto);
                return new JsonResponse(" Mensaje enviado correctamente ", 200, []);
            }
        }  else{
            return new JsonResponse("{ mensaje: No se pudo enviar el mensaje correctamente }", 300, [], true);
        }

    }

    #[Route('/api/chat/borrarChat', name: 'borrarChat_usuario',  methods: ['DELETE'])]
    #[OA\Tag(name:'Chat')]
    #[Security(name: "apikey")]
    #[OA\RequestBody(description: "Dto del chat", required: true, content: new OA\JsonContent(ref: new Model(type:BorrarChatDTO::class)))]
    #[OA\Response(response: 200,description: "Chat borrado correctamente")]
    #[OA\Response(response: 100,description: "No se ha podido borrar correctamente")]
    #[OA\Response(response: 300,description: "No se pudo borrar correctamente")]
    #[OA\Response(response: 400,description: "No puedes borrar chats de otro usuario")]
    public function BorrarChat(Request $request, ChatRepository $chatRepository, UsuarioRepository $usuarioRepository,
                               Utilidades $utils): JsonResponse
    {

        $json = json_decode($request->getContent(), true);
        $apikey = $request->headers->get('apikey');
        $id_emisor = $json['usuarioIdEmisor'];
        $id_receptor = $json['usuarioIdReceptor'];
        $idu = Token::getPayload($apikey)["user_id"];

        if ($utils->comprobarPermisos($request, 0)) {
            $array = array();

            $parametrosBusqueda = array(
                'usuario_id_emisor' => $id_emisor,
                'usuario_id_receptor' => $id_receptor
            );

            $parametrosBusqueda2 = array(
                'usuario_id_receptor' => $id_emisor,
                'usuario_id_emisor' => $id_receptor
            );

            $listChats1 = $chatRepository->findBy($parametrosBusqueda, []);
            $listChats2 = $chatRepository->findBy($parametrosBusqueda2, []);

            $listChats = array_merge($listChats1, $listChats2);

            foreach ($listChats as $chat) {

                $em = $this->doctrine->getManager();
                $em->remove($chat);
                $em->flush();
            }

            $listChats1 = $chatRepository->findBy($parametrosBusqueda, []);
            $listChats2 = $chatRepository->findBy($parametrosBusqueda2, []);

            $listChats = array_merge($listChats1, $listChats2);

            if ($listChats != null) {
                return new JsonResponse("{ mensaje: No se ha podido borrar correctamente }", 100, [], true);
            } else {
                return new JsonResponse("{ mensaje: Chat borrado correctamente }", 200, [], true);
            }
        } elseif ($utils->comprobarPermisos($request, 1)) {
            {
                if ($id_emisor != $idu) {
                    return new JsonResponse("{ mensaje: No puedes borrar chats de otro usuario}", 400, [], true);
                } else {
                    $array = array();

                    $parametrosBusqueda = array(
                        'usuario_id_emisor' => $id_emisor,
                        'usuario_id_receptor' => $id_receptor
                    );

                    $parametrosBusqueda2 = array(
                        'usuario_id_receptor' => $id_emisor,
                        'usuario_id_emisor' => $id_receptor
                    );

                    $listChats1 = $chatRepository->findBy($parametrosBusqueda, []);
                    $listChats2 = $chatRepository->findBy($parametrosBusqueda2, []);

                    $listChats = array_merge($listChats1, $listChats2);

                    foreach ($listChats as $chat) {

                        $em = $this->doctrine->getManager();
                        $em->remove($chat);
                        $em->flush();
                    }

                    $listChats1 = $chatRepository->findBy($parametrosBusqueda, []);
                    $listChats2 = $chatRepository->findBy($parametrosBusqueda2, []);

                    $listChats = array_merge($listChats1, $listChats2);

                    if ($listChats != null) {
                        return new JsonResponse("{ mensaje: No se ha podido borrar correctamente }", 100, [], true);
                    } else {
                        return new JsonResponse("{ mensaje: Chat borrado correctamente }", 200, [], true);
                    }
                }

            }

        } else {
            return new JsonResponse("{ mensaje: No se pudo borrar correctamente }", 300, [], true);
        }


    }
}
<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Publicacion;
use App\Repository\ChatRepository;
use App\Repository\UsuarioRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ChatController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine) {}
    #[Route('/chat', name: 'chat')]
    public function listarchat(ChatRepository $chatRepository)//: JsonResponse
    {
        $listChat = $chatRepository->findAll();
        return $this->json($listChat, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);
    }

    #[Route('/chat/privado',  methods: ['GET', 'HEAD'])]
    public function listarchatUsuario(Request $request, ChatRepository $chatRepository)//: JsonResponse
    {

        $arraySort = new ArraySort();

        $id = $request->query->get("id");

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

        return $this->json($listArraySort);

    }

    #[Route('/chat/listachatsUsuario', name: 'chat_usuario',  methods: ['GET', 'HEAD'])]
    public function listarchatsAbiertosUsuario(Request $request, ChatRepository $chatRepository): JsonResponse
    {

        $id = $request->query->get("usuario_id");


        $array = array();

        $parametrosBusqueda = array(
            'usuario_id_emisor' => $id
        );

        $parametrosBusqueda2 = array(
            'usuario_id_receptor' => $id
        );

        $listChats1 = $chatRepository->findBy($parametrosBusqueda, []);
        $listChats2 = $chatRepository->findBy($parametrosBusqueda2, []);

        $listChats = array_merge($listChats1, $listChats2);

        foreach ($listChats as $chat){
            $parametrosBusqueda3 = array(
                'id' => $id
            );

            if($chat->getUsuarioIdEmisor()->getId()!=$parametrosBusqueda3){
                $chat1 = $chat->getUsuarioIdEmisor();
                array_push($array, $chat1);
            }
            else{
                $chat2 = $chat->getUsuarioIdReceptor();
                array_push($array, $chat2);
            }

        }

        for($i = 0; $i < count($array); ++$i) {
            if ($id = $array[$i]->getId()) {
                unset($array[$i]);
            }
        }


        return $this->json($array, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);
    }


    #[Route('/chat/enviarMensaje', name: 'chat_usuario',  methods: ['POST'])]
    public function enviarMensaje(Request $request, ChatRepository $chatRepository, UsuarioRepository $usuarioRepository): JsonResponse
    {

        $json  = json_decode($request->getContent(), true);

        $id_emisor = $json['usuario_id_emisor'];
        $id_receptor = $json['usuario_id_receptor'];
        $texto = $json['texto'];
        $fecha = date('d-m-Y H:i:s');
        $foto = $json['foto'];

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
        return new JsonResponse("{ mensaje: Mensaje enviado }", 200, [], true);

    }

    #[Route('/chat/borrarChat', name: 'borrarChat_usuario',  methods: ['POST'])]
    public function BorrarChat(Request $request, ChatRepository $chatRepository, UsuarioRepository $usuarioRepository): JsonResponse
    {

        $json = json_decode($request->getContent(), true);
        $id_emisor = $json['usuario_id_emisor'];
        $id_receptor = $json['usuario_id_receptor'];


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

        foreach ($listChats as $chat){

            $em = $this->doctrine->getManager();
            $em->remove($chat);
            $em->flush();
        }

        $listChats1 = $chatRepository->findBy($parametrosBusqueda, []);
        $listChats2 = $chatRepository->findBy($parametrosBusqueda2, []);

        $listChats = array_merge($listChats1, $listChats2);

        if($listChats!=null){
            return new JsonResponse("{ mensaje: No se ha podido borrar }", 200, [], true);;
        }
        else{
            return new JsonResponse("{ mensaje: Usuarios borrados correctamente }", 200, [], true);
        }

    }



}
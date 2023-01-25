<?php

namespace App\Controller;

use App\Repository\ChatRepository;
use App\Utils\ArraySort;
use App\Utils\JsonResponseConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ChatController extends AbstractController
{

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




}
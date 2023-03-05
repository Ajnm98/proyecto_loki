<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use App\Utils\JsonResponseConverter;
use App\Utils\Utilidades;
use Doctrine\ORM\Mapping\Entity;
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




class TagsController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }
    #[Route('/api/tags/listar',  methods: ['GET'])]
    #[OA\Tag(name: 'Tags')]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Entity( Tags::class))))]
    public function listarTagsPopulares(Request $request,TagsRepository $tagsRepository, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {
        $listaTags = $tagsRepository->findAll();

        return $this->json($listaTags, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__','usuarioId'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},
        ]);
    }
    #[Route('/api/tags/publicaciones-nombre-tag',name: 'portagbuscar',  methods: ['GET'])]
    #[OA\Tag(name: 'Tags')]
    #[OA\Parameter(name: "nombre", description: "Nombre del Tag", in: "query", required: true, schema: new OA\Schema(type: "string") )]
    #[OA\Response(response:200,description:"successful operation" ,content: new OA\JsonContent(type: "array", items: new OA\Items(ref:new Entity( Tags::class))))]
    #[OA\Response(response: 300,description: "No existe ese tag")]
    public function listarPublicacionesPorTag(Request $request,TagsRepository $tagsRepository, JsonResponseConverter $jsonResponseConverter): JsonResponse
    {

        $id = $request->query->get("nombre");

        $parametrosBusqueda = array(
            'nombre' => $id
        );
        $listaTags = $tagsRepository->findBy($parametrosBusqueda);
        if(count($listaTags)==0){

            return new JsonResponse("No existe ese tag",300,[],true);
        }
        return $this->json($listaTags, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__','usuarioId'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER=>function ($obj){return $obj->getId();},

        ]);

    }

}
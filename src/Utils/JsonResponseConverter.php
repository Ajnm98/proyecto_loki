<?php

namespace App\Utils;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonResponseConverter
{
    public function toJson($data): string
    {
        //Inicialización de serializador
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        //Conversion a JSON
        $json = $serializer->serialize($data, 'json');

        return $json;
    }

}
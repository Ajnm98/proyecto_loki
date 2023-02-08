<?php

namespace App\Utils;

use App\Entity\ApiKey;
use App\Entity\Usuario;
use App\Repository\ApiKeyRepository;
use App\Repository\UsuarioRepository;
use DateTime;
use ReallySimpleJWT\Token;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;


class Utilidades
{
    public function  hashPassword($password):string
    {

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');

        return $passwordHasher->hash($password);

    }

    public function  verify($passwordPlain, $passwordBD):bool
    {
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');

        return $passwordHasher->verify($passwordBD,$passwordPlain);

    }
    public function  generateApiToken(Usuario $user, ApiKeyRepository $apiKeyRepository):string
    {
        //BORRADO SI TIENE UN TOKEN NO VALIDO
        $token_invalido = $apiKeyRepository->findOneBy(array("usuario"=>$user->getId()));
        if($token_invalido!=null){
            $apiKeyRepository->remove($token_invalido);
        }

        //GENERO UN OBJETO CON API KEY NUEVO
        $apiKey = new ApiKey();
        $apiKey->setUsuario($user);
        $fechaActual5hour = date("Y-m-d H:i:s", strtotime('+5 hours'));
        $fechaExpiracion = DateTime::createFromFormat('Y-m-d H:i:s', $fechaActual5hour);
        $apiKey->setFechaExpiracion($fechaExpiracion);

        $tokenData = [
            'user_id' => $user->getId(),
            'username' => $user->getUsuario(),
            'user_rol' => $user->getLogin()->getRol(),
            'fecha_expiracion' => $fechaExpiracion,
        ];

        $secret = $user->getLogin()->getPassword();

        $token = Token::customPayload($tokenData, $secret);

        $apiKey->setToken($token);

        $apiKeyRepository->save($apiKey,true);


        return $token;
    }

    public function esApiKeyValida($token, $permisoRequerido, ApiKeyRepository $apiKeyRepository,UsuarioRepository $usuarioRepository):bool
    {
        $apiKey = $apiKeyRepository->findOneBy(array("token" => $token));
        $fechaActual = DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
        $id_usuario = Token::getPayload($token)["user_id"];
        $rol_name= Token::getPayload($token)["user_rol"];
        $usuario= $usuarioRepository->findOneBy(array("id" => $id_usuario));

        return $apiKey == null
            or $permisoRequerido == $rol_name
            or $apiKey->getUsuario()->getId() == $id_usuario
            or $apiKey->getFechaExpiracion() <= $fechaActual
            or Token::validate($token, $usuario->getPassword());
    }
}
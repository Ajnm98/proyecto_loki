<?php

namespace App\Dto;

use App\Entity\Amigos;
use App\Entity\Login;
use App\Entity\Usuario;

class DtoConverters
{
    public function AmigosToDto(Amigos $amigos):AmigosDTO
    {
        $amigosDto = new AmigosDTO();
        $amigosDto->setId($amigos->getId());
        $amigosDto->setUsuarioId($this->UsuarioToDto($amigos->getUsuarioId()));
        $amigosDto->setAmigoId($this->UsuarioToDto($amigos->getAmigoId()));

        return $amigosDto;

    }

    public function UsuarioToDto(Usuario $usuario):UsuarioDTO
    {

        $usuarioDto = new UsuarioDTO();
        $usuarioDto->setId($usuario->getId());
        $usuarioDto->setUsuario($usuario->getUsuario());
        $usuarioDto->setNombre($usuario->getNombre());
        $usuarioDto->setNick($usuario->getNick());
        $usuarioDto->setLogin($this->LoginToDto($usuario->getLogin()));
        $usuarioDto->setTelefono($usuario->getTelefono());

        return $usuarioDto;

    }

    public function LoginToDto(Login $login):LoginDTO
    {
        $loginDto = new LoginDTO();
        $loginDto->setId($login->getId());
        $loginDto->setEmail($login->getEmail());
        $loginDto->setPassword($login->getPassword());
        $loginDto->setRol($login->getRol());

        return $loginDto;
    }



}
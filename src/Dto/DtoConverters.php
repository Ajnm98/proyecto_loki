<?php

namespace App\Dto;

use App\Entity\Amigos;
use App\Entity\Bloqueados;
use App\Entity\Chat;
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

    public function BloqueadoToDto(Bloqueados $bloqueado):BloqueadosDTO
    {
        $bloqueadoDto = new BloqueadosDTO();
        $bloqueadoDto->setId($bloqueado->getId());
        $bloqueadoDto->setUsuarioId($this->UsuarioToDto($bloqueado->getUsuarioId()));
        $bloqueadoDto->setBloqueadoId($this->UsuarioToDto($bloqueado->getBloqueadoId()));

        return $bloqueadoDto;

    }

    public function ChatToDto(Chat $chat):ChatDTO
    {
        $chatDto = new ChatDTO();
        $chatDto->setId($chat->getId());
        $chatDto->setUsuarioIdEmisor($this->UsuarioToDto($chat->getUsuarioIdEmisor()));
        $chatDto->setUsuarioIdReceptor($this->UsuarioToDto($chat->getUsuarioIdReceptor()));
        $chatDto->setTexto($chat->getTexto());
        $chatDto->setFecha($chat->getFecha());
        if($chat->getFoto()!=null) {
            $chatDto->setFoto($chat->getFoto());
        }

        return $chatDto;

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
<?php

namespace App\Dto;

use App\Entity\Amigos;
use App\Entity\Bloqueados;
use App\Entity\Chat;
use App\Entity\Login;
use App\Entity\Publicacion;
use App\Entity\Respuesta;
use App\Entity\Usuario;

class DtoConverters
{
    public function amigosToDto(Amigos $amigos):AmigosDTO
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

    public function chatToDto(Chat $chat):ChatDTO
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


    public function publicacionToDto(Publicacion $publicacion):PublicacionDTO
    {
        $publicacionDto = new PublicacionDTO();
        $publicacionDto->setId($publicacion->getId());
        $publicacionDto->setUsuarioId($this->UsuarioToDto($publicacion->getUsuarioId()));
        $publicacionDto->setTexto($publicacion->getTexto());
        $publicacionDto->setFecha($publicacion->getFecha());
        if($publicacion->getFoto()!=null) {
            $publicacionDto->setFoto($publicacion->getFoto());
        }
        if($publicacion->getLikes()!=null) {
            $publicacionDto->setLikes($publicacion->getLikes());
        }


        return $publicacionDto;
    }

    public function respuestaToDto(Respuesta $respuesta):RespuestaDTO
    {

    $respuestaDto = new RespuestaDTO();
    $respuestaDto->setId($respuesta->getId());
    $respuestaDto->setUsuarioId($this->UsuarioToDto($respuesta->getUsuarioId()));
    $respuestaDto->setPublicacionId($this->publicacionToDto($respuesta->getPublicacionId()));
    $respuestaDto->setTexto($respuesta->getTexto());
    $respuestaDto->setFecha($respuesta->getFecha());
     if($respuesta->getFoto()!=null) {
         $respuestaDto->setFoto($respuesta->getFoto());
     }
    $respuestaDto->setLikes($respuesta->getLikes());
      if($respuesta->getTag()!=null) {
          $respuestaDto->setTag($respuesta->getTag());
      }

        return $respuestaDto;
    }


    public function UsuarioToDto(Usuario $usuario):UsuarioDTO
    {

        $usuarioDto = new UsuarioDTO();
        $usuarioDto->setId($usuario->getId());

        $usuarioDto->setUsuario($usuario->getUsuario());

        if($usuario->getNombre()!=null) {
            $usuarioDto->setNombre($usuario->getNombre());
        }
        if($usuario->getNick()!=null) {
            $usuarioDto->setNick($usuario->getNick());
        }
//        $usuarioDto->setLogin($this->LoginToDto($usuario->getLogin()));
        if($usuario->getTelefono()!=null) {
            $usuarioDto->setTelefono($usuario->getTelefono());
        }
        return $usuarioDto;

    }

    public function UsuarioToDto2(Usuario $usuario):UsuarioDTO
    {

        $usuarioDto = new UsuarioDTO();
        $usuarioDto->setId($usuario->getId());

        if($usuario->getUsuario()!=null) {
            $usuarioDto->setUsuario($usuario->getUsuario());
        }
        if($usuario->getNombre()!=null) {
            $usuarioDto->setNombre($usuario->getNombre());
        }
        if($usuario->getNick()!=null) {
            $usuarioDto->setNick($usuario->getNick());
        }
        if($usuario->getLogin()!=null) {
            $usuarioDto->setLogin($this->LoginToDto($usuario->getLogin()));
        }
        if($usuario->getTelefono()!=null) {
            $usuarioDto->setTelefono($usuario->getTelefono());
        }

        if($usuario->getFoto()!=null) {
            $usuarioDto->setFoto($usuario->getFoto());
        }

        if($usuario->getEncabezado()!=null) {
            $usuarioDto->setEncabezado($usuario->getEncabezado());
        }

        return $usuarioDto;

    }

    public function loginToDto(Login $login):LoginDTO
    {
        $loginDto = new LoginDTO();
        $loginDto->setId($login->getId());
        $loginDto->setEmail($login->getEmail());
        $loginDto->setPassword($login->getPassword());
        $loginDto->setRol($login->getRol());

        return $loginDto;
    }



}
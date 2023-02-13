<?php

namespace App\Dto;

use App\Entity\Usuario;

class ApiKeyDTO
{
    private string $token;
    private \DateTimeInterface $fechaExpiracion;

    private UsuarioDTO $usuario;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getFechaExpiracion(): \DateTimeInterface
    {
        return $this->fechaExpiracion;
    }

    /**
     * @param \DateTimeInterface $fechaExpiracion
     */
    public function setFechaExpiracion(\DateTimeInterface $fechaExpiracion): void
    {
        $this->fechaExpiracion = $fechaExpiracion;
    }

    /**
     * @return UsuarioDTO
     */
    public function getUsuario(): UsuarioDTO
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioDTO $usuario
     */
    public function setUsuario(UsuarioDTO $usuario): void
    {
        $this->usuario = $usuario;
    }


}
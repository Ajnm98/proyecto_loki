<?php

namespace App\Dto;

class BloqueadosDTO
{
    private int $id;
    private UsuarioDTO $usuario_id;
    private UsuarioDTO $bloqueado_id;

    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return UsuarioDTO
     */
    public function getUsuarioId(): UsuarioDTO
    {
        return $this->usuario_id;
    }

    /**
     * @param UsuarioDTO $usuario_id
     */
    public function setUsuarioId(UsuarioDTO $usuario_id): void
    {
        $this->usuario_id = $usuario_id;
    }

    /**
     * @return UsuarioDTO
     */
    public function getBloqueadoId(): UsuarioDTO
    {
        return $this->bloqueado_id;
    }

    /**
     * @param UsuarioDTO $bloqueado_id
     */
    public function setBloqueadoId(UsuarioDTO $bloqueado_id): void
    {
        $this->bloqueado_id = $bloqueado_id;
    }



}
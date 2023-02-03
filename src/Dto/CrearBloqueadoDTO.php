<?php

namespace App\Dto;

class CrearBloqueadoDTO
{
    private int $usuario_id;
    private int $bloqueado_id;

    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getUsuarioId(): int
    {
        return $this->usuario_id;
    }

    /**
     * @param int $usuario_id
     */
    public function setUsuarioId(int $usuario_id): void
    {
        $this->usuario_id = $usuario_id;
    }

    /**
     * @return int
     */
    public function getBloqueadoId(): int
    {
        return $this->bloqueado_id;
    }

    /**
     * @param int $bloqueado_id
     */
    public function setBloqueadoId(int $bloqueado_id): void
    {
        $this->bloqueado_id = $bloqueado_id;
    }



}
<?php

namespace App\Dto;

class CrearAmigoDTO
{
    private int $usuario_id;
    private int $amigo_id;

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
    public function getAmigoId(): int
    {
        return $this->amigo_id;
    }

    /**
     * @param int $amigo_id
     */
    public function setAmigoId(int $amigo_id): void
    {
        $this->amigo_id = $amigo_id;
    }



}
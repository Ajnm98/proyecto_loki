<?php

namespace App\Dto;

class AmigosDTO
{
    private int $id;
    private UsuarioDTO $usuario_id;
    private UsuarioDTO $amigo_id;

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
    public function getAmigoId(): UsuarioDTO
    {
        return $this->amigo_id;
    }

    /**
     * @param UsuarioDTO $amigo_id
     */
    public function setAmigoId(UsuarioDTO $amigo_id): void
    {
        $this->amigo_id = $amigo_id;
    }




}
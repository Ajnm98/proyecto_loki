<?php

namespace App\Dto;

class UsuarioBuscarNombreDTO
{
    private string $nombre;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }



}
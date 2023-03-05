<?php

namespace App\Dto;

class CrearRespuestaDTO
{

    private int $usuario_id;
    private int $publicacion_id;
    private string $texto;
    private string $foto;

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
    public function getPublicacionId(): int
    {
        return $this->publicacion_id;
    }

    /**
     * @param int $publicacion_id
     */
    public function setPublicacionId(int $publicacion_id): void
    {
        $this->publicacion_id = $publicacion_id;
    }

    /**
     * @return string
     */
    public function getTexto(): string
    {
        return $this->texto;
    }

    /**
     * @param string $texto
     */
    public function setTexto(string $texto): void
    {
        $this->texto = $texto;
    }

    /**
     * @return string
     */
    public function getFoto(): string
    {
        return $this->foto;
    }

    /**
     * @param string $foto
     */
    public function setFoto(string $foto): void
    {
        $this->foto = $foto;
    }



}
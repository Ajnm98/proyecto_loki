<?php

namespace App\Dto;

class CrearChatDTO
{

    private int $usuario_id_emisor;
    private int $usuario_id_receptor;
    private string $texto;
    private string $foto;


    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getUsuarioIdEmisor(): int
    {
        return $this->usuario_id_emisor;
    }

    /**
     * @param int $usuario_id_emisor
     */
    public function setUsuarioIdEmisor(int $usuario_id_emisor): void
    {
        $this->usuario_id_emisor = $usuario_id_emisor;
    }

    /**
     * @return int
     */
    public function getUsuarioIdReceptor(): int
    {
        return $this->usuario_id_receptor;
    }

    /**
     * @param int $usuario_id_receptor
     */
    public function setUsuarioIdReceptor(int $usuario_id_receptor): void
    {
        $this->usuario_id_receptor = $usuario_id_receptor;
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
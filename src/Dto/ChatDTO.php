<?php

namespace App\Dto;

class ChatDTO
{
    private int $id;
    private UsuarioDTO $usuario_id_emisor;
    private UsuarioDTO $usuario_id_receptor;
    private string $texto;
    private string $fecha;
    private string $foto;

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
    public function getUsuarioIdEmisor(): UsuarioDTO
    {
        return $this->usuario_id_emisor;
    }

    /**
     * @param UsuarioDTO $usuario_id_emisor
     */
    public function setUsuarioIdEmisor(UsuarioDTO $usuario_id_emisor): void
    {
        $this->usuario_id_emisor = $usuario_id_emisor;
    }

    /**
     * @return UsuarioDTO
     */
    public function getUsuarioIdReceptor(): UsuarioDTO
    {
        return $this->usuario_id_receptor;
    }

    /**
     * @param UsuarioDTO $usuario_id_receptor
     */
    public function setUsuarioIdReceptor(UsuarioDTO $usuario_id_receptor): void
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
    public function getFecha(): string
    {
        return $this->fecha;
    }

    /**
     * @param string $fecha
     */
    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
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
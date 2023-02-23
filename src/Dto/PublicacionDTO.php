<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\Date;
use Doctrine\Common\Collections\Collection;


class PublicacionDTO
{
    private int $id;
    private UsuarioDTO $usuario_id;
    private string $texto;
    private string $fecha;
    private string $foto;
    private int $likes;


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

    /**
     * @return int
     */
    public function getLikes(): int
    {
        return $this->likes;
    }

    /**
     * @param int $likes
     */
    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }





}
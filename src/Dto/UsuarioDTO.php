<?php

namespace App\Dto;

class UsuarioDTO
{
    private int $id;
    private string $usuario;
    private string $nombre;
    private string $nick;
    private LoginDTO $login;
    private string $fecha;
    private int $telefono;
    private int $foto;


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
     * @return string
     */
    public function getUsuario(): string
    {
        return $this->usuario;
    }

    /**
     * @param string $usuario
     */
    public function setUsuario(string $usuario): void
    {
        $this->usuario = $usuario;
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

    /**
     * @return string
     */
    public function getNick(): string
    {
        return $this->nick;
    }

    /**
     * @param string $nick
     */
    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }

    /**
     * @return LoginDTO
     */
    public function getLogin(): LoginDTO
    {
        return $this->login;
    }

    /**
     * @param LoginDTO $login
     */
    public function setLogin(LoginDTO $login): void
    {
        $this->login = $login;
    }

    /**
     * @return \DateTime
     */
    public function getFecha(): \DateTime
    {
        return $this->fecha;
    }

    /**
     * @param \DateTime $fecha
     */
    public function setFecha(\DateTime $fecha): void
    {
        $this->fecha = $fecha;
    }

    /**
     * @return int
     */
    public function getTelefono(): int
    {
        return $this->telefono;
    }

    /**
     * @param int $telefono
     */
    public function setTelefono(int $telefono): void
    {
        $this->telefono = $telefono;
    }

    /**
     * @return int
     */
    public function getFoto(): int
    {
        return $this->foto;
    }

    /**
     * @param int $foto
     */
    public function setFoto(int $foto): void
    {
        $this->foto = $foto;
    }


}
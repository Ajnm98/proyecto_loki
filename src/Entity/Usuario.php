<?php

namespace App\Entity;
use App\Repository\UsuarioRepository;
use DateTime;
use Date;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;


#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\Table]
class Usuario
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $usuario = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nick = null;

    #[ORM\OneToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'login_id',nullable: false)]

    private ?Login $login = null;

    #[ORM\Column(length: 200,nullable: true)]
    private ?String $fecha = null;

    #[ORM\Column]
    private ?int $telefono = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $encabezado = null;



    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUsuario(): ?String
    {
        return $this->usuario;
    }

    public function setUsuario(int $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
    public function getNombre(): ?String
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }
    public function getNick(): ?String
    {
        return $this->nick;
    }

    public function setNick(int $nick): self
    {
        $this->nick = $nick;

        return $this;
    }
    public function getLogin(): ?Login
    {
        return $this->login;
    }

    public function setLogin(Login $login): self
    {
        $this->login = $login;

        return $this;
    }
    public function getfecha(): ?String
    {
        return $this->fecha;
    }

    public function setFecha(int $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }
    public function getTelefono(): ?int
    {
        return $this->telefono;
    }

    public function setTelefono(int $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }
    public function getFoto(): ?String
    {
        return $this->foto;
    }

    public function setFoto(int $foto): self
    {
        $this->foto = $foto;

        return $this;
    }
    public function getEncabezado(): ?String
    {
        return $this->encabezado;
    }

    public function setEmcabezado(int $encabezado): self
    {
        $this->encabezado = $encabezado;

        return $this;
    }


}
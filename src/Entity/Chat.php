<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Cassandra\Date;
use DateTime;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
#[ORM\Table]
class Chat
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'chat', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'usuario_id_emisor',nullable: false)]
    private ?Usuario $usuario_id_emisor = null;

    #[ORM\ManyToOne(inversedBy: 'chat', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'usuario_id_receptor',nullable: false)]
    private ?Usuario $usuario_id_receptor = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $texto = null;

    #[ORM\Column]
    private ?DateTime $fecha = null;

    #[ORM\Column(nullable: true)]
    private ?string $foto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
    public function getUsuario_id_emisor(): ?int
    {
        return $this->usuario_id_emisor;
    }

    public function setUsuario_id_emisor(int $usuario_id_emisor): self
    {
        $this->usuario_id_emisor = $usuario_id_emisor;

        return $this;
    }
    public function getUsuario_id_receptor(): ?int
    {
        return $this->usuario_id_receptor;
    }

    public function setUsuario_id_receptor(string $usuario_id_receptor): self
    {
        $this->usuario_id_receptor= $usuario_id_receptor;

        return $this;
    }
    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;

        return $this;
    }
    public function getFecha(): ?DateTime
    {
        return $this->fecha;
    }

    public function setFecha(DateTime $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }
    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

}
<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'usuario_id_emisor',nullable: false)]
    private ?Usuario $usuario_id_emisor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'usuario_id_receptor',nullable: false)]
    private ?Usuario $usuario_id_receptor = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'texto',nullable: false)]
    private ?string $texto = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'fecha',nullable: false)]
    private ?string $fecha = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'foto',nullable: true)]
    private ?string $foto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarioIdEmisor(): ?Usuario
    {
        return $this->usuario_id_emisor;
    }

    public function setUsuarioIdEmisor(?Usuario $usuario_id_emisor): self
    {
        $this->usuario_id_emisor = $usuario_id_emisor;

        return $this;
    }

    public function getUsuarioIdReceptor(): ?Usuario
    {
        return $this->usuario_id_receptor;
    }

    public function setUsuarioIdReceptor(?Usuario $usuario_id_receptor): self
    {
        $this->usuario_id_receptor = $usuario_id_receptor;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(?string $texto): self
    {
        $this->texto = $texto;

        return $this;
    }

    public function getFecha(): ?string
    {
        return $this->fecha->format('Y-m-d H:i:s');
    }

    public function setFecha(?string $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }


}

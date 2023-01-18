<?php

namespace App\Entity;

use App\Repository\PublicacionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicacionRepository::class)]
class Publicacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'usuario_id',nullable: false)]
    private ?Usuario $usuario_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'texto',nullable: false)]
    private ?string $texto = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[ORM\JoinColumn(name: 'fecha',nullable: false)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'foto',nullable: false)]
    private ?string $foto = null;

    #[ORM\Column(nullable: true)]
    #[ORM\JoinColumn(name: 'likes',nullable: false)]
    private ?int $likes = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'tag',nullable: false)]
    private ?string $tag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarioId(): ?Usuario
    {
        return $this->usuario_id;
    }

    public function setUsuarioId(?Usuario $usuario_id): self
    {
        $this->usuario_id = $usuario_id;

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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
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

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}

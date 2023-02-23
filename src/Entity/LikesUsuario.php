<?php

namespace App\Entity;

use App\Repository\LikesUsuarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikesUsuarioRepository::class)]
#[ORM\Table('likesusuario')]
class LikesUsuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publicacion_likeUsuario')]
    #[ORM\JoinColumn(name: 'publicacion_id',nullable: false)]
    private ?Publicacion $publicacion_id = null;

    #[ORM\ManyToOne(inversedBy: 'usuario_likesUsuario')]
    #[ORM\JoinColumn(name: 'usuario_id',nullable: false)]
    private ?Usuario $usuario_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicacionId(): ?Publicacion
    {
        return $this->publicacion_id;
    }

    public function setPublicacionId(?Publicacion $publicacion_id): self
    {
        $this->publicacion_id = $publicacion_id;

        return $this;
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
}

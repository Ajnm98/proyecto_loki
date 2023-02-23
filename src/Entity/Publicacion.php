<?php

namespace App\Entity;

use App\Repository\PublicacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicacionRepository::class)]
#[ORM\Table]
class Publicacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'usuario_id',nullable: false)]
    private ?Usuario $usuario_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'texto',nullable: false)]
    private ?string $texto = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'fecha',nullable: false)]
    private ?string $fecha = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\JoinColumn(name: 'foto',nullable: false)]
    private ?string $foto = null;

    #[ORM\Column(nullable: true)]
    #[ORM\JoinColumn(name: 'likes',nullable: false)]
    private ?int $likes = null;

    #[ORM\OneToMany(mappedBy: 'publicacion_id', targetEntity: LikesUsuario::class)]
    private Collection $publicacion_likeUsuario;

    #[ORM\OneToMany(mappedBy: 'publicacion_id', targetEntity: PublicacionTags::class)]
    private Collection $tags;


    public function __construct()
    {
        $this->publicacion_likes = new ArrayCollection();
        $this->LikesUsuarios = new ArrayCollection();
        $this->publicacion_likeUsuario = new ArrayCollection();
    }

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

    public function getFecha(): ?string
    {
        return $this->fecha;
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

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

/**
 * @return Collection<int, LikesUsuario>
 */
public function getPublicacionLikeUsuario(): Collection
{
    return $this->publicacion_likeUsuario;
}

public function addPublicacionLikeUsuario(LikesUsuario $publicacionLikeUsuario): self
{
    if (!$this->publicacion_likeUsuario->contains($publicacionLikeUsuario)) {
        $this->publicacion_likeUsuario->add($publicacionLikeUsuario);
        $publicacionLikeUsuario->setPublicacionId($this);
    }

    return $this;
}

public function removePublicacionLikeUsuario(LikesUsuario $publicacionLikeUsuario): self
{
    if ($this->publicacion_likeUsuario->removeElement($publicacionLikeUsuario)) {
        // set the owning side to null (unless already changed)
        if ($publicacionLikeUsuario->getPublicacionId() === $this) {
            $publicacionLikeUsuario->setPublicacionId(null);
        }
    }

    return $this;
}

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Collection $tags
     */
    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }


}

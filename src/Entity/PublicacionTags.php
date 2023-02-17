<?php

namespace App\Entity;
use App\Repository\PublicacionTagsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicacionTagsRepository::class)]
#[ORM\Table]
class PublicacionTags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade:['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'tags_id',nullable: false)]
    private ?Tags $tags = null;

    #[ORM\ManyToOne(cascade:['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'publicacion_id',nullable: false)]
    private ?Publicacion $publicacion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Tags|null
     */
    public function getTags(): ?Tags
    {
        return $this->tags;
    }

    /**
     * @param Tags|null $tags
     */
    public function setTags(?Tags $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return Publicacion|null
     */
    public function getPublicacion(): ?Publicacion
    {
        return $this->publicacion;
    }

    /**
     * @param Publicacion|null $publicacion
     */
    public function setPublicacion(?Publicacion $publicacion): void
    {
        $this->publicacion = $publicacion;
    }




}
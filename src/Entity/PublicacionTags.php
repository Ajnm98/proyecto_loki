<?php

namespace App\Entity;
use App\Repository\PublicacionTagsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicacionTagsRepository::class)]
#[ORM\Table(name: "publicaciontags")]
class PublicacionTags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade:['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'tags_id',nullable: false)]
    private ?Tags $tags_id = null;

    #[ORM\ManyToOne(cascade:['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'publicacion_id',nullable: false)]
    private ?Publicacion $publicacion_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Tags|null
     */
    public function getTagsId(): ?Tags
    {
        return $this->tags_id;
    }

    /**
     * @param Tags|null $tags_id
     */
    public function setTagsId(?Tags $tags_id): void
    {
        $this->tags_id = $tags_id;
    }

    /**
     * @return Publicacion|null
     */
    public function getPublicacionId(): ?Publicacion
    {
        return $this->publicacion_id;
    }

    /**
     * @param Publicacion|null $publicacion_id
     */
    public function setPublicacionId(?Publicacion $publicacion_id): void
    {
        $this->publicacion_id = $publicacion_id;
    }







}
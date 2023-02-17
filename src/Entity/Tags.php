<?php

namespace App\Entity;
use App\Repository\TagsRepository;
use DateTime;
use Date;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ORM\Entity(repositoryClass: TagsRepository::class)]
#[ORM\Table]
class Tags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $nombre = null;
    #[ORM\Column]
    private ?int $contador = null;

    #[ORM\Column(length: 500,nullable: true)]
    private ?string $fecha_expiracion = null;

    #[ORM\OneToMany(mappedBy: 'tags_id', targetEntity: PublicacionTags::class)]
    private Collection $tags_id;

    /**
     * @return Collection
     */
    public function getTagsId(): Collection
    {
        return $this->tags_id;
    }

    /**
     * @param Collection $tags_id
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    /**
     * @param string|null $nombre
     */
    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * @return int|null
     */
    public function getContador(): ?int
    {
        return $this->contador;
    }

    /**
     * @param int|null $contador
     */
    public function setContador(?int $contador): void
    {
        $this->contador = $contador;
    }

    /**
     * @return string|null
     */
    public function getFechaExpiracion(): ?string
    {
        return $this->fecha_expiracion;
    }

    /**
     * @param string|null $fecha_expiracion
     */
    public function setFechaExpiracion(?string $fecha_expiracion): void
    {
        $this->fecha_expiracion = $fecha_expiracion;
    }

    /**
     * @return string|null
     */







}
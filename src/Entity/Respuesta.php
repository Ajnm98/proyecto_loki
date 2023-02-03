<?php

namespace App\Entity;
use App\Repository\RespuestaRepository;
use DateTime;
use Date;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;


#[ORM\Entity(repositoryClass: RespuestaRepository::class)]
#[ORM\Table]
class Respuesta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade:['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'usuario_id',nullable: false)]
    private ?Usuario $usuario_id = null;

    #[ORM\ManyToOne(cascade:['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'publicacion_id',nullable: false)]
    private ?Publicacion $publicacion_id = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $texto = null;

    #[ORM\Column(length: 200,nullable: true)]
    private ?String $fecha = null;

    #[ORM\Column(length: 200,nullable: true)]
    private ?String $foto = null;
    #[ORM\Column]
    private ?int $likes = null;

    #[ORM\Column(length: 200,nullable: true)]
    private ?String $tag = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getUsuarioId(): ?Usuario
    {
        return $this->usuario_id;
    }

    /**
     * @return int|null
     */
    public function getPublicacionId(): ?Publicacion
    {
        return $this->publicacion_id;
    }

    /**
     * @return string|null
     */
    public function getTexto(): ?string
    {
        return $this->texto;
    }

    /**
     * @return String|null
     */
    public function getFecha(): ?string
    {
        return $this->fecha;
    }

    /**
     * @return String|null
     */
    public function getFoto(): ?string
    {
        return $this->foto;
    }

    /**
     * @return int|null
     */
    public function getLikes(): ?int
    {
        return $this->likes;
    }

    /**
     * @return String|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param int|null $usuario_id
     */
    public function setUsuarioId(?Usuario $usuario_id): void
    {
        $this->usuario_id = $usuario_id;
    }

    /**
     * @param int|null $publicacion_id
     */
    public function setPublicacionId(?Publicacion $publicacion_id): void
    {
        $this->publicacion_id = $publicacion_id;
    }

    /**
     * @param string|null $texto
     */
    public function setTexto(?string $texto): void
    {
        $this->texto = $texto;
    }

    /**
     * @param String|null $fecha
     */
    public function setFecha(?string $fecha): void
    {
        $this->fecha = $fecha;
    }

    /**
     * @param String|null $foto
     */
    public function setFoto(?string $foto): void
    {
        $this->foto = $foto;
    }

    /**
     * @param int|null $likes
     */
    public function setLikes(?int $likes): void
    {
        $this->likes = $likes;
    }

    /**
     * @param String|null $tag
     */
    public function setTag(?string $tag): void
    {
        $this->tag = $tag;
    }



}
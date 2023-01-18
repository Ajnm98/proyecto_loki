<?php

namespace App\Entity;
use App\Repository\AmigosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: AmigosRepository::class)]
#[ORM\Table]
class Amigos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(fetch: 'EAGER',cascade:['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'usuario_id',nullable: false)]
    private ?Usuario $usuario_id = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'amigo_id',nullable: false)]
    private ?Usuario $amigo_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarioId(): ?Usuario
    {
        return $this->usuario_id;
    }
    public function setUsuario_Id(Usuario $usuario_id): self
    {
        $this->usuario_id = $usuario_id;

        return $this;
    }

    public function getAmigoId(): ?Usuario
    {
        return $this->amigo_id;
    }

    public function setAmigo_Id(Usuario $amigo_id): self
    {
        $this->amigo_id = $amigo_id;

        return $this;
    }

}
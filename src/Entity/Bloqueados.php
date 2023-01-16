<?php

namespace App\Entity;

use App\Repository\Bloqueados2Repository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BloqueadosRepository::class)]
class Bloqueados
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'usuario_bloquea_id', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'usuario_id',nullable: false)]
    private ?Usuario $usuario_id = null;

    #[ORM\ManyToOne(inversedBy: 'usuario_bloqueado_id')]
    #[ORM\JoinColumn(name: 'bloqueado_id',nullable: false)]
    private ?Usuario $bloqueado_id = null;

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


    public function getBloqueadoId(): ?Usuario
    {
        return $this->bloqueado_id;
    }

    public function setBloqueadoId(?Usuario $bloqueado_id): self
    {
        $this->bloqueado_id = $bloqueado_id;

        return $this;
    }
}

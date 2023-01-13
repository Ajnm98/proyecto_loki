<?php

namespace App\Entity;


use App\Repository\BloqueadosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BloqueadosRepository::class)]
#[ORM\Table]
class Bloqueados
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bloqueados', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'usuario_id',nullable: false)]
    private ?Usuario $usuario_id = null;

    #[ORM\OneToOne(inversedBy: 'bloqueados', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'bloqueado_id',nullable: false)]
    private ?Usuario $bloqueado_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUsuario_id(): ?int
    {
        return $this->usuario_id;
    }

    public function setUsuario_id(int $usuario_id): self
    {
        $this->usuario_id = $usuario_id;

        return $this;
    }

    public function getBloqueado_id(): ?int
    {
        return $this->bloqueado_id;
    }

    public function setBloqueado_id(int $bloqueado_id): self
    {
        $this->bloqueado_id = $bloqueado_id;

        return $this;
    }

}
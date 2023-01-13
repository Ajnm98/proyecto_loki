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

    #[ORM\Column]
    private ?int $usuario_id = null;

    #[ORM\Column]
    private ?int $amigo_id = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario_Id(): ?int
    {
        return $this->usuario_id;
    }
    public function setUsuario_Id(int $usuario_id): self
    {
        $this->usuario_id = $usuario_id;

        return $this;
    }
    public function getAmigo_Id(): ?int
    {
        return $this->amigo_id;
    }
    public function setAmigo_Id(int $amigo_id): self
    {
        $this->amigo_id = $amigo_id;

        return $this;
    }

}
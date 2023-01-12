<?php

namespace App\Entity;
use App\Repository\AmigosRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
#[ORM\Entity(repositoryClass: MensajeRepository::class)]

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

    public function getAmigo_Id(): ?int
    {
        return $this->amigo_id;
    }

}
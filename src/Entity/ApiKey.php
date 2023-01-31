<?php

namespace App\Entity;

use App\Repository\ApiKeyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ApiKeyRepository::class)]
class ApiKey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_query'])]
    private ?int $id = null;

    #[ORM\Column(length: 400)]
    #[Groups(['user_query'])]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user_query'])]
    private ?\DateTimeInterface $fechaExpiracion = null;

    #[ORM\ManyToOne(inversedBy: 'apiKeys')]
    #[ORM\JoinColumn(name: 'id_usuario',nullable: false)]
    private ?Usuario $usuario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getFechaExpiracion(): ?\DateTimeInterface
    {
        return $this->fechaExpiracion;
    }

    public function setFechaExpiracion(\DateTimeInterface $fechaExpiracion): self
    {
        $this->fechaExpiracion = $fechaExpiracion;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
}
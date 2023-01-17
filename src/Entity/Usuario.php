<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;


#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\Table]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $usuario = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nick = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $fecha = null;

    #[ORM\Column]
    private ?int $telefono = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $encabezado = null;


    #[ORM\OneToOne(mappedBy: 'usuario_id', cascade: ['persist', 'remove'])]
    private ?Bloqueados $usuario_bloquea_id = null;

    #[ORM\OneToMany(mappedBy: 'bloqueado_id', targetEntity: Bloqueados::class)]
    private Collection $usuario_bloqueado_id;

    #[ORM\OneToMany(mappedBy: 'usuario_id_emisor', targetEntity: Chat::class)]
    private Collection $usuario_id_emisor;

    #[ORM\OneToMany(mappedBy: 'bloqueado_id', targetEntity: Chat::class)]
    private Collection $usuario_id_receptor;

    #[ORM\OneToMany(mappedBy: 'usuario_id', targetEntity: Publicacion::class)]
    private Collection $usuario_publicacion_id;

    public function __construct()
    {
        $this->usuario_bloqueado_id= new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(?string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFecha(): ?string
    {
        return $this->fecha->format('Y-m-d H:i:s');
    }

    public function setFecha(?DateTime $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTelefono(): ?int
    {
        return $this->telefono;
    }

    public function setTelefono(?int $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getEncabezado(): ?string
    {
        return $this->encabezado;
    }

    public function setEncabezado(?string $encabezado): self
    {
        $this->encabezado = $encabezado;

        return $this;
    }

//    public function getUsuarioBloqueaId(): ?Bloqueados
//    {
//        return $this->usuario_bloquea_id;
//    }

    public function setUsuarioBloqueaId(?Bloqueados $usuario_bloquea_id): self
    {
        // unset the owning side of the relation if necessary
        if ($usuario_bloquea_id === null && $this->usuario_bloquea_id !== null) {
            $this->usuario_bloquea_id->setUsuarioId(null);
        }

        // set the owning side of the relation if necessary
        if ($usuario_bloquea_id !== null && $usuario_bloquea_id->getUsuarioId() !== $this) {
            $usuario_bloquea_id->setUsuarioId($this);
        }

        $this->usuario_bloquea_id = $usuario_bloquea_id;

        return $this;
    }


//    /**
//     * @return Collection<int, Bloqueados>
//     */
//    public function getUsuarioBloqueadoId(): Collection
//    {
//        return $this->usuario_bloqueado_id;
//    }

    public function addUsuarioBloqueadoId(Bloqueados $usuarioBloqueadoId): self
    {
        if (!$this->usuario_bloqueado_id->contains($usuarioBloqueadoId)) {
            $this->usuario_bloqueado_id->add($usuarioBloqueadoId);
            $usuarioBloqueadoId->setBloqueadoId($this);
        }

        return $this;
    }

    public function removeUsuarioBloqueadoId(Bloqueados $usuarioBloqueadoId): self
    {
        if ($this->usuario_bloqueado_id->removeElement($usuarioBloqueadoId)) {
            // set the owning side to null (unless already changed)
            if ($usuarioBloqueadoId->getBloqueadoId() === $this) {
                $usuarioBloqueadoId->setBloqueadoId(null);
            }
        }

        return $this;
    }
}

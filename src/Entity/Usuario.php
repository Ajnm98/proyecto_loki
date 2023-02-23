<?php

namespace App\Entity;
use App\Repository\UsuarioRepository;
use DateTime;
use Date;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nick = null;

    #[ORM\OneToOne(cascade:['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'login_id',nullable: false)]
    private ?Login $login = null;

    #[ORM\Column(length: 500,nullable: true)]
    private ?string $fecha = null;

    #[ORM\Column]
    private ?int $telefono = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $encabezado = null;

    #[OneToMany( mappedBy: 'usuario', targetEntity: Amigos::class )]
    private Amigos|null $usuario_id = null;

    #[OneToMany( mappedBy: 'usuario', targetEntity: Amigos::class)]
    private Amigos|null $amigo_id = null;

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

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: ApiKey::class, orphanRemoval: true)]
    private Collection $apiKeys;

    #[ORM\OneToMany(mappedBy: 'usuario_id', targetEntity: LikesUsuario::class)]
    private Collection $usuario_likesUsuario;


    public function __construct()
    {
        $this->usuario_bloqueado_id= new ArrayCollection();
        $this->likesUsuarios = new ArrayCollection();
        $this->likesusuario_id = new ArrayCollection();
        $this->usuario_likesUsuario = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUsuario(): ?String
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
    public function getNombre(): ?String
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }
    public function getNick(): ?String
    {
        return $this->nick;
    }

    public function setNick(?string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }
    public function getLogin(): ?Login
    {
        return $this->login;
    }

    public function setLogin(Login $login): self
    {
        $this->login = $login;

        return $this;
    }
    public function getfecha(): ?String
    {
        return $this->fecha;
    }

    public function setFecha(?String $fecha): self
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
    public function getFoto(): ?String
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }
    public function getEncabezado(): ?String
    {
        return $this->encabezado;
    }

    public function setEncabezado(?string $encabezado): self
    {
        $this->encabezado = $encabezado;

        return $this;
    }
    public function getUsuarioBloqueaId(): ?Bloqueados
    {
        return $this->usuario_bloquea_id;
    }

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


    /**
     * @return Collection<int, Bloqueados>
     */
    public function getUsuarioBloqueadoId(): Collection
    {
        return $this->usuario_bloqueado_id;
    }

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
    /**
     * @return Collection<int, ApiKey>
     */
    public function getApiKeys(): Collection
    {
        return $this->apiKeys;
    }

    public function addApiKey(ApiKey $apiKey): self
    {
        if (!$this->apiKeys->contains($apiKey)) {
            $this->apiKeys->add($apiKey);
            $apiKey->setUsuario($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, LikesUsuario>
     */
    public function getUsuarioLikesUsuario(): Collection
    {
        return $this->usuario_likesUsuario;
    }

    public function addUsuarioLikesUsuario(LikesUsuario $usuarioLikesUsuario): self
    {
        if (!$this->usuario_likesUsuario->contains($usuarioLikesUsuario)) {
            $this->usuario_likesUsuario->add($usuarioLikesUsuario);
            $usuarioLikesUsuario->setUsuarioId($this);
        }

        return $this;
    }

    public function removeUsuarioLikesUsuario(LikesUsuario $usuarioLikesUsuario): self
    {
        if ($this->usuario_likesUsuario->removeElement($usuarioLikesUsuario)) {
            // set the owning side to null (unless already changed)
            if ($usuarioLikesUsuario->getUsuarioId() === $this) {
                $usuarioLikesUsuario->setUsuarioId(null);
            }
        }

        return $this;
    }



}


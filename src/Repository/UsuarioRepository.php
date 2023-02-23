<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Usuario>
 *
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function save(Usuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Usuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function borrarUsuario(int $usuarioID): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM usuario
            WHERE id = :id 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $usuarioID]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function buscarNick(string $nick): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT *
            FROM usuario
            WHERE nick LIKE :nick
            ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['nick' => $nick]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function buscarNombre(string $nombre): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT *
            FROM usuario
            WHERE nombre LIKE :nombre
            ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['nombre' => $nombre]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }


    public function editarUsuario(Usuario $usuario): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
          UPDATE usuario SET usuario = :usuario, nombre = :nombre,
            nick = :nick, fecha = :fecha, telefono = :telefono,
            foto = :foto, encabezado = :encabezado
            WHERE id = :id
            ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['usuario' => $usuario->getUsuario(), 'nombre' => $usuario->getNombre(),
            'nick' => $usuario->getNick(), 'fecha' => $usuario->getfecha(), 'telefono' => $usuario->getTelefono(), 'foto' => $usuario->getFoto(),
            'encabezado' => $usuario->getEncabezado(), 'id' => $usuario->getId()]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

}

<?php

namespace App\Repository;

use App\Entity\LikesUsuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LikesUsuario>
 *
 * @method LikesUsuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikesUsuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikesUsuario[]    findAll()
 * @method LikesUsuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikesUsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikesUsuario::class);
    }

    public function save(LikesUsuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LikesUsuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function borrarLikesUsuario(int $usuarioId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM likesusuario
            WHERE usuario_id = :id 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $usuarioId]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return LikesUsuario[] Returns an array of LikesUsuario objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LikesUsuario
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function likessumadoborrar(int $usuario_ID, int $publicacion_ID): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            DELETE FROM likesusuario
            WHERE usuario_id = :usuarioId AND publicacion_id = :publicacionId
            ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['usuarioId' => $usuario_ID, 'publicacionId' => $publicacion_ID]);
        return $resultSet->fetchAllAssociative();

    }
}

<?php

namespace App\Repository;

use App\Entity\Publicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publicacion>
 *
 * @method Publicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publicacion[]    findAll()
 * @method Publicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicacion::class);
    }

    public function save(Publicacion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Publicacion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function borrarPublicacion(int $publicacionID): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM publicacion
            WHERE id = :publicacionId 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['publicacionId' => $publicacionID]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
    public function borrarPublicacionPorUsuario(int $usuarioId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM publicacion
            WHERE usuario_id = :id 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $usuarioId]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function sumarLike(int $publicacionId, int $likes): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            UPDATE publicacion
            SET likes = :likessum
            WHERE id = :publicacionId 
            ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['likessum' => $likes, 'publicacionId' => $publicacionId]);

        return $resultSet->fetchAllAssociative();
    }



//    /**
//     * @return Publicacion2[] Returns an array of Publicacion2 objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Publicacion2
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

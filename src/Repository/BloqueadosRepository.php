<?php

namespace App\Repository;

use App\Entity\Bloqueados;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bloqueados>
 *
 * @method Bloqueados|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bloqueados|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bloqueados[]    findAll()
 * @method Bloqueados[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BloqueadosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bloqueados::class);
    }

    public function save(Bloqueados $entity, bool $flush = false): void
    {
         $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bloqueados $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function borrarBloqueadosPorUsuario(int $usuarioId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM bloqueados
            WHERE usuario_id = :id 
            OR bloqueado_id = :id
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $usuarioId]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function desbloquear(int $usuario_ID, int $desbloqueado_ID): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM bloqueados
            WHERE usuario_id = :usuarioId AND bloqueado_id = :desbloqueadoId
            ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['usuarioId' => $usuario_ID, 'desbloqueadoId' => $desbloqueado_ID]);
        return $resultSet->fetchAllAssociative();
    }


//    /**
//     * @return Bloqueados2[] Returns an array of Bloqueados2 objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bloqueados2
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

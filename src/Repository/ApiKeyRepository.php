<?php

namespace App\Repository;

use App\Entity\ApiKey;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiKey>
 *
 * @method ApiKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiKey[]    findAll()
 * @method ApiKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiKey::class);
    }

    public function save(ApiKey $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiKey $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ApiKey[] Returns an array of ApiKey objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findApiKeyValida($usuario): ?ApiKey
    {
        $fechaActual = date("Y-m-d H:i:s");
        $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $fechaActual);


        return $this->createQueryBuilder('a')
            ->andWhere('a.usuario = :val and a.fechaExpiracion >= :fecha ')
            ->setParameter('val', $usuario)
            ->setParameter('fecha', $fecha)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


//    public function findApiKeyValida($id_usuario):string
//    {
//
//        $rsm = new ResultSetMapping();
//
//        $query = $this->getEntityManager()->createNativeQuery('select token from api_key ak  where ak.id_usuario  = ? and ak.fecha_expiracion >= ? ', $rsm);
//        $query->setParameter(1, $id_usuario);
//        $query->setParameter(2, date("Y-m-d"));
//        $token = $query->getResult();
//
//        return  $token;
//
//    }
}
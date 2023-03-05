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

    public function borrarApiKeyUsuario(int $usuarioId):array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM api_key
            WHERE usuario_id = :id 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $usuarioId]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
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

    public function findApiKeyValida($usuario): ?array
    {
        $fechaActual = date("Y-m-d H:i:s");
//        $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $fechaActual);

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select token FROM api_key
            WHERE usuario_id = :id 
            and fecha_expiracion >= :fecha 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $usuario,'fecha' =>$fechaActual]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

}
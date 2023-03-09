<?php

namespace App\Repository;
use App\Entity\PublicacionTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublicacionTags>
 *
 * @method PublicacionTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicacionTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicacionTags[]    findAll()
 * @method PublicacionTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicacionTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicacionTags::class);
    }

    public function borrarPublicacionTags(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM publicaciontags
            WHERE publicacion_id = :id 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

}
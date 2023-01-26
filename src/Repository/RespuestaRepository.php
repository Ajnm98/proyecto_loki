<?php

namespace App\Repository;
use App\Entity\Respuesta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Respuesta>
 *
 * @method Respuesta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Respuesta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Respuesta[]    findAll()
 * @method Respuesta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RespuestaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Respuesta::class);
    }

    public function borrarTodasRespuestasPorPublicacion(int $publicacionID): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM respuesta
            WHERE publicacion_id = :publicacionId 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['publicacionId' => $publicacionID]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
    public function borrarRespuesta(int $respuestaID): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM respuesta
            WHERE id = :id 
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $respuestaID]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function sumarLikeRespuesta(int $respuestaId, int $likes): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            UPDATE respuesta
            SET likes = :likessum
            WHERE id = :publicacionId 
            ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['likessum' => $likes, 'publicacionId' => $respuestaId]);

        return $resultSet->fetchAllAssociative();
    }
}
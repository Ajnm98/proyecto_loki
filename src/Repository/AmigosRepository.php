<?php

namespace App\Repository;

use App\Entity\Amigos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Amigos>
 *
 * @method Amigos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Amigos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Amigos[]    findAll()
 * @method Amigos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AmigosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Amigos::class);
    }
}
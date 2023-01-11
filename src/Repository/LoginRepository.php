<?php

namespace App\Repository;

use App\Entity\Login;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Login>
 *
 * @method Mensaje|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mensaje|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mensaje[]    findAll()
 * @method Mensaje[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mensaje::class);
    }
}
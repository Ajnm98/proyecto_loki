<?php

namespace App\Repository;


use App\Entity\Bloqueados;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bloqueado>
 *
 * @method Bloqueado|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bloqueado|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bloqueado[]    findAll()
 * @method Bloqueado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BloqueadosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bloqueados::class);
    }
}
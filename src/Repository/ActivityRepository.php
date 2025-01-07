<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Activity::class);
    }

    public function findWithDepthLimit(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.children', 'c1')
            ->addSelect('c1')
            ->leftJoin('c1.children', 'c2')
            ->addSelect('c2')
            ->leftJoin('c2.children', 'c3')
            ->addSelect('c3')
            ->getQuery()
            ->getResult();
    }

    public function findByNameWithDepthLimit(string $name): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.children', 'c1')
            ->addSelect('c1')
            ->leftJoin('c1.children', 'c2')
            ->addSelect('c2')
            ->leftJoin('c2.children', 'c3')
            ->addSelect('c3')
            ->where('a.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }
}

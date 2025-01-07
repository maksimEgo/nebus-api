<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Organization::class);
    }

    public function findByBuilding(int $buildingId): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.name', 'o.phoneNumbers')
            ->andWhere('o.building = :building')
            ->setParameter('building', $buildingId)
            ->getQuery()
            ->getArrayResult();
    }

    public function findByActivity(int $activityId, ActivityRepository $activityRepository): array
    {
        $activities = $activityRepository->findWithDepthLimit();

        $activityIds = array_map(
            static fn($activity) => $activity->getId(),
            array_filter($activities,
                fn($activity) => $activity->getId() === $activityId
                    || $this->isChildOf($activity, $activityId)
            )
        );

        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.name', 'o.phoneNumbers')
            ->innerJoin('o.activity', 'a')
            ->where('a.id IN (:activityIds)')
            ->setParameter('activityIds', $activityIds)
            ->getQuery()
            ->getArrayResult();
    }

    public function findByActivityTree(int $activityId, ActivityRepository $activityRepository): array
    {
        $activities = $activityRepository->findWithDepthLimit();

        $activityIds = array_map(
            static fn($activity) => $activity->getId(),
            array_filter($activities,
                fn($activity) => $activity->getId() === $activityId
                    || $this->isChildOf($activity, $activityId)
            )
        );

        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.name', 'o.phoneNumbers', 'a.id AS activityId', 'a.name AS activityName')
            ->innerJoin('o.activity', 'a')
            ->where('a.id IN (:activityIds)')
            ->setParameter('activityIds', $activityIds)
            ->getQuery()
            ->getArrayResult();
    }

    private function isChildOf(Activity $activity, int $parentId): bool
    {
        $parent = $activity->getParent();
        while ($parent) {
            if ($parent->getId() === $parentId) {
                return true;
            }
            $parent = $parent->getParent();
        }
        return false;
    }

    public function findByGeoArea(float $latitude, float $longitude, float $radius): array
    {
        $radiusSquared = $radius * $radius;

        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.name', 'o.phoneNumbers', 'b.id AS buildingId', 'b.address AS buildingAddress')
            ->innerJoin('o.building', 'b')
            ->where('((b.latitude - :latitude) * (b.latitude - :latitude) + (b.longitude - :longitude) * (b.longitude - :longitude)) <= :radiusSquared')
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)
            ->setParameter('radiusSquared', $radiusSquared)
            ->getQuery()
            ->getArrayResult();
    }


    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function getByDate(int $id)
    {
        $query = $this->createQueryBuilder('a');
        if (!empty($id)) {
            $query->andWhere('a.user = :id')
                ->setParameter('id', $id)
                ->orderBy('a.duration','DESC')
            ;

        }
        return $query->getQuery()->getResult();
    }

    public function getByBurnedCalories(int $id)
    {
        $query = $this->createQueryBuilder('a');
        if (!empty($id)) {
            $query->andWhere('a.user = :id')
                ->setParameter('id', $id)
                ->orderBy('a.calories_burned','DESC')
            ;

        }
        return $query->getQuery()->getResult();
    }

    public function getByType(int $id, string $type)
    {
        $query = $this->createQueryBuilder('a');
        if (!empty($id)) {
            $query->andWhere('a.user = :id AND a.type LIKE :type')
                ->setParameter('id', $id)
                ->setParameter('type', '%'.$type.'%')
            ;

        }
        return $query->getQuery()->getResult();
    }
}

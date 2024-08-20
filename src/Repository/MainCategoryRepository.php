<?php

namespace App\Repository;

use App\Entity\MainCategory;
use App\Trait\EntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainCategory>
 */
class MainCategoryRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry, private readonly CategoryRepository $categoryRepository)
    {
        parent::__construct($registry, MainCategory::class);
    }

    use EntityRepositoryTrait;

    //    /**
    //     * @return MainCategory[] Returns an array of MainCategory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MainCategory
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

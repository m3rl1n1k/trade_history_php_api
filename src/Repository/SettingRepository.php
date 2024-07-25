<?php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Setting>
 */
class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, protected UserRepository $userRepository)
    {
        parent::__construct($registry, Setting::class);
    }

    //    /**
    //     * @return Setting[] Returns an array of Setting objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Setting
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function save(?array $setting, int $user_id = null): void
    {
        if ($user_id !== null) {
            $user_id = $this->userRepository->findOneBy(['id' => $user_id]);
        }

        if ($setting === null) {
            $setting = new Setting();
            $setting->setUser($user_id);
            $setting->setSetting(null);
            $this->getEntityManager()->persist($setting);
            $this->getEntityManager()->flush();
        }
    }
}

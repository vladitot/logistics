<?php

namespace App\Repository;

use App\Entity\Courier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\Tests\Fixtures\includes\HotPath\P1;

/**
 * @method Courier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Courier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Courier[]    findAll()
 * @method Courier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourierRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Courier::class);
    }


    /**
     * Получение всех курьеров, кроме перечисленных
     * @param $courierIds
     * @return Courier[]|mixed
     */
    public function getRestOfThis($courierIds)
    {
        if (count($courierIds)>0) {
            return $this->createQueryBuilder('fc')
                ->where('fc.id not in ('.implode(',', $courierIds).')')
                ->getQuery()
                ->getResult();
        } else {
            return $this->findAll();
        }

    }
}

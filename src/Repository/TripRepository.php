<?php

namespace App\Repository;

use App\Entity\Trip;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    private $regionRepository;
    private $courierRepository;

    /**
     * TripRepository constructor.
     * @param RegistryInterface $registry
     * @param RegionRepository $regionRepository
     * @param CourierRepository $courierRepository
     */
    public function __construct(RegistryInterface $registry, RegionRepository $regionRepository, CourierRepository $courierRepository)
    {
        parent::__construct($registry, Trip::class);
        $this->regionRepository = $regionRepository;
        $this->courierRepository = $courierRepository;
    }


    /**
     * Трипы по периоду
     * @param \DateTimeInterface $dateStart
     * @param \DateTimeInterface $dateEnd
     * @param int $page
     * @return mixed
     */
    public function getByPeriod(\DateTimeInterface $dateStart, \DateTimeInterface $dateEnd)
    {
        $result = $this->createQueryBuilder('c')
            ->where('c.date_start >= :start')
            ->andWhere('c.date_start <= :end')
            ->orderBy('c.date_start', 'DESC')
            ->setParameter('start', $dateStart->format('Y-m-d'))
            ->setParameter('end', $dateEnd->format('Y-m-d'))
            ->getQuery()
            ->getResult();
        return $result;
    }

    /**
     *  Получение свободного курьера, готового к поездке в конкретный регион.
     * От региона зависит, потому что для поездки в регион требуется конкретное количество дней.
     * У кого есть в графике такое окно, а у кого то нет.
     * Какое именно окно - зависит от региона.
     * @param \DateTimeInterface $dateStart
     * @param $region_id
     * @return array|null
     */
    public function getFreeCourierByPeriodAndRegion(\DateTimeInterface $dateStart, $region_id)
    {
        $region = $this->regionRepository->find($region_id);
        if ($region) {
            $period = $region->getTripDaysFrom() + $region->getTripDaysTo();
        } else {
            return null;
        }

        $result = $this->getAlreadyWorkingCouriers($dateStart, $period);

        $courierIds = [];
        if ($result) {
            foreach ($result as $trip) {
                /** @var Trip $trip */
                $courierIds[] = $trip->getCourierId()->getId();
            }
        }

        $couriers = $this->courierRepository->getRestOfThis($courierIds);

        $courierIds = [];
        foreach ($couriers as $courier) {
            $courierIds[] = ['id' => $courier->getId(), 'title' => $courier->getTitle()];
        }

        return $courierIds;

    }

    /**
     * Получение курьеров, которые уже заняты работой
     * @param \DateTimeInterface $dateStart
     * @param int $period
     * @return mixed
     */
    private function getAlreadyWorkingCouriers(\DateTimeInterface $dateStart, int $period)
    {
        $query = $this->createQueryBuilder('c');

        $where1 = $query->expr()->andX();
        $where1->addMultiple(['c.date_start >= :dateStart', 'c.date_start < :dateEnd']);

        $where2 = $query->expr()->andX();
        $where2->addMultiple(['c.date_end >= :dateStart', 'c.date_end <= :dateEnd']);

        $where3 = $query->expr()->andX();
        $where3->addMultiple(['c.date_return > :dateStart', 'c.date_return <= :dateEnd']);

        $result = $query
            ->orWhere($where1)
            ->orWhere($where2)
            ->orWhere($where3)
            ->setParameter('dateEnd', Carbon::createFromTimestamp($dateStart->getTimestamp())->addDays($period))
            ->setParameter('dateStart', Carbon::createFromTimestamp($dateStart->getTimestamp()))
            ->getQuery()
            ->getResult();
        return $result;
    }
}

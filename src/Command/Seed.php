<?php
/**
 * Created by PhpStorm.
 * User: vladitot
 * Date: 07.04.19
 * Time: 16:39
 */


namespace App\Command;

use App\Entity\Courier;
use App\Entity\Region;
use App\Entity\Trip;
use App\Repository\CourierRepository;
use App\Repository\RegionRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Seed extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'db:seed';
    private $container;
    private $faker;
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;
    private $courierRepository;
    private $regionRepository;

    public function __construct(ContainerInterface $container, CourierRepository $courierRepository, RegionRepository $regionRepository)
    {
        parent::__construct();
        $this->container = $container;
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->faker = Factory::create();

        $this->courierRepository = $courierRepository;
        $this->regionRepository = $regionRepository;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createFakeCouriers();

        $this->createFakeRegions();

        return $this->fillTrips(
            $this->courierRepository->findAll(),
            $this->regionRepository->findAll()
        );

    }

    private function createFakeCouriers()
    {
        for ($i = 0; $i < 15; $i++) {
            $this->entityManager->persist(
                $this->createCourier()
            );
        }
        $this->entityManager->flush();
    }

    private function createFakeRegions()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->entityManager->persist(
                $this->createRegion()
            );
        }
        $this->entityManager->flush();
    }

    /**
     * @return Courier
     */
    private function createCourier(): Courier
    {
        $courier = new Courier();
        $courier->setTitle($this->faker->name);
        return $courier;
    }

    private function createRegion(): Region
    {
        $region = new Region();
        $region->setTitle($this->faker->city);
        $daysTo = $this->faker->numberBetween(2, 10);

        $region->setTripDaysTo($daysTo);
        $region->setTripDaysFrom($this->faker->numberBetween(1, $daysTo));

        return $region;
    }

    /**
     * @param Courier[] $couriers
     * @param Region[] $regions
     */
    private function fillTrips(array $couriers, array $regions)
    {

        foreach ($couriers as $courier) {
            $carbonCounter = Carbon::createFromDate(2015, 1, 1);
            while ($carbonCounter->lessThan(Carbon::now())) {
                $trip = new Trip();
                $trip->setCourierId($courier);
                $trip->setRegionId($regions[array_rand($regions)]);

                $trip->setDateStart($carbonCounter->toDate());

                $carbonCounter->addDays($trip->getRegionId()->getTripDaysTo());
                $trip->setDateEnd($carbonCounter->toDate());

                $carbonCounter->addDays($trip->getRegionId()->getTripDaysFrom());
                $trip->setDateReturn($carbonCounter->toDate());
                $this->entityManager->persist($trip);
            }
        }

        $this->entityManager->flush();

    }
}
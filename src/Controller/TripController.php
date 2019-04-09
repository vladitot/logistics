<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Repository\CourierRepository;
use App\Repository\RegionRepository;
use App\Repository\TripRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class TripController extends AbstractController
{
    /**
     * Форма вывода по дате
     * @Route("/", name="trip_index", methods={"GET"})
     * @param Request $request
     * @param TripRepository $tripRepository
     * @return Response
     */
    public function index(Request $request, TripRepository $tripRepository): Response
    {
        if ($request->get('date_start')) {
            $dateStart = Carbon::createFromFormat('d.m.Y', $request->get('date_start'));
        } else {
            $dateStart = Carbon::now()->addDays(-10);
        }

        if ($request->get('date_start')) {
            $dateEnd = Carbon::createFromFormat('d.m.Y', $request->get('date_end'));
        } else {
            $dateEnd = Carbon::now();
        }

        return $this->render('trip/index.html.twig', [
            'trips' => $tripRepository->getByPeriod(
                $dateStart,
                $dateEnd,
                ($request->get('page') ?? 1)
            ),
            'dateStart' => $dateStart->format('d.m.Y'),
            'dateEnd' => $dateEnd->format('d.m.Y'),
        ]);
    }

    /**
     * Форма сохранения новой поездки
     * @Route("/new", name="trip_new", methods={"GET","POST"})
     * @param Request $request
     * @param TripRepository $tripRepository
     * @param RegionRepository $regionRepository
     * @param CourierRepository $courierRepository
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, TripRepository $tripRepository, RegionRepository $regionRepository, CourierRepository $courierRepository): Response
    {
        $regionId = $request->get('region_id');
        $courierId = $request->get('courier_id');
        $dateStart = $request->get('date_start');

        $date = Carbon::createFromTimestamp(strtotime($dateStart));

        //Да, я понимаю, что здесь было бы лучше использовать блокировку, чтобы не дать никому занять нашего курьера вместо нас.
        //Но с Доктриной я работаю, можно сказать, впервые, мне здесь тяжеловато в сжатые сроки сделать подобное.
        //поэтому просто проверим, свободен ли он до сих пор.
        $freeCouriers = $tripRepository->getFreeCourierByPeriodAndRegion($date, $regionId);
        $found = false;
        foreach ($freeCouriers as $courier) {
            if ($courier['id'] == $courierId) {
                $found = true;
                break;
            }
        }

        if ($found) {
            $trip = new Trip();
            $trip->setCourierId($courierRepository->find($courierId));

            $region = $regionRepository->find($regionId);

            $trip->setRegionId($region);
            $trip->setDateStart(clone $date);

            $date->addDays($region->getTripDaysTo());
            $trip->setDateEnd(clone $date);

            $date->addDays($region->getTripDaysFrom());
            $trip->setDateReturn($date);

            $this->getDoctrine()->getManager()->persist($trip);
            $this->getDoctrine()->getManager()->flush();

            $response = new Response(json_encode(['status' => 'ok']));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

        } else {
            throw new \Exception("Courier isn't free at these date to drive with your cargo");
        }

    }


    /**
     * Показываем форму добавления
     * @Route("/add", name="trip_add", methods={"GET"})
     * @param Request $request
     * @param RegionRepository $regionRepository
     * @return Response
     */
    public function form(Request $request, RegionRepository $regionRepository): Response
    {
        return $this->render('trip/add-trip.html.twig', [
            'regions' => $regionRepository->findAll()
        ]);
    }


    /**
     * Простой роут загрузки свободных курьеров на текущую дату
     * @Route("/load-couriers", name="load-couriers", methods={"GET"})
     * @param Request $request
     * @param TripRepository $repository
     * @return Response
     */
    public function loadCouriers(Request $request, TripRepository $repository): Response
    {
        $couriers = $repository->getFreeCourierByPeriodAndRegion(Carbon::createFromTimestamp(strtotime($request->get('date_start'))), $request->get('region_id'));

        $response = new Response(json_encode($couriers));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


}

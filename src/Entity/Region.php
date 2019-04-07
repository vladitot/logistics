<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 */
class Region
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $trip_days_to;

    /**
     * @ORM\Column(type="integer")
     */
    private $trip_days_from;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTripDaysTo(): ?int
    {
        return $this->trip_days_to;
    }

    public function setTripDaysTo(int $trip_days_to): self
    {
        $this->trip_days_to = $trip_days_to;

        return $this;
    }

    public function getTripDaysFrom(): ?int
    {
        return $this->trip_days_from;
    }

    public function setTripDaysFrom(int $trip_days_from): self
    {
        $this->trip_days_from = $trip_days_from;

        return $this;
    }
}

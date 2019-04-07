<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TripRepository")
 */
class Trip
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Region")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region_id;

    /**
     * @ORM\Column(type="date")
     */
    private $date_start;

    /**
     * @ORM\Column(type="date")
     */
    private $date_end;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Courier")
     * @ORM\JoinColumn(nullable=false)
     */
    private $courier_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegionId(): ?Region
    {
        return $this->region_id;
    }

    public function setRegionId(?Region $region_id): self
    {
        $this->region_id = $region_id;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }

    public function getCourierId(): ?Courier
    {
        return $this->courier_id;
    }

    public function setCourierId(?Courier $courier_id): self
    {
        $this->courier_id = $courier_id;

        return $this;
    }
}

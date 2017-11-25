<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RaceRun
 *
 * @ORM\Table(name="race_run")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceRunRepository")
 */
class RaceRun
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=255)
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="lng", type="string", length=255)
     */
    private $lng;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var RaceRunner
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RaceRunner", inversedBy="raceRun")
     * @ORM\JoinColumn(name="race_runner_id", referencedColumnName="id")
     */
    private $raceRunner;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lat
     *
     * @param string $lat
     *
     * @return RaceRun
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     *
     * @return RaceRun
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $time
     *
     * @return RaceRun
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return RaceRunner
     */
    public function getRaceRunner()
    {
        return $this->raceRunner;
    }

    /**
     * @param RaceRunner $raceRunner
     */
    public function setRaceRunner($raceRunner)
    {
        $this->raceRunner = $raceRunner;
    }
}


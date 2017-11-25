<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TrackElem
 *
 * @ORM\Table(name="track_elem")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrackElemRepository")
 */
class TrackElem
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
     * @var int
     *
     * @ORM\Column(name="orderNumber", type="integer")
     */
    private $orderNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="partOfRace", type="boolean")
     */
    private $partOfRace;

    /**
     * @var Track
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Track", inversedBy="trackElems")
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id")
     */
    private $track;

    /**
     * @var TrackPoint[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TrackPoint", mappedBy="trackElem", cascade={"persist", "remove"})
     */
    private $points;

    /**
     * TrackElem constructor.
     */
    public function __construct()
    {
        $this->points = new ArrayCollection();
        $this->partOfRace = true;
        $this->orderNumber = 1;
    }

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
     * Set orderNumber
     *
     * @param integer $orderNumber
     *
     * @return TrackElem
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set partOfRace
     *
     * @param boolean $partOfRace
     *
     * @return TrackElem
     */
    public function setPartOfRace($partOfRace)
    {
        $this->partOfRace = $partOfRace;

        return $this;
    }

    /**
     * Get partOfRace
     *
     * @return bool
     */
    public function getPartOfRace()
    {
        return $this->partOfRace;
    }

    /**
     * @return Track
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param Track $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
    }

    /**
     * @return TrackPoint[]|ArrayCollection
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param TrackPoint[]|ArrayCollection $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * @param TrackPoint $trackPoint
     * @return TrackPoint[]|ArrayCollection
     */
    public function addPoint(TrackPoint $trackPoint){
        $this->points->add($trackPoint);
        $trackPoint->setTrackElem($this);
        return $this->points;
    }
}


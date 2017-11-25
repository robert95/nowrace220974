<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Track
 *
 * @ORM\Table(name="track")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrackRepository")
 */
class Track
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Race[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Race", mappedBy="track")
     */
    private $races;

    /**
     * @var TrackElem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TrackElem", mappedBy="track", cascade={"persist", "remove"})
     */
    private $trackElems;

    /**
     * Track constructor.
     */
    public function __construct()
    {
        $this->races = new ArrayCollection();
        $this->trackElems = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Track
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Race[]|ArrayCollection
     */
    public function getRaces()
    {
        return $this->races;
    }

    /**
     * @param Race[]|ArrayCollection $races
     */
    public function setRaces($races)
    {
        $this->races = $races;
    }

    /**
     * @param Race $race
     * @return Race[]|ArrayCollection
     */
    public function addRace(Race $race){
        $this->races->add($race);
        $race->setTrack($this);
        return $this->races;
    }

    /**
     * @return TrackElem[]|ArrayCollection
     */
    public function getTrackElems()
    {
        return $this->trackElems;
    }

    /**
     * @param TrackElem[]|ArrayCollection $trackElems
     */
    public function setTrackElems($trackElems)
    {
        $this->trackElems = $trackElems;
    }

    /**
     * @param TrackElem $trackElem
     * @return TrackElem[]|ArrayCollection
     */
    public function addTrackElems(TrackElem $trackElem){
        $this->trackElems->add($trackElem);
        $trackElem->setTrack($this);
        return $this->trackElems;
    }
}


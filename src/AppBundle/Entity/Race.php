<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Race
 *
 * @ORM\Table(name="race")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceRepository")
 */
class Race
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
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="datetime")
     */
    private $startTime;

    /**
     * @var float
     *
     * @ORM\Column(name="distance", type="float")
     */
    private $distance;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_runners", type="integer")
     */
    private $maxRunners;

    /**
     * @var Contest
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contest", inversedBy="races")
     * @ORM\JoinColumn(name="contest_id", referencedColumnName="id")
     */
    private $contest;

    /**
     * @var RaceCategory[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\RaceCategory", inversedBy="races")
     */
    private $categories;

    /**
     * @var Track
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Track", inversedBy="races", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id")
     *
     */
    private $track;

    /**
     * @var RaceRunner[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RaceRunner", mappedBy="race", cascade={"all"})
     */
    private $raceRunners;

    /**
     * Race constructor.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->raceRunners = new ArrayCollection();
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Race
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set distance
     *
     * @param float $distance
     *
     * @return Race
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @return Contest
     */
    public function getContest()
    {
        return $this->contest;
    }

    /**
     * @param Contest $contest
     */
    public function setContest($contest)
    {
        $this->contest = $contest;
        if(!$this->startTime){
            $this->startTime = $contest->getStartTime();
        }
    }

    /**
     * @return RaceCategory[]|ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param RaceCategory[]|ArrayCollection $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param RaceCategory $raceCategory
     */
    public function addCategories(RaceCategory $raceCategory)
    {
        $this->categories->add($raceCategory);
    }

    /**
     * @return integer
     */
    public function getMaxRunners()
    {
        return $this->maxRunners;
    }

    /**
     * @param integer $maxRunners
     */
    public function setMaxRunners($maxRunners)
    {
        $this->maxRunners = $maxRunners;
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
     * @return RaceRunner[]|ArrayCollection
     */
    public function getRaceRunners()
    {
        return $this->raceRunners;
    }

    /**
     * @param RaceRunner[]|ArrayCollection $raceRunners
     */
    public function setRaceRunners($raceRunners)
    {
        $this->raceRunners = $raceRunners;
    }

    /**
     * @param RaceRunner $raceRunner
     * @return RaceRunner[]|ArrayCollection
     */
    public function addRaceRunners(RaceRunner $raceRunner){
        if(!$this->hasRunner($raceRunner->getRunner())){
            $this->raceRunners->add($raceRunner);
            $raceRunner->setRace($this);
        }
        return $this->raceRunners;
    }

    public function getRaceRunner(Runner $runner){
        foreach ($this->raceRunners as $raceRunner){
            if($raceRunner->getRunner()->getId() == $runner->getId()){
                return $raceRunner;
            }
        }
        return false;
    }

    public function hasRunner(Runner $runner){
        foreach ($this->raceRunners as $raceRunner){
            if($raceRunner->getRunner()->getId() == $runner->getId()){
                return true;
            }
        }
        return false;
    }
    /**
     * @return bool
     */
    public function isLive(){
        return $this->startTime < (new \DateTime())
               && !$this->isEnded();
    }

    /**
     * @return bool
     */
    public function isEnded(){
        if($this->startTime > (new \DateTime())) return false;
        foreach ($this->raceRunners as $runner){
            if(!$runner->getEndTime()){
                return false;
            }
        }
        return true;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime(){
        $endTime = null;
        foreach ($this->raceRunners as $runner){
            if(!$endTime || $runner->getEndTime() > $endTime){
                $endTime = $runner->getEndTime();
            }
        }
        return $endTime;
    }

    /**
     * @return \DateTime
     */
    public function getRealStartTime(){
        $startTime = null;
        $endTime = null;
        foreach ($this->raceRunners as $runner){
            if(!$endTime || $runner->getFirstTime() < $startTime){
                $startTime = $runner->getFirstTime();
            }
        }
        return $startTime;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canSign(User $user){
        return $user->hasRole('ROLE_RUNNER')
                && $this->maxRunners >= count($this->raceRunners->toArray())
                && $this->startTime > (new \DateTime())
                && !$this->hasRunner($user->getRunner());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canSignOut(User $user){
        return $this->isSign($user)
            && $this->startTime > (new \DateTime());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isSign(User $user){
        return $user->hasRole('ROLE_RUNNER')
            && $this->hasRunner($user->getRunner());
    }
}


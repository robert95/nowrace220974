<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * RaceRunner
 *
 * @ORM\Table(name="race_runner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceRunnerRepository")
 */
class RaceRunner
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
     * @ORM\Column(name="startNumber", type="integer")
     */
    private $startNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="finishPlace", type="integer", nullable=true)
     */
    private $finishPlace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var Runner
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Runner", inversedBy="raceRunners")
     * @ORM\JoinColumn(name="runner_id", referencedColumnName="id")
     */
    private $runner;

    /**
     * @var Race
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Race", inversedBy="raceRunners")
     * @ORM\JoinColumn(name="race_id", referencedColumnName="id")
     */
    private $race;

    /**
     * @var RaceRun[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RaceRun", mappedBy="raceRunner", cascade={"persist", "remove"})
     */
    private $raceRun;

    /**
     * @var RaceCategory[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\RaceCategory")
     */
    private $categories;

    /**
     * RaceRunner constructor.
     */
    public function __construct()
    {
        $this->raceRun = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
     * Set startNumber
     *
     * @param integer $startNumber
     *
     * @return RaceRunner
     */
    public function setStartNumber($startNumber)
    {
        $this->startNumber = $startNumber;

        return $this;
    }

    /**
     * Get startNumber
     *
     * @return int
     */
    public function getStartNumber()
    {
        return $this->startNumber;
    }

    /**
     * Set finishPlace
     *
     * @param integer $finishPlace
     *
     * @return RaceRunner
     */
    public function setFinishPlace($finishPlace)
    {
        $this->finishPlace = $finishPlace;

        return $this;
    }

    /**
     * Get finishPlace
     *
     * @return int
     */
    public function getFinishPlace()
    {
        return $this->finishPlace;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return RaceRunner
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
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return RaceRunner
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return string
     */
    public function getPrettyTime(){
       if($this->endTime != null && $this->startTime != null){
           $diffInSeconds = $this->endTime->getTimestamp() - $this->startTime->getTimestamp();
           return sprintf('%02d:%02d:%02d', ($diffInSeconds/3600),($diffInSeconds/60%60), $diffInSeconds%60);
       }
       return '-';
    }

    /**
     * @return Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * @param Runner $runner
     */
    public function setRunner($runner)
    {
        $this->runner = $runner;
    }

    /**
     * @return Race
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * @param Race $race
     */
    public function setRace($race)
    {
        $this->race = $race;
    }

    /**
     * @return RaceRun[]|ArrayCollection
     */
    public function getRaceRun()
    {
        return $this->raceRun;
    }

    /**
     * @param RaceRun[]|ArrayCollection $raceRun
     */
    public function setRaceRun($raceRun)
    {
        $this->raceRun = $raceRun;
    }

    /**
     * @param RaceRun $raceRun
     * @return RaceRun[]|ArrayCollection
     */
    public function addRaceRun(RaceRun $raceRun){
        $this->raceRun->add($raceRun);
        $raceRun->setRaceRunner($this);
        return $this->raceRun;
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
     * @return RaceRun[]|ArrayCollection
     */
    public function addCategories(RaceCategory $raceCategory){
        $this->categories->add($raceCategory);
        return $this->raceRun;
    }

    /**
     * @return \DateTime
     */
    public function getFirstTime(){
        $startTime = null;
        if(count($this->raceRun) > 0 ){
            return $this->raceRun->get(0)->getTime();
        }
        return $startTime;
    }

}


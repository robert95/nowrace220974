<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * RaceCategory
 *
 * @ORM\Table(name="race_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceCategoryRepository")
 */
class RaceCategory
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Race", mappedBy="categories")
     */
    private $races;

    public function __construct() {
        $this->races = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return RaceCategory
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
     * @return Race[]
     */
    public function getRaces()
    {
        return $this->races;
    }

    public function hasRace(Race $race){
        foreach ($this->races as $r){
            if($race->getId() == $r->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * @param Race $race
     */
    public function addRace(Race $race){
        if(!$this->hasRace($race)){
            $this->races->add($race);
        }
    }

    /**
     * @param Race[] $races
     */
    public function setRaces($races)
    {
        $this->races = $races;
    }

    function __toString()
    {
        return $this->name ? $this->name: '';
    }
}


<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanyRepository")
 * @UniqueEntity(fields="name", message="Organizator o takiej naziwe już istnieje")
 */
class Company
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
     * @Assert\NotBlank(message = "Wpisz nazwę")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="nip", type="string", length=255, nullable=true)
     */
    private $nip;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message = "Wpisz miasto")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="postCode", type="string", length=10, nullable=true)
     */
    private $postCode;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=10, nullable=true)
     */
    private $phone;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="company", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Contest[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Contest", mappedBy="company")
     */
    private $contests;

    /**
     * Company constructor.
     */
    public function __construct()
    {
        $this->contests = new ArrayCollection();
        $this->country = 'PL';
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
     * @return Company
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
     * Set nip
     *
     * @param string $nip
     *
     * @return Company
     */
    public function setNip($nip)
    {
        $this->nip = $nip;

        return $this;
    }

    /**
     * Get nip
     *
     * @return string
     */
    public function getNip()
    {
        return $this->nip;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Company
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postCode
     *
     * @param string $postCode
     *
     * @return Company
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get postCode
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return Company
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Company
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * @return Contest[]|ArrayCollection
     */
    public function getContests()
    {
        return $this->contests;
    }

    /**
     * @param Contest[]|ArrayCollection $contests
     */
    public function setContests($contests)
    {
        $this->contests = $contests;
    }

    /**
     * @param Contest $contest
     * @return Contest[]|ArrayCollection
     */
    public function addContests(Contest $contest){
        $this->contests->add($contest);
        return $this->contests;
    }

    public function getFullName(){
        return $this->getName();
    }

    public function getCountOfRaces(){
        $count = 0;
        foreach ($this->contests as $contest){
            $count += count($contest->getRaces());
        }
        return $count;
    }

    public function getCountOfRunners(){
        $count = 0;
        foreach ($this->contests as $contest){
            foreach ($contest->getRaces() as $race){
                $count += count($race->getRaceRunners());
            }
        }
        return $count;
    }

    public function getActiveContests(){
        $contests = [];
        foreach ($this->contests as $contest){
            if($contest->getStartTime() > new \DateTime()){
                $contests[] = $contest;
            }
        }

        return $contests;
    }

    public function __toString()
    {
        return $this->name;
    }
}


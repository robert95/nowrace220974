<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrackPoint
 *
 * @ORM\Table(name="track_point")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrackPointRepository")
 */
class TrackPoint
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var TrackElem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TrackElem", inversedBy="points")
     * @ORM\JoinColumn(name="track_elem_id", referencedColumnName="id")
     *
     */
    private $trackElem;

    /**
     * TrackPoint constructor.
     * @param int $orderNumber
     * @param string $lat
     * @param string $lng
     */
    public function __construct($orderNumber, $lat, $lng)
    {
        $this->orderNumber = $orderNumber;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->type = "PART_OF_TRACK";
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
     * @return TrackPoint
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
     * Set lat
     *
     * @param string $lat
     *
     * @return TrackPoint
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
     * @return TrackPoint
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
     * Set type
     *
     * @param string $type
     *
     * @return TrackPoint
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return TrackElem
     */
    public function getTrackElem()
    {
        return $this->trackElem;
    }

    /**
     * @param TrackElem $trackElem
     */
    public function setTrackElem($trackElem)
    {
        $this->trackElem = $trackElem;
    }
}


<?php
namespace Hack2Hire\FraudDetectionBackend\Entities;

use Doctrine\ORM\Mapping;

/**
 * ZipCode
 *
 * @Table(name="zip_codes")
 * @Entity
 */
class ZipCode
{
    /**
     * @var integer $zip
     *
     * @Column(name="zip", type="integer", nullable=false)
     * @Id
     */
    protected $zip;

    /**
     * @var string $latitude
     *
     * @Column(name="latitude", type="string", nullable=true)
     */
    protected $latitude;

    /**
     * @var string $longitude
     *
     * @Column(name="longitude", type="string", nullable=true)
     */
    protected $longitude;

    /**
     * @var string $city
     *
     * @Column(name="city", type="string", nullable=true)
     */
    protected $city;

    /**
     * @var string $state
     *
     * @Column(name="state", type="string", nullable=true)
     */
    protected $state;

    /**
     * @var string $county
     *
     * @Column(name="county", type="string", nullable=true)
     */
    protected $county;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", nullable=true)
     */
    protected $name;

    public function __construct($zip, $latitude, $longitude, $city, $state, $county, $name)
    {
        $this->zip = $zip;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->city = $city;
        $this->state = $state;
        $this->county = $county;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param int $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * @param string $county
     */
    public function setCounty($county)
    {
        $this->county = $county;
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
}

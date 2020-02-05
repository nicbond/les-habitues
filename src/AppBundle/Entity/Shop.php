<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
class Shop
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $nameShop;

    /**
     * @ORM\Column(type="text")
     */
    private $adress;
	
	/**
     * @ORM\Column(type="text")
     */
    private $zipCode;
	
	/**
     * @ORM\Column(type="text")
     */
    private $city;
	
	/**
     * @ORM\Column(type="text")
     */
    private $image;
	
	/**
     * @var decimal
     *
     * @ORM\Column(name="offer", type="float")
     */
    private $offer;
	
	/**
     * @ORM\Column(type="integer", options={"unsigned":true, "default":0})
     */
    private $id_shop;
	
	/**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

	public function setNameShop($nameShop)
    {
        $this->nameShop = $nameShop;

        return $this;
    }

    public function getNameShop()
    {
        return $this->nameShop;
    }
	
	public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }

    public function getAdress()
    {
        return $this->adress;
    }
	
	public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

	public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

	public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }
	
	public function setOffer($offer)
    {
        $this->offer = $offer;

        return $this;
    }

    public function getOffer()
    {
        return $this->offer;
    }
	
	public function __construct()
    {
        $this->id_shop = 0;
    }
	
	public function setIdShop($id_shop)
    {
        $this->id_shop = $id_shop;

        return $this;
    }

    public function getIdShop()
    {
        return $this->id_shop;
    }
}
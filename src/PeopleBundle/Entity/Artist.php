<?php

namespace PeopleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Artist
 *
 * @ORM\Table(name="artist")
 * @ORM\Entity(repositoryClass="PeopleBundle\Repository\ArtistRepository")
 */
class Artist
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
     * Set name
     *
     * @param string $name
     *
     * @return People
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
     * @ORM\OneToMany(targetEntity="Network",mappedBy ="artist")
     * @var
     */
    private $networks;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add network
     *
     * @param \PeopleBundle\Entity\Network $network
     *
     * @return Artist
     */
    public function addNetwork(\PeopleBundle\Entity\Network $network)
    {
        $this->networks[] = $network;

        return $this;
    }

    /**
     * Remove network
     *
     * @param \PeopleBundle\Entity\Network $network
     */
    public function removeNetwork(\PeopleBundle\Entity\Network $network)
    {
        $this->networks->removeElement($network);
    }

    /**
     * Get networks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNetworks()
    {
        return $this->networks;
    }

    public function __toString()
    {
        return $this->getName();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->networks = new \Doctrine\Common\Collections\ArrayCollection();
    }

}

<?php

namespace PeopleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * People
 * @ORM\MappedSuperClass()
 */
class People
{

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
     * Constructor
     */
    public function __construct()
    {
        $this->networks = new \Doctrine\Common\Collections\ArrayCollection();
    }

}

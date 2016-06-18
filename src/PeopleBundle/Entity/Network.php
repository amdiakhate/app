<?php

namespace PeopleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Network
 *
 * @ORM\Table(name="network")
 * @ORM\Entity(repositoryClass="PeopleBundle\Repository\NetworkRepository")
 */
class Network
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity = "Artist", inversedBy = "networks" ,cascade={"persist", "remove", "merge"})
     */
    private $artist;

    /**
     * @ORM\OneToMany(targetEntity="TweetsBundle\Entity\Tweet", mappedBy="network", cascade={"remove"})
     * @var
     */
    private $tweets;
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
     * Set url
     *
     * @param string $url
     *
     * @return Network
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Network
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
     * Set artist
     *
     * @param \PeopleBundle\Entity\Artist $artist
     *
     * @return Network
     */
    public function setArtist(\PeopleBundle\Entity\Artist $artist = null)
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return \PeopleBundle\Entity\Artist
     */
    public function getArtist()
    {
        return $this->artist;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tweets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tweet
     *
     * @param \TweetsBundle\Entity\Tweet $tweet
     *
     * @return Network
     */
    public function addTweet(\TweetsBundle\Entity\Tweet $tweet)
    {
        $this->tweets[] = $tweet;

        return $this;
    }

    /**
     * Remove tweet
     *
     * @param \TweetsBundle\Entity\Tweet $tweet
     */
    public function removeTweet(\TweetsBundle\Entity\Tweet $tweet)
    {
        $this->tweets->removeElement($tweet);
    }

    /**
     * Get tweets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTweets()
    {
        return $this->tweets;
    }
}

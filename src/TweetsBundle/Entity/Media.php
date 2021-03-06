<?php

namespace TweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Media
 */
class Media
{
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var string
     */
    private $media_url_https;
    
    /**
     * @var string
     */
    private $url;
    
    /**
     * @var string
     */
    private $display_url;
    
    /**
     * @var string
     */
    private $expanded_url;
        
    /**
     * @var ArrayCollection
     */
    private $tweets;
    
    public function __construct($id = null)
    {
        if (! is_null($id)) {
            $this->setId($id);
        }
        
        $this->tweets = new ArrayCollection();
    }
    
    /**
     * Set id
     *
     * @param int $id
     * @return Media 
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
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
     * Set media_url_https
     *
     * @param string $mediaUrlHttps
     * @return Media
     */
    public function setMediaUrlHttps($mediaUrlHttps)
    {
        $this->media_url_https = $mediaUrlHttps;

        return $this;
    }
    
    /**
     * Get media_url_https
     *
     * @return string 
     */
    public function getMediaUrlHttps()
    {
        return $this->media_url_https;
    }
    
    /**
     * Set url
     *
     * @param string $url
     * @return Media
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
     * Set display_url
     *
     * @param string $displayUrl
     * @return Media
     */
    public function setDisplayUrl($displayUrl)
    {
        $this->display_url = $displayUrl;

        return $this;
    }
    
    /**
     * Get display_url
     *
     * @return string 
     */
    public function getDisplayUrl()
    {
        return $this->display_url;
    }
    
    /**
     * Set expanded_url
     *
     * @param string $expandedUrl
     * @return Media
     */
    public function setExpandedUrl($expandedUrl)
    {
        $this->expanded_url = $expandedUrl;

        return $this;
    }
    
    /**
     * Get expanded_url
     *
     * @return string 
     */
    public function getExpandedUrl()
    {
        return $this->expanded_url;
    }
    
    /**
     * Add a tweet
     *
     * @param Tweet $tweet
     *
     * @return Media
     */
    public function addTweet(Tweet $tweet)
    {
        $this->tweets->add($tweet);
        
        return $this;
    }
    
    /**
     * Remove a tweet
     *
     * @param Tweet $tweet
     *
     * @return Media
     */
    public function removeTweet(Tweet $tweet)
    {
        $this->tweets->removeElement($tweet);
        
        return $this;
    }
    
    /**
     * Get tweets
     *
     * @return ArrayCollection
     */
    public function getTweets()
    {
        return $this->tweets;
    }
    
    /**
     * Call setter functions
     * 
     * @param \stdClass $mediaTmp
     *
     * @return Media
     */
    public function setValues(\stdClass $mediaTmp)
    {
        $this
            ->setMediaUrlHttps($mediaTmp->media_url_https)
            ->setUrl($mediaTmp->url)
            ->setDisplayUrl($mediaTmp->display_url)
            ->setExpandedUrl($mediaTmp->expanded_url)
        ;
        
        return $this;
    }
}

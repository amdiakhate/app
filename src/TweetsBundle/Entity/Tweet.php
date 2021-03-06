<?php

namespace TweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tweet
 */
class Tweet
{
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var \DateTime
     */
    private $created_at;
    
    /**
     * @var string
     */
    private $text;
    
    /**
     * @var integer
     */
    private $retweet_count = 0;
    
    /**
     * @var integer
     */
    private $favorite_count = 0;
    
    /**
     * @var User
     */
    private $user;
    
    /**
     * In timeline: false for retweeted Tweets
     */
    private $in_timeline = false;
    
    /**
     * @var Tweet
     */
    private $retweeted_status = null;
    
    /**
     * @var ArrayCollection
     */
    private $retweeting_statuses;
    
    /**
     * @var ArrayCollection
     */
    private $medias;
    
    public function __construct($id = null)
    {
        if (! is_null($id)) {
            $this->setId($id);
        }
        
        $this->medias = new ArrayCollection();
        $this->retweeting_statuses = new ArrayCollection();
    }
    
    /**
     * Set id
     *
     * @param bigint $id
     * @return Tweet 
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Tweet
     */
    public function setCreatedAt(\Datetime $createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }
    
    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * Set text
     *
     * @param string $text
     * @return Tweet
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }
    
    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
    
    public function getTextLinkified()
    {
        /** @see http://stackoverflow.com/questions/507436/how-do-i-linkify-urls-in-a-string-with-php/507459#507459 */
        return preg_replace(
            "~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~",
            "<a href=\"\\0\">\\0</a>", 
            $this->getText()
        );
    }
    
    /**
     * Set retweet_count
     *
     * @param integer $retweetCount
     * @return Tweet
     */
    public function setRetweetCount($retweetCount)
    {
        $this->retweet_count = $retweetCount;

        return $this;
    }
    
    /**
     * Get retweet_count
     *
     * @return integer 
     */
    public function getRetweetCount()
    {
        return $this->retweet_count;
    }
    
    /**
     * Set favorite_count
     *
     * @param integer $favoriteCount
     * @return Tweet
     */
    public function setFavoriteCount($favoriteCount)
    {
        $this->favorite_count = $favoriteCount;

        return $this;
    }
    
    /**
     * Get favorite_count
     *
     * @return integer
     */
    public function getFavoriteCount()
    {
        return $this->favorite_count;
    }
    
    /**
     * Set user
     *
     * @param User $user
     * @return Tweet
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->user->addTweet($this);

        return $this;
    }
    
    /**
     * Get User
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set in timeline
     *
     * @param boolean $inTimeline
     * @return Tweet
     */
    public function setInTimeline($inTimeline)
    {
        $this->in_timeline = $inTimeline;
        
        return $this;
    }
    
    /**
     * Get in timeline
     *
     * @return boolean
     */
    public function isInTimeline()
    {
        return $this->in_timeline;
    }
    
    /**
     * Set retweeted
     * "This attribute contains a representation of the original Tweet
     *  that was retweeted."
     *
     * @param Tweet $retweetedStatus
     * @return Tweet
     */
    public function setRetweetedStatus(Tweet $retweetedStatus)
    {
        $this->retweeted_status = $retweetedStatus;
        
        return $this;
    }
    
    /**
     * Get retweeted status
     *
     * @return Tweet
     */
    public function getRetweetedStatus()
    {
        return $this->retweeted_status;
    }
    
    /**
     * Get medias
     *
     * @return ArrayCollection 
     */
    public function getMedias()
    {
        return $this->medias;
    }
    
    /**
     * Add a media
     *
     * @param Media $media
     *
     * @return Tweet
     */
    public function addMedia(Media $media)
    {
        $this->medias->add($media);
        $media->addTweet($this);
        
        return $this;
    }
    
    /**
     * Remove a media
     *
     * @param Media $media
     *
     * @return Tweet
     */
    public function removeMedia(Media $media)
    {
        $this->medias->removeElement($media);
        $media->removeTweet($this);
        
        return $this;
    }
    
    /**
     * Get retweeting status
     *
     * @return ArrayCollection 
     */
    public function getRetweetingStatuses()
    {
        return $this->retweeting_statuses;
    }
    
    /**
     * Call setter functions
     * 
     * @param \stdClass $tweetTmp
     *
     * @return Tweet
     */
    public function setValues(\stdClass $tweetTmp)
    {
        $this
            ->setCreatedAt(new \Datetime($tweetTmp->created_at))
            ->setText($tweetTmp->text)
            ->setRetweetCount($tweetTmp->retweet_count)
            ->setFavoriteCount($tweetTmp->favorite_count)
        ;
        
        return $this;
    }
    
    /**
     * Check that tweet can be deleted
     * 
     * @param integer $tweetId
     * 
     * @return boolean
     */
    public function mustBeKept($tweetId)
    {
        if (count($this->getRetweetingStatuses()) == 0) {
            // This tweet has not been retweeted
            return(false);
        }
        
        // Check that this tweet has not been retweeted after $tweetId
        foreach ($this->getRetweetingStatuses() as $retweeting_status) {
            // This tweet is retweeted in the timeline, keep it
            if ($retweeting_status->getId() >= $tweetId) {
                return(true);
            }
        }
        
        return(false);
    }
    /**
     * @var \PeopleBundle\Entity\Network
     */
    private $network;


    /**
     * Get inTimeline
     *
     * @return boolean
     */
    public function getInTimeline()
    {
        return $this->in_timeline;
    }

    /**
     * Add retweetingStatus
     *
     * @param \TweetsBundle\Entity\Tweet $retweetingStatus
     *
     * @return Tweet
     */
    public function addRetweetingStatus(\TweetsBundle\Entity\Tweet $retweetingStatus)
    {
        $this->retweeting_statuses[] = $retweetingStatus;

        return $this;
    }

    /**
     * Remove retweetingStatus
     *
     * @param \TweetsBundle\Entity\Tweet $retweetingStatus
     */
    public function removeRetweetingStatus(\TweetsBundle\Entity\Tweet $retweetingStatus)
    {
        $this->retweeting_statuses->removeElement($retweetingStatus);
    }

    /**
     * Set network
     *
     * @param \PeopleBundle\Entity\Network $network
     *
     * @return Tweet
     */
    public function setNetwork(\PeopleBundle\Entity\Network $network = null)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Get network
     *
     * @return \PeopleBundle\Entity\Network
     */
    public function getNetwork()
    {
        return $this->network;
    }
}

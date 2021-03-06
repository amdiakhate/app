<?php

namespace TweetsBundle\Utils;

use TweetsBundle\Entity\User;
use TweetsBundle\Entity\Tweet;
use TweetsBundle\Entity\Media;

class PersistTweet
{
    private $em;
    private $displayTable;
    private $table;
    private $network;
    
    public function __construct($em, $displayTable, $table, $network)
    {
        $this->em = $em;
        $this->displayTable = $displayTable;
        $this->table = $table;
        $this->network = $network;
    }
    
    /**
     * @param \stdClass $userTmp
     * 
     * @return User
     */
    protected function persistUser(\stdClass $userTmp)
    {
        $user = $this->em
            ->getRepository('TweetsBundle:User')
            ->findOneById($userTmp->id)
        ;
        
        if (! $user)
        {
            # Only set the id when adding the User
            $user = new User($userTmp->id);
        }
        
        # Update other fields
        $user->setValues($userTmp);
        
        $this->em->persist($user);
        
        return $user;
    }
    
    /**
     * @param array $medias
     * @param Tweet $tweet
     */
    public function iterateMedias($medias, Tweet $tweet)
    {
        foreach ($medias as $mediaTmp)
        {
            if ($mediaTmp->type == 'photo')
            {
                $this->persistMedia($tweet, $mediaTmp);
            }
        }
    }
    
    /**
     * @param \stdClass $tweetTmp
     * @param Tweet $tweet
     */
    protected function addMedias(\stdClass $tweetTmp, Tweet $tweet)
    {
        if ((isset($tweetTmp->entities))
            && (isset($tweetTmp->entities->media)))
        {
            $this->iterateMedias($tweetTmp->entities->media, $tweet);
        }
    }
    
    /**
     * Create a Tweet object and return it
     * 
     * @param \stdClass $tweetTmp
     * @param User $user
     * @param boolean $inTimeline
     * 
     * @return Tweet
     */
    protected function createTweet(\stdClass $tweetTmp, $user, $inTimeline)
    {
        $tweet = new Tweet($tweetTmp->id);
        
        $tweet
            ->setValues($tweetTmp)
            ->setUser($user)
            ->setNetwork($this->network)
            ->setInTimeline($inTimeline)
        ;
        
        $this->addMedias($tweetTmp, $tweet);
        
        return $tweet;
    }
    
    /**
     * @param \stdClass $tweetTmp
     * @param User $user
     * @param boolean $inTimeline
     * 
     * @return Tweet
     */
    protected function persistTweet(\stdClass $tweetTmp, User $user,
        $inTimeline)
    {
        $tweet = $this->em
            ->getRepository('TweetsBundle:Tweet')
            ->findOneById($tweetTmp->id)
        ;
        
        if (! $tweet) {
            $tweet = $this->createTweet($tweetTmp, $user, $inTimeline);
        }
        
        if (isset($tweetTmp->retweeted_status)) {
            $retweet = $this->persistRetweetedTweet($tweetTmp);
            $tweet->setRetweetedStatus($retweet);
        }
        
        $this->em->persist($tweet);
        $this->em->flush();
        
        return $tweet;
    }
    
    /**
     * @param \stdClass $tweetTmp
     * 
     * @return Tweet
     */
    protected function persistRetweetedTweet(\stdClass $tweetTmp)
    {
        $retweet = $this->em
            ->getRepository('TweetsBundle:Tweet')
            ->findOneById($tweetTmp->retweeted_status->id)
        ;
        
        if (! $retweet) {
            $retweet = $this->addTweet(
                $tweetTmp->retweeted_status
            );
        }
        
        return($retweet);
    }
    
    /**
     * @param Tweet $tweet
     * @param \stdClass $mediaTmp
     */
    protected function persistMedia(Tweet $tweet, \stdClass $mediaTmp)
    {
        $media = $this->em
            ->getRepository('TweetsBundle:Media')
            ->findOneById($mediaTmp->id)
        ;
        
        if (! $media) {
            # Only set the id and values when adding the Media
            $media = new Media($mediaTmp->id);
            $media->setValues($mediaTmp);
            $this->em->persist($media);
            $this->em->flush();
        }
        
        $tweet->addMedia($media);
    }
    
    /**
     * @param \stdClass $tweetTmp
     * @param boolean $inTimeline
     * 
     * @return Tweet
     */
    public function addTweet(\stdClass $tweetTmp, $inTimeline = false) {
        $user = $this->persistUser($tweetTmp->user);
        
        $tweet = $this->persistTweet($tweetTmp, $user, $inTimeline);
        
        // Ignore retweeted tweets
        if ($this->displayTable && $inTimeline) {
            $this->table->addRow(array(
                $tweet->getCreatedAt()->format('Y-m-d H:i:s'),
                mb_substr($tweet->getText(), 0, 40),
                $user->getName()
            ));
        }
        
        return $tweet;
    }
}

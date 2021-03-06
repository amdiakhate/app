<?php

namespace TweetsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use TweetsBundle\Entity\Tweet;

class LoadTweetPagesData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $tweetId = 1;
        
        // Add retweeted Tweet
        $retweeted = new Tweet($tweetId);
        $retweeted
            ->setUser($this->getReference('user-githubeng'))
            ->setCreatedAt(new \Datetime(
                '2015-02-10 21:18:'.sprintf('%02d', $tweetId)))
            ->setText($tweetId)
            ->setRetweetCount($tweetId)
            ->setFavoriteCount($tweetId)
        ;
        
        $manager->persist($retweeted);
        
        // not retweet tweets
        foreach (range(2, 40) as $tweetId)
        {
            $tweet = new Tweet($tweetId);
            $tweet
                ->setUser($this->getReference('user'))
                ->setCreatedAt(
                    new \Datetime('2015-02-10 21:19:'.sprintf('%02d', $tweetId)))
                ->setText($tweetId)
                ->setRetweetCount($tweetId)
                ->setFavoriteCount($tweetId)
                ->setInTimeline(true)
            ;
            
            $manager->persist($tweet);
        }
        
        $manager->flush();
        
        # Attach the same Media to 2 Tweet
        $tweet = $manager
            ->getRepository('TweetsBundle:Tweet')
            ->find(5);
        $tweet->addMedia($this->getReference('media-1'));
        
        $manager->persist($tweet);
        
        $tweet = $manager
            ->getRepository('TweetsBundle:Tweet')
            ->find(25);
        $tweet->addMedia($this->getReference('media-2'));
        
        $manager->persist($tweet);
        
        $manager->flush();
        
        
        // Set tweet as retweet
        $retweet = $manager
            ->getRepository('TweetsBundle:Tweet')
            ->find(15);
        
        $retweet->setRetweetedStatus($retweeted);
        
        $manager->persist($tweet);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return array(
            'TweetsBundle\DataFixtures\ORM\LoadMediaData',
        );
    }
}

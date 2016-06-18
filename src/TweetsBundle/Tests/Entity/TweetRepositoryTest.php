<?php

namespace TweetsBundle\Tests\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use TweetsBundle\Entity\Tweet;

class TweetRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public function testTweetRepository()
    {
        $this->loadFixtures(array(
            'TweetsBundle\DataFixtures\ORM\LoadTweetData',
        ));
        
        $tweets = $this->em
            ->getRepository('TweetsBundle:Tweet')
            ->getWithUsers(1)
        ;

        $this->assertCount(3, $tweets);
        
        $tweets = $this->em
            ->getRepository('TweetsBundle:Tweet')
            ->getWithUsersAndMedias(null)
        ;

        $this->assertCount(3, $tweets);
        
        $tweets = $this->em
            ->getRepository('TweetsBundle:Tweet')
            ->getWithUsersAndMedias(null)
        ;

        $this->assertCount(3, $tweets);
        
        $tweets = $this->em
            ->getRepository('TweetsBundle:Tweet')
            ->countPendingTweets(49664)
        ;
        
        $this->assertEquals(3, $tweets);
    }
}

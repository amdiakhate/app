<?php

namespace TweetsBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;

use TweetsBundle\Command\StatusesHomeTimelineTestCommand;

class StatusesHomeTimelineTest extends StatusesBase
{
    /** @var CommandTester $commandTester */
    public $commandTester;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->application->add(new StatusesHomeTimelineTestCommand());

        $command = $this->application->find('statuses:hometimelinetest');
        $this->commandTester = new CommandTester($command);
    }
    
    public function testStatusesHomeTimeline()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            'test' => null
        ));
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('[code] => 215', $display);
        $this->assertContains('[message] => Bad Authentication data.', $display);
    }
    
    public function testStatusesHomeTimelineEmpty()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            'test' => 'json'
        ));
        
        $this->assertContains('Number of tweets: 4', $this->commandTester->getDisplay());
    }
    
    public function testStatusesHomeTimelineNotArray()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            'test' => 'not_array'
        ));
        
        $this->assertContains('Something went wrong, $content is not an array.', $this->commandTester->getDisplay());
    }
    
    public function testStatusesHomeTimelineEmptyArray()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            'test' => 'empty_array'
        ));
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('No new tweet.', $display);
    }
    
    public function testStatusesHomeTimelineWithTweets()
    {
        $this->loadFixtures(array());
        
        // Disable decoration for tests on Windows
        $options = array();
        
        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }
        
        $this->commandTester->execute(
            array(
                'test' => 'json',
                '--table' => true,
            ),
            $options
        );
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('Number of tweets: 4', $display);
        
        # Test the headers of the table
        $this->assertContains(
            '| Datetime            | '.
                'Text excerpt                        | '.
                'Name                |',
            $display
        );
        
        # Test the lines of the table
        $this->assertContains(
            '| 2015-02-10 21:18:00 | '.
                'Bonjour Twitter ! #monpremierTweet  | '.
                'Asynchronous tweets |',
            $display
        );
        $this->assertContains(
            '| 2015-02-10 21:19:20 | '.
                'Hello Twitter! #myfirstTweet        | '.
                'Asynchronous tweets |',
            $display
        );
        $this->assertContains(
            '| 2015-02-18 00:01:14 | '.
                '#image #test http://t.co/rX1oieH1ug | '.
                'Asynchronous tweets |',
            $display
        );
        
        // Test the retweet
        $this->assertContains(
            '| 2015-03-03 21:18:00 | '.
                'RT This is a retweet.               | '.
                'Asynchronous tweets |',
            $display
        );
        
        // Fetch tweet from database
        $em = $this
            ->getContainer()->get('doctrine.orm.entity_manager');
        
        $tweets = $em
            ->getRepository('TweetsBundle:Tweet')
            ->findAll();
        
        $this->assertEquals(
            5,
            count($tweets)
        );
    }
    
    public function testStatusesHomeTimelineWithTweetAndRetweet()
    {
        $this->loadFixtures(array());
        
        // Disable decoration for tests on Windows
        $options = array();
        
        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }
        
        $this->commandTester->execute(
            array(
                'test' => 'json_with_retweet',
                '--table' => true,
            ),
            $options
        );
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('Number of tweets: 1', $display);
        
        // Test the headers of the table
        $this->assertContains(
            '| Datetime            | '.
                'Text excerpt                             | '.
                'Name                |',
            $display
        );
        
        // Test the retweet
        $this->assertContains(
            '| 2015-08-22 20:20:27 | '.
                'RT @travisci: Good morning! We shipped o | '.
                'Asynchronous tweets |',
            $display
        );
        
        // Fetch tweet from database
        $em = $this
            ->getContainer()->get('doctrine.orm.entity_manager');

        $tweet = $em
            ->getRepository('TweetsBundle:Tweet')
            ->findOneBy(array(
                'id' => 999080449,
            ));
        
        $this->assertEquals(
            999080449,
            $tweet->getId()
        );
        
        // Image URL was stored with HTTPS
        $this->assertSame(
            'https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png',
            $tweet->getUser()->getProfileImageUrlHttpOrHttps()
        );
        
        // Image URL with HTTP was not stored
        $this->assertSame(
            'https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png',
            $tweet->getUser()->getProfileImageUrlHttps()
        );
        
        $this->assertNull(
            $tweet->getUser()->getProfileImageUrl()
        );
        
        // The number of retweet is the same for both tweets
        $this->assertEquals(
            89,
            $tweet->getRetweetCount()
        );
        
        $this->assertEquals(
            42,
            $tweet->getFavoriteCount()
        );
        
        $retweet = $tweet->getRetweetedStatus();
        
        $this->assertEquals(
            79172609,
            $retweet->getId()
        );
        
        // The number of retweet is the same for both tweets
        $this->assertEquals(
            89,
            $retweet->getRetweetCount()
        );
        
        $this->assertEquals(
            61,
            $retweet->getFavoriteCount()
        );
        
        $tweets = $em
            ->getRepository('TweetsBundle:Tweet')
            ->findAll();
        
        $this->assertEquals(
            2,
            count($tweets)
        );
    }
    
    public function testStatusesHomeTimelineWithSinceIdParameter()
    {
        $this->loadFixtures(array(
            'TweetsBundle\DataFixtures\ORM\LoadTweetData',
        ));
        
        // Disable decoration for tests on Windows
        $options = array();
        
        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }
        
        $this->commandTester->execute(
            array('test' => 'empty_array'),
            $options
        );
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains(
            'last tweet = 1005868490',
            $display
        );
    }
}

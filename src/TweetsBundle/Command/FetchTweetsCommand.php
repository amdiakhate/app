<?php
/**
 * Created by PhpStorm.
 * User: maxta
 * Date: 17/06/2016
 * Time: 16:58
 */


namespace TweetsBundle\Command;

use PeopleBundle\Entity\Network;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\Console\Helper\Table;
use TweetsBundle\Utils\PersistTweet;

class FetchTweetsCommand extends BaseCommand
{
    private $displayTable;
    private $table;
    private $progress;

    /** @see https://dev.twitter.com/rest/reference/get/statuses/home_timeline */
    private $parameters = null;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('statuses:gettweets')
            ->setDescription('Fetch home timeline')
            # http://symfony.com/doc/2.3/cookbook/console/console_command.html#automatically-registering-commands
            ->addOption('table', null, InputOption::VALUE_NONE,
                'Display a table with tweets');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $networks = $this->em->getRepository('PeopleBundle:Network')
            ->findBy(array('type' => 'twitter'));
        foreach ($networks as $network) {
            $this->parameters = array(
                'count' => 5,
                'screen_name' => $network->getUrl(),
                'exclude_replies' => true
            );
            $this->setAndDisplayLastTweet($output,$network);

            $content = $this->getContent($input);

            if (!is_array($content)) {
                $this->displayContentNotArrayError($output, $content);
                continue;
            }

            $numberOfTweets = count($content);

            if ($numberOfTweets == 0) {
                $output->writeln('<comment>No new tweet.</comment>');
                continue;
            }

            $this->addAndDisplayTweets($input, $output, $content, $numberOfTweets, $network);
        }

    }

    /**
     * @param OutputInterface $output
     */
    protected function setAndDisplayLastTweet(OutputInterface $output, Network $network)
    {
        # Get the last tweet
        $lastTweet = $this->em
            ->getRepository('TweetsBundle:Tweet')
            ->getLastTweet($network);

        # And use it in the request if it exists
        if ($lastTweet) {
            $this->parameters['since_id'] = $lastTweet->getId();

            $comment = 'last tweet = ' . $this->parameters['since_id'];
        } else {
            $comment = 'no last tweet';
        }

        $output->writeln('<comment>' . $comment . '</comment>');
    }

    /**
     * @param InputInterface $input
     */
    protected function getContent(InputInterface $input)
    {
        $connection = new TwitterOAuth(
            $this->container->getParameter('twitter_consumer_key'),
            $this->container->getParameter('twitter_consumer_secret'),
            $this->container->getParameter('twitter_token'),
            $this->container->getParameter('twitter_token_secret')
        );

        return ($connection->get(
            'statuses/user_timeline',
            $this->parameters
        ));
    }

    /**
     * @param OutputInterface $output
     * @param null|object $content
     */
    protected function displayContentNotArrayError(OutputInterface $output,
                                                   $content)
    {
        $formatter = $this->getHelper('formatter');

        $errorMessages = array('Error!', 'Something went wrong, $content is not an array.');
        $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        $output->writeln($formattedBlock);
        $output->writeln(print_r($content, true));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $content
     * @param integer $numberOfTweets
     */
    protected function addAndDisplayTweets(InputInterface $input,
                                           OutputInterface $output, $content, $numberOfTweets, Network $network)
    {
        $output->writeln('<comment>Number of tweets: ' . $numberOfTweets . '</comment>');

        # Iterate through $content in order to add the oldest tweet first,
        #  if there is an error the oldest tweet will still be saved
        #  and newer tweets can be saved next time the command is launched
        $tweets = array_reverse($content);

        $this->setProgressBar($output, $numberOfTweets);
        $this->setTable($input, $output);
        $this->iterateTweets($tweets, $network);

        $this->progress->finish();
        $output->writeln('');

        if ($this->displayTable) {
            $this->table->render();
        }
    }

    /**
     * @param OutputInterface $output
     * @param integer $numberOfTweets
     */
    protected function setProgressBar(OutputInterface $output,
                                      $numberOfTweets)
    {
        $this->progress = new ProgressBar($output, $numberOfTweets);
        $this->progress->setBarCharacter('<comment>=</comment>');
        $this->progress->start();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function setTable(InputInterface $input,
                                OutputInterface $output)
    {
        $this->displayTable = $input->getOption('table');

        # Display
        if ($this->displayTable) {
            $this->table = new Table($output);
            $this->table
                ->setHeaders(array('Datetime', 'Text excerpt', 'Name'));
        }
    }

    /**
     * @param array $tweets
     */
    protected function iterateTweets($tweets, Network $network)
    {
        $persistTweet = new PersistTweet($this->em, $this->displayTable,
            $this->table, $network);

        foreach ($tweets as $tweetTmp) {
            $persistTweet->addTweet($tweetTmp, true);

            $this->progress->advance();
        }
    }
}

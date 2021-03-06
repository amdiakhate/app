# TweetsBundle

A Symfony bundle providing a Twitter reader for asynchronous reading

[Packagist ![Latest Stable Version][Packagist Stable Image] ![Latest Unstable Version][Packagist Unstable Image]][Packagist]

[![Build status][Travis Master image]][Travis Master]
[![Scrutinizer Code Quality][Scrutinizer image]
![Scrutinizer][Scrutinizer Coverage Image]][Scrutinizer]
[![Code Climate][Code Climate image]][Code Climate]
[![Coveralls][Coveralls image]][Coveralls]
[![AppVeyor][AppVeyor image]][AppVeyor]
[![Circle CI][Circle CI image]][Circle CI]
[![Codacy][Codacy image]][Codacy]

[![Dependency Status][Dependency Status Image]][Dependency Status]
[![SensioLabsInsight][SensioLabsInsight Image]][SensioLabsInsight]

## Links

 - Demo: http://asynctweets.alexislefebvre.com/demo/
 - Code coverage: http://asynctweets.alexislefebvre.com/codecoverage/
 - Doxygen: http://asynctweets.alexislefebvre.com/doxygen/

## Goal

The goal of this project is to create an online Twitter reader, built with [Symfony][Symfony].
AsyncTweets retrieves and stores your timeline, allowing to read your Twitter timeline even if you're away from your Twitter client for several days.

This bundle is also used to test several CI (Continuous Integration) services.

## Features

 - Retrieve tweets by using User's Twitter keys
 - Display the tweets with a pagination
 - Display images below tweets

## Installation

### Requirements:

 - [Twitter keys][Twitter keys]
 - PHP >= 5.5 (required by abraham/twitteroauth 0.6.0)
 - a database (must be supported by Doctrine2)
 - [Symfony][Symfony GitHub] (2.7 or higher) with [Composer][Composer]. If you want to install it:

        php composer.phar create-project symfony/framework-standard-edition YOUR_DIRECTORY "2.7.*" -vvv

### Steps:
 
 1. Install this bundle with Composer: `cd YOUR_DIRECTORY ; php composer.phar require alexislefebvre/async-tweets-bundle dev-master --prefer-dist -vvv`
 2. Add the bundle in <kbd>app/AppKernel.php</kbd>:

        <?php
        
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new TweetsBundle\TweetsBundle(),
            );
        }

 3. Enter your Twitter keys at the end of the <kbd>app/config/parameters.yml</kbd> file:

        twitter_consumer_key: null
        twitter_consumer_secret: null
        twitter_token: null
        twitter_token_secret: null

 4. Create the database and create the tables: `php app/console doctrine:schema:update --force --env=prod`

### Usage:

 1. Launch this command to fetch tweets: `php app/console statuses:hometimeline --table --env=prod`, with the ` --table` option the imported tweets will be shown
 2. Update <kbd>app/config/config.yml</kbd> to enable Assetic if it's not activated yet:
 
        framework:
            # ...
            assets: ~

 3. Import the routes in your <kbd>app/config/routing.yml</kbd>:
 
        asynctweets_website:
            resource: "@TweetsBundle/Resources/config/routing.yml"
            prefix:   /asynctweets # Use only "/" if you want AsyncTweets at the root of the website

 4. Open the page with your browser `.../YOUR_DIRECTORY/web/asynctweets/` or use the following command `php app/console statuses:read --env=prod` to see tweets

 5. If you have an error `An exception has been thrown during the compilation of a template ("You must add TweetsBundle to the assetic.bundle config to use the {% image %} tag in TweetsBundle::layout.html.twig.") in "TweetsBundle::layout.html.twig".`, add the bundle in assetic bundles:
 
        # Assetic Configuration
        assetic:
            # ...
            bundles: [ TweetsBundle ]

 6. Add `php app/console statuses:hometimeline --env=prod` in your crontab (e.g. every hour) to retrieve tweets automatically

## Dependencies
 - [symfony/symfony][Symfony GitHub] (2.7+)
 - [abraham/twitteroauth][twitteroauth] (^0.6.0)
 - [twitter/bootstrap][Twitter Bootstrap] (use [Bootswatch 3.3.2][Bootstrap CDN])


### Tests:

If `phpunit` is installed:

    phpunit

Or by installing `phpunit` with Composer:

    php composer.phar require --dev phpunit/phpunit "4.8.* || 5.1.*" -vvv ; php vendor/bin/phpunit

### Development environment

 - [doctrine/doctrine-fixtures-bundle][doctrine-fixtures-bundle] (~2.3)
 - [liip/functional-test-bundle][functional-test-bundle] (~1.0)

[Packagist]: https://packagist.org/packages/alexislefebvre/async-tweets-bundle
[Packagist Stable Image]: https://poser.pugx.org/alexislefebvre/async-tweets-bundle/v/stable.svg
[Packagist Unstable Image]: https://poser.pugx.org/alexislefebvre/async-tweets-bundle/v/unstable.svg

[Symfony]: http://symfony.com/
[Twitter keys]: https://apps.twitter.com/
[Symfony GitHub]: https://github.com/symfony/symfony
[Composer]: https://getcomposer.org/download/

[Travis Master image]: https://travis-ci.org/alexislefebvre/TweetsBundle.svg?branch=master
[Travis Master]: https://travis-ci.org/alexislefebvre/TweetsBundle
[Scrutinizer image]: https://scrutinizer-ci.com/g/alexislefebvre/TweetsBundle/badges/quality-score.png?b=master
[Scrutinizer]: https://scrutinizer-ci.com/g/alexislefebvre/TweetsBundle/?branch=master
[Scrutinizer Coverage image]: https://scrutinizer-ci.com/g/alexislefebvre/TweetsBundle/badges/coverage.png?b=master
[Code Climate image]: https://codeclimate.com/github/alexislefebvre/TweetsBundle/badges/gpa.svg
[Code Climate]: https://codeclimate.com/github/alexislefebvre/TweetsBundle
[Coveralls image]: https://coveralls.io/repos/github/alexislefebvre/TweetsBundle/badge.svg?branch=master
[Coveralls]: https://coveralls.io/github/alexislefebvre/TweetsBundle?branch=master
[AppVeyor image]: https://ci.appveyor.com/api/projects/status/p3n423qlvnrkabg3/branch/master?svg=true
[AppVeyor]: https://ci.appveyor.com/project/alexislefebvre/TweetsBundle/branch/master
[Circle CI image]: https://circleci.com/gh/alexislefebvre/TweetsBundle/tree/master.svg?style=svg
[Circle CI]: https://circleci.com/gh/alexislefebvre/TweetsBundle/tree/master
[Codacy image]: https://api.codacy.com/project/badge/grade/0803f8e9a98c4abca2c9bcfe750e19c4
[Codacy]: https://www.codacy.com/app/alexislefebvre/TweetsBundle

[Dependency Status Image]: https://www.versioneye.com/user/projects/5523d4ac971f7847ca0006cd/badge.svg?style=flat
[Dependency Status]: https://www.versioneye.com/user/projects/5523d4ac971f7847ca0006cd
[SensioLabsInsight Image]: https://insight.sensiolabs.com/projects/00d3eb84-0c1c-471c-9f76-d8abe41a647d/mini.png
[SensioLabsInsight]: https://insight.sensiolabs.com/projects/00d3eb84-0c1c-471c-9f76-d8abe41a647d

[twitteroauth]: https://github.com/abraham/twitteroauth
[Twitter Bootstrap]: https://github.com/twbs/bootstrap
[Bootstrap CDN]: http://www.bootstrapcdn.com/#bootswatch_tab
[doctrine-fixtures-bundle]: https://github.com/doctrine/DoctrineFixturesBundle
[functional-test-bundle]: https://github.com/liip/LiipFunctionalTestBundle

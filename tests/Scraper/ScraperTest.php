<?php

namespace PicoFeed\Scraper;

use PHPUnit\Framework\TestCase;
use PicoFeed\Reader\Reader;
use PicoFeed\Config\Config;
use Spatie\Snapshots\MatchesSnapshots;

class ScraperTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @group online
     */
    public function testUrlScraper()
    {
        $grabber = new Scraper(new Config());
        $grabber->setUrl('http://www.lemonde.fr/proche-orient/article/2013/08/30/la-france-nouvelle-plus-ancienne-alliee-des-etats-unis_3469218_3218.html');
        $grabber->execute();
        $this->assertTrue($grabber->hasRelevantContent());

        $grabber = new Scraper(new Config());
        $grabber->setUrl('http://www.inc.com/suzanne-lucas/why-employee-turnover-is-so-costly.html');
        $grabber->execute();
        $this->assertTrue($grabber->hasRelevantContent());

        $grabber = new Scraper(new Config());
        $grabber->setUrl('http://arstechnica.com/information-technology/2013/08/sysadmin-security-fail-nsa-finds-snowden-hijacked-officials-logins/');
        $grabber->execute();
        $this->assertTrue($grabber->hasRelevantContent());
    }

    /**
     * @group online
     */
    public function testRuleParser()
    {
        $grabber = new Scraper(new Config());
        $grabber->setUrl('https://www.andreas-wiedel.de/');
        $grabber->execute();
        $this->assertTrue($grabber->hasRelevantContent());

        $this->assertMatchesSnapshot($grabber->getRelevantContent());
    }

    /**
     * @group online
     */
    public function testRssGrabContent()
    {
        $reader = new Reader();
        $client = $reader->download('http://www.egscomics.com/rss.php');
        $parser = $reader->getParser($client->getUrl(), $client->getContent(), $client->getEncoding());
        $parser->enableContentGrabber();
        $feed = $parser->execute();

        $this->assertTrue(is_array($feed->items));
        $this->assertTrue(strpos($feed->items[0]->content, '<img') >= 0);
    }

    /**
     * @group online
     */
    public function testContentGrabberCallback()
    {
        $reader = new Reader();
        $client = $reader->download('http://www.egscomics.com/rss.php');
        $parser = $reader->getParser($client->getUrl(), $client->getContent(), $client->getEncoding());
        $that   = $this;
        $parser->enableContentGrabber(false, function($feed, $item, $scraper) use ($that) {
            $that->assertInstanceOf('PicoFeed\Scraper\Scraper', $scraper);
        });
        $parser->execute();
    }
}

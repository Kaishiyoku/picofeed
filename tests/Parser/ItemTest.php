<?php

namespace PicoFeed\Parser;

use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testLangRTL()
    {
        $item = new Item();
        $item->language = 'fr_FR';
        $this->assertFalse($item->isRTL());

        $item->language = 'ur';
        $this->assertTrue($item->isRTL());

        $item->language = 'syr-**';
        $this->assertTrue($item->isRTL());

        $item->language = 'ru';
        $this->assertFalse($item->isRTL());
    }

    public function testGetTag()
    {
        $parser = new Rss20(file_get_contents('tests/fixtures/podbean.xml'));
        $feed = $parser->execute();
        $this->assertEquals(['http://aroundthebloc.podbean.com/e/s03e11-finding-nemo-rocco/'], $feed->items[0]->getTag('guid'));
        $this->assertEquals(['false'],  $feed->items[0]->getTag('guid', 'isPermaLink'));
        $this->assertEquals(['http://aroundthebloc.podbean.com/mf/web/28bcnk/ATBLogo-BlackBackground.png'],  $feed->items[0]->getTag('media:content', 'url'));
        $this->assertEquals(['http://aroundthebloc.podbean.com/e/s03e11-finding-nemo-rocco/feed/'],  $feed->items[0]->getTag('wfw:commentRss'));
        $this->assertEquals([],  $feed->items[0]->getTag('wfw:notExistent'));
        $this->assertCount(7, $feed->items[0]->getTag('itunes:*'));
    }

    public function testGetPubDate()
    {
        $parser = new Rss20(file_get_contents('tests/fixtures/rss_20_mal.xml'));
        $feed = $parser->execute();

        $this->assertEquals(new \DateTime('2019-05-10T13:42:08.000000+0200'), $feed->items[0]->getDate());
    }
}

<?php

namespace PicoFeed\Client;

use DateTime;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private const DATETIME_FORMAT = 'm-d-Y H:i:s';

    /**
     * @group online
     */
    public function testDownload()
    {
        $client = Client::getInstance();
        $client->setUrl('http://php.net/robots.txt');
        $client->execute();

        $this->assertTrue($client->isModified());
        $this->assertNotEmpty($client->getContent());
        $this->assertNotEmpty($client->getEtag());
        $this->assertNotEmpty($client->getLastModified());
    }

    /**
     * @runInSeparateProcess
     * @group online
     */
    public function testPassthrough()
    {
        $client = Client::getInstance();
        $client->setUrl('https://www.andreas-wiedel.de/img/icons/favicon-96x96.png');
        $client->enablePassthroughMode();
        $client->execute();

        $this->expectOutputString(file_get_contents('tests/fixtures/favicon-96x96.png'));
    }

    /**
     * @group online
     */
    public function testCacheBothHaveToMatch()
    {
        $client = Client::getInstance();
        $client->setUrl('http://php.net/robots.txt');
        $client->execute();
        $etag = $client->getEtag();

        $client = Client::getInstance();
        $client->setUrl('http://php.net/robots.txt');
        $client->setEtag($etag);
        $client->execute();

        $this->assertTrue($client->isModified());
    }

    /**
     * @group online
     */
    public function testMultipleDownload()
    {
        $client = Client::getInstance();
        $client->setUrl('https://packagist.org/');
        $result = $client->doRequest();

        $body = $result['body'];
        $result = $client->doRequest();

        $this->assertEquals($body, $result['body']);
    }

    /**
     * @group online
     */
    public function testCacheEtag()
    {
        $client = Client::getInstance();
        $client->setUrl('http://php.net/robots.txt');
        $client->execute();
        $etag = $client->getEtag();
        $lastModified = $client->getLastModified();

        $client = Client::getInstance();
        $client->setUrl('http://php.net/robots.txt');
        $client->setEtag($etag);
        $client->setLastModified($lastModified);
        $client->execute();

        $this->assertFalse($client->isModified());
    }

    /**
     * @group online
     */
    public function testCacheLastModified()
    {
        $client = Client::getInstance();
        $client->setUrl('https://www.andreas-wiedel.de/img/icons/manifest.json');
        $client->execute();
        $lastmod = $client->getLastModified();

        $client = Client::getInstance();
        $client->setUrl('https://www.andreas-wiedel.de/img/icons/manifest.json');
        $client->setLastModified($lastmod);
        $client->execute();

        $this->assertFalse($client->isModified());
    }

    /**
     * @group online
     */
    public function testCacheBoth()
    {
        $client = Client::getInstance();
        $client->setUrl('https://www.andreas-wiedel.de/img/icons/manifest.json');
        $client->execute();
        $lastmod = $client->getLastModified();
        $etag = $client->getEtag();

        $client = Client::getInstance();
        $client->setUrl('https://www.andreas-wiedel.de/img/icons/manifest.json');
        $client->setLastModified($lastmod);
        $client->setEtag($etag);
        $client->execute();

        $this->assertFalse($client->isModified());
    }

    /**
     * @group online
     */
    public function testCharset()
    {
        $client = Client::getInstance();
        $client->setUrl('http://php.net/');
        $client->execute();
        $this->assertEquals('utf-8', $client->getEncoding());

        $client = Client::getInstance();
        $client->setUrl('http://php.net/robots.txt');
        $client->execute();
        $this->assertEquals('', $client->getEncoding());
    }

    /**
     * @group online
     */
    public function testContentType()
    {
        $client = Client::getInstance();
        $client->setUrl('https://www.andreas-wiedel.de/img/icons/favicon-96x96.png');
        $client->execute();
        $this->assertEquals('image/png', $client->getContentType());

        $client = Client::getInstance();
        $client->setUrl('https://www.andreas-wiedel.de/');
        $client->execute();
        $this->assertEquals('text/html; charset=utf-8', $client->getContentType());
    }

    public function testExpirationWithExpiresHeader()
    {
        $client = Client::getInstance();
        $headers = new HttpHeaders(['Expires' => 'Fri, 30 Dec 2016 22:58:52 GMT']);
        $this->assertEquals(new DateTime('Fri, 30 Dec 2016 22:58:52 GMT'), $client->parseExpiration($headers));
    }

    public function testExpirationWithExpiresHeaderAtZero()
    {
        $client = Client::getInstance();
        $headers = new HttpHeaders(['Expires' => '0']);
        $this->assertEquals((new DateTime())->format(self::DATETIME_FORMAT), $client->parseExpiration($headers)->format(self::DATETIME_FORMAT));
    }

    public function testExpirationWithCacheControlHeaderAndZeroMaxAge()
    {
        $client = Client::getInstance();
        $headers = new HttpHeaders(['cache-control' => 'private, max-age=0, no-cache']);
        $this->assertEquals((new DateTime())->format(self::DATETIME_FORMAT), $client->parseExpiration($headers)->format(self::DATETIME_FORMAT));
    }

    public function testExpirationWithCacheControlHeaderAndNotEmptyMaxAge()
    {
        $client = Client::getInstance();
        $headers = new HttpHeaders(['cache-control' => 'private, max-age=600']);
        $this->assertEquals((new DateTime('+600 seconds'))->format(self::DATETIME_FORMAT), $client->parseExpiration($headers)->format(self::DATETIME_FORMAT));
    }

    public function testExpirationWithCacheControlHeaderAndOnlyMaxAge()
    {
        $client = Client::getInstance();
        $headers = new HttpHeaders(['cache-control' => 'max-age=300']);
        $this->assertEquals((new DateTime('+300 seconds'))->format(self::DATETIME_FORMAT), $client->parseExpiration($headers)->format(self::DATETIME_FORMAT));
    }

    public function testExpirationWithCacheControlHeaderAndNotEmptySMaxAge()
    {
        $client = Client::getInstance();
        $headers = new HttpHeaders(['cache-control' => 'no-transform,public,max-age=300,s-maxage=900']);
        $this->assertEquals((new DateTime('+900 seconds'))->format(self::DATETIME_FORMAT), $client->parseExpiration($headers)->format(self::DATETIME_FORMAT));
    }
}

<?php

namespace PicoFeed\Reader;

use PHPUnit\Framework\TestCase;

class FaviconTest extends TestCase
{
    public function testExtract()
    {
        $favicon = new Favicon();

        $html = '<!DOCTYPE html><html><head>
                <link rel="icon" href="http://example.com/myicon.ico" />
                </head><body><p>boo</p></body></html>';

        $this->assertEquals(['http://example.com/myicon.ico'], $favicon->extract($html));

        // multiple values in rel attribute
        $html = '<!DOCTYPE html><html><head>
                <link rel="shortcut icon" href="http://example.com/myicon.ico" />
                </head><body><p>boo</p></body></html>';

        $this->assertEquals(['http://example.com/myicon.ico'], $favicon->extract($html));

        // with other attributes present
        $html = '<!DOCTYPE html><html><head>
                <link rel="icon" type="image/vnd.microsoft.icon" href="http://example.com/image.ico" />
                </head><body><p>boo</p></body></html>';

        $this->assertEquals(['http://example.com/image.ico'], $favicon->extract($html));

        // ignore icon in other attribute
        $html = '<!DOCTYPE html><html><head>
                <link type="icon" href="http://example.com/image.ico" />
                </head><body><p>boo</p></body></html>';

        // ignores apple icon
        $html = '<!DOCTYPE html><html><head>
                <link rel="apple-touch-icon" href="assets/img/touch-icon-iphone.png">
                <link rel="icon" type="image/png" href="http://example.com/image.png" />
                </head><body><p>boo</p></body></html>';

        $this->assertEquals(['http://example.com/image.png'], $favicon->extract($html));

        // allows multiple icons
        $html = '<!DOCTYPE html><html><head>
                <link rel="icon" type="image/png" href="http://example.com/image.png" />
                <link rel="icon" type="image/x-icon" href="http://example.com/image.ico"/>
                </head><body><p>boo</p></body></html>';

        $this->assertEquals(['http://example.com/image.png', 'http://example.com/image.ico'], $favicon->extract($html));

        // empty array with broken html
        $html = '!DOCTYPE html html head
                link rel="icon" type="image/png" href="http://example.com/image.png" /
                link rel="icon" type="image/x-icon" href="http://example.com/image.ico"/
                /head body /p boo /p body /html';

        $this->assertEquals([], $favicon->extract($html));

        // empty array on no input
        $this->assertEquals([], $favicon->extract(''));

        // empty array on no icon found
        $html = '<!DOCTYPE html><html><head>
                </head><body><p>boo</p></body></html>';

        $this->assertEquals([], $favicon->extract($html));
    }

    /**
     * @group online
     */
    public function testExists()
    {
        $favicon = new Favicon();

        $this->assertTrue($favicon->exists('https://miniflux.net/favicon.ico'));
        $this->assertFalse($favicon->exists('http://foobar'));
        $this->assertFalse($favicon->exists(''));
    }

    /**
     * @group online
     */
    public function testFind_inMeta()
    {
        $favicon = new Favicon();

        // favicon in meta
        $this->assertEquals(
            'https://github.githubassets.com/favicon.ico',
            $favicon->find('https://github.com/')
        );

        $this->assertNotEmpty($favicon->getContent());
    }

    /**
     * @group online
     */
    public function testFind_directLinkFirst()
    {
        $favicon = new Favicon();

        $this->assertEquals(
            'https://www.andreas-wiedel.de/img/icons/apple-icon-57x57.png',
            $favicon->find('https://www.andreas-wiedel.de', '/img/icons/apple-icon-57x57.png')
        );

        $this->assertNotEmpty($favicon->getContent());
    }

    /**
     * @group online
     */
    public function testFind_fallsBackToExtract()
    {
        $favicon = new Favicon();
        $this->assertEquals(
            'https://github.githubassets.com/favicon.ico',
            $favicon->find('https://github.com', '/nofavicon.ico')
        );

        $this->assertNotEmpty($favicon->getContent());
    }

    /**
     * @group online
     */
    public function testDataUri()
    {
        $favicon = new Favicon();

        $this->assertEquals(
            'https://www.andreas-wiedel.de/img/icons/android-icon-192x192.png',
            $favicon->find('https://www.andreas-wiedel.de')
        );

        $expected = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMAAAADACAMAAABlApw1AAAC+lBMVEUAAABWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RWd5RrEsOTAAAA/XRSTlMAAAhARMclELT9X2f5pwbU4SoEj2lD67EJEbXnMWhyJtW6DQWQN3y2wu8hhiDXyhUCkfNI8QHA0Bpz/vdRLOOZpR9W+PpaZaLO3oL8ZDXiq6HkLU5s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';

        $this->assertEquals($expected, $favicon->getDataUri());
    }

    /**
     * @group online
     */
    public function testDataUri_withBadContentType()
    {
        $favicon = new Favicon();
        $this->assertNotEmpty($favicon->find('http://www.lemonde.fr/'));
        $expected = 'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAAAAABWESUoAAABMUlEQVR4AWMYOoC5moCClhYGzY7Di1UYGFxmCQL5knOiGJGkGdW/nz/5HwiuyK76//++MVPOx///T9iyRQhBFeh5/fkPAVdK3/7//+PCpK6fQM6fxSJQBZxZVpGO1lVAMeV0t0///08UyPbc+v9/OaoznCOAChQZUk///9/LwOBg8OE3O6oCy0iwgrjjYAWCd/9fY8CjQDvp//+z+BQElBBQEFVNQEEs2QoEoQrisCkAhsNbBjwKnBP+/y+BKYjHokB86pMgBnwKpCfwMsAVJGAocJ6w59AUPQY+JAVXkaTtLoAj++e63WAFTQwZhf///22bA0sv7be74oIynvx/m8EEUnBMjsHb9+v/v9Nh6SUunomBQbgxbIYwiDPJC6Sn0n2ZMZojWRXAlAKUq80wlAAAJbvjBhQMZdcAAAAASUVORK5CYII=';
        $this->assertEquals($expected, $favicon->getDataUri());
    }
}

<?php

return [
    'grabber' => [
        '%.*%' => [
            'test_url' => 'http://foreignpolicy.com/2016/01/09/networking-giant-pulls-nsa-linked-code-exploited-by-hackers/',
            'body' => [
                '//article',
            ],
            'strip' => [
                '//div[@id="post-category"]',
                '//div[@id="desktop-right"]',
                '//h1',
                '//section[@class="article-meta"]',
                '//div[@class="side-panel-wrapper"]',
                '//*[contains(@class, "share-")]',
                '//*[contains(@id, "taboola-")]',
                '//div[@class="comments"]',
            ],
        ],
    ],
];

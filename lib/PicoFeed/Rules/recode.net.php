<?php

return [
    'grabber' => [
        '%.*%' => [
            'test_url' => 'http://recode.net/2015/09/26/big-tech-rolls-out-red-carpet-for-indian-prime-minister-lobbies-behind-closed-doors/',
            'body' => [
            '//img[contains(@class,"attachment-large")]',
            '//div[contains(@class,"postarea")]',
            '//li[@class,"author"]',
            ],
            'strip' => [
            '//script',
            '//div[contains(@class,"sharedaddy")]',
            '//div[@class="post-send-off"]',
            '//div[@class="large-12 columns"]',
            '//div[contains(@class,"inner-related-article")]',
            ],
        ],
    ],
];

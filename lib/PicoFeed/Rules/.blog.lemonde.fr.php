<?php

return [
    'grabber' => [
        '%.*%' => [
            'test_url' => 'http://combat.blog.lemonde.fr/2013/08/31/teddy-riner-le-rookie-devenu-rambo/#xtor=RSS-3208',
            'body' => [
                '//div[@class="entry-content"]',
            ],
            'strip' => [
                '//*[contains(@class, "fb-like") or contains(@class, "social")]'
            ],
        ]
    ]
];

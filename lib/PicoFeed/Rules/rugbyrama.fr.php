<?php

return [
    'grabber' => [
        '%.*%' => [
            'test_url' => 'http://www.rugbyrama.fr/rugby/top-14/2015-2016/top-14-hayman-coupe-du-monde-finale-2012-lutte.-voici-levan-chilachava-toulon_sto5283863/story.shtml',
           'body' => [
                '//div[@class="storyfull__content"]',
           ],
            'strip' => [
                '//script',
                '//form',
                '//style',
                '//*[@class="share-buttons"]',
                '//*[@class="ad"]',
                '//*[@class="hide-desktop"]',
                '//*[@id="tracking_img"]',
            ]
        ]
    ]
];
<?php

return [
    'libraries' => [
        'movies' => [
            'lang' => 'en-US,en',
        ],
    ],

    'providers' => [
        'tvdb' => [
            'image_base_url' => env('TVDB_IMAGE_BASE_URL', 'https://www.thetvdb.com/banners'),
            'identifier'     => env('TVDB_UNIQUE_ID'),
            'key'            => env('TVDB_API_KEY'),
            'username'       => env('TVDB_USERNAME'),
        ],
    ],
];

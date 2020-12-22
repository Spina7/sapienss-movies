<?php

return [
    'roles' => [
        [
            'default' => true,
            'extends' => 'users',
            'name' => 'users',
            'permissions' => [
                'links.view',
                'links.create',
            ]
        ],
        [
            'guests' => true,
            'extends' => 'guests',
            'name' => 'guests',
            'permissions' => [
                'links.view',
            ]
        ]
    ],
    'all' => [
        'links' => [
            'links.view',
            'links.create',
            'links.update',
            'links.delete',
        ],
    ]
];

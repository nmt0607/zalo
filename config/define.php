<?php

return [
    'user' => [
        'state' => [
            'active' => 'active',
            'inactive' => 'inactive',
        ],
        'role' => [
            'user' => 'user',
            'admin' => 'admin',
        ],
    ],
    'message' => [
        'status' => [
            'read' => 0,
            'unread' => 1,
        ]
    ],
    'events' => [
        'authenticating' => 'authenticating',
        'conversation_joining' => 'joinchat',
        'conversation_joined' => 'conversation_joined',
        'message_sending' => 'send',
        'message_created' => 'message_created',
    ]
];

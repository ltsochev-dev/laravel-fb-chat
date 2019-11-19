<?php

return [
    'enabled' => true,

    'app_id' => 0,

    'page_id' => 0,

    'autoinject' => true,

    'view' => 'ltsochev-customerchat::customer-chat.wrapper',

    'inject_sdk' => true,

    'sdk' => [
        'xfbml' => true,
        'autoLogAppEvents' => true,
        'graph_version' => 'v5.0',
    ],

    'locale' => 'en_US',

    'plugin' => [
        'theme_color' => '#0084FF',
        'logged_in_greeting' => 'Hello There!',
        'logged_out_greeting' => 'Log in to Chat with Us',
        'greeting_dialog_display' => 'show',
        'ref' => ''
    ],

    'except' => [
        'admin/*'
    ]
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Library active state
    |--------------------------------------------------------------------------
    |
    | TRUE enables the library, while FALSE disables it even if autoinject is
    | enabled. This is a single global switch key for this library. If this is
    | FALSE, it simply won't work.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Facebook Application ID
    |--------------------------------------------------------------------------
    |
    | Application ID is needed by the Facebook's SDK which we will be injecting
    | into the page's source in order to have Facebook's customer chat
    |
    */

    'app_id' => 0,

    /*
    |--------------------------------------------------------------------------
    | Facebook Page ID
    |--------------------------------------------------------------------------
    |
    | Customer chat works only with Facebook pages. You can't be messaged
    | directly by your customers. As such you would need to set up a
    | facebook page and use its ID in here.
    |
    */

    'page_id' => 0,

    /*
    |--------------------------------------------------------------------------
    | Auto injection
    |--------------------------------------------------------------------------
    |
    | If this setting is enabled the library will capture valid HTML responses
    | and it will attempt to inject itself before the closing </body> tag.
    | If such tag does not exist it will inject itself at the end of the
    | response.
    |
    */

    'autoinject' => true,

    /*
    |--------------------------------------------------------------------------
    | View file
    |--------------------------------------------------------------------------
    |
    | The view file contains the mandatory HTML for the customer chat to work.
    | You can create your own view if you have specific needs and the library
    | will use it.
    |
    */

    'view' => 'ltsochev-customerchat::customer-chat.wrapper',

    /*
    |--------------------------------------------------------------------------
    | Facebook SDK injection
    |--------------------------------------------------------------------------
    |
    | From here you can prevent automatic SDK injection in case you have
    | already injected the Facebook SDK.
    |
    */

    'inject_sdk' => true,

    /*
    |--------------------------------------------------------------------------
    | Facebook SDK initialization
    |--------------------------------------------------------------------------
    |
    | If this is disabled you'll have to initialize the Facebook's SDK
    | yourself in order for the customer chat to work.
    |
    */

    'sdk_init' => true,

    /*
    |--------------------------------------------------------------------------
    | Facebook SDK initialization settings
    |--------------------------------------------------------------------------
    |
    | The settings listed here will be used to initialize the Facebook' SDK
    |
    */

    'sdk' => [
        'xfbml' => true,
        'autoLogAppEvents' => true,
        'graph_version' => 'v5.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Facebook Locale
    |--------------------------------------------------------------------------
    |
    | If you need the Facebook SDK in a different locale this is the option
    | you'll have to change. The localization is based off of Facebook's locale
    | rules, which you can read in here
    | https://developers.facebook.com/docs/internationalization/#plugins
    |
    */

    'locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Facebook Customer chat plugin settings
    |--------------------------------------------------------------------------
    |
    | Customer chat's options. More information can be found at the official
    | documentation.
    | https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin/#customization
    |
    */

    'plugin' => [
        'theme_color' => '#0084FF',
        'logged_in_greeting' => 'Hello There!',
        'logged_out_greeting' => 'Log in to Chat with Us',
        'greeting_dialog_display' => 'show',
        'ref' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes without autoinjection
    |--------------------------------------------------------------------------
    |
    | If you have autoinjection enabled and you'd prefer not to have the chat
    | autoinjected, for example, in your admin panel you can add the URI
    | in here. When the URI matches any entry from this array the system
    | disables itself.
    |
    */

    'except' => [
        'admin/*'
    ]
];

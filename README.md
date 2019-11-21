# Facebook Customer Chat for Laravel

A simple Laravel library that wraps the Facebook customer chat plugin in your website.

## Installation

Facebook Customer Chat docs can be found here: https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin/

Before installing the plugin please follow the instructions on this page https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin/#steps

To install the wrapper simply run the following command

```bash
composer require ltsochev/laravel-fb-chat
```

### Installation prior Laravel 5.6

On older versions of Laravel you don't have the luxury of package autodiscovery so you'll have to manually add it to your project.

First off you'll need to publish the configuration of the project

```bash
php artisan vendor:publish
```

Once the configurations are extracted you can edit the settings in your `config/customerchat.php` file

Once you are done with the settings you should add the service provider in your `config/app.php` file like so:

```php
<?php 

    'providers' => [
        ...
        Ltsochev\CustomerChat\CustomerChatServiceProvider::class,
        ...
    ]

```

By default the wrapper uses an auto-inject technique so you should be able to see the Facebook chat plugin immediately. 

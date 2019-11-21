<?php

namespace Ltsochev\CustomerChat\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Ltsochev\CustomerChat\CustomerChatServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [CustomerChatServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('customerchat.page_id', 'testing');
    }
}

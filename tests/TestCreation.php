<?php

namespace Ltsochev\CustomerChat\Tests;

class TestCreation extends TestCase
{
    public function testCreation()
    {
        $config = require __DIR__ . '/../config/config.php';

        $viewFactory = $this->app->make('view');

        $chat = new \Ltsochev\CustomerChat\CustomerChat($viewFactory, $config);

        $this->assertInstanceOf(\Ltsochev\CustomerChat\CustomerChat::class, $chat);

        $chatServiceProvider = $this->app->make('customer-chat');

        $this->assertInstanceOf(\Ltsochev\CustomerChat\CustomerChat::class, $chatServiceProvider);
    }

    public function testRender()
    {
        $chat = $this->app->make('customer-chat');

        $html = $chat->render();

        $this->assertStringStartsWith('<!-- Customer chat plugin START -->', $html);
    }

    public function testRenderWhileDisabled()
    {
        $chat = $this->app->make('customer-chat');

        $chat->disable();

        $this->assertNull($chat->render());
    }
}

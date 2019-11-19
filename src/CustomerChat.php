<?php

namespace Ltsochev\CustomerChat;

use Illuminate\Support\Arr;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerChat
{
    private $enabled = false;
    private $appId = 0;
    private $pageId = 0;
    private $fbLocale;
    private $view;
    private $injectSdk;
    private $viewFactory;
    private $sdkSettings = [];
    private $pluginSettings = [];

    public function __construct(ViewFactory $viewFactory, array $config)
    {
        $this->viewFactory = $viewFactory;
        $this->enabled = Arr::get($config, 'enabled', false);
        $this->view = Arr::get($config, 'view', 'ltsochev-customerchat::customer-chat.wrapper');
        $this->appId = Arr::get($config, 'appId', 0);
        $this->pageId = Arr::get($config, 'page_id', 0);
        $this->fbLocale = Arr::get($config, 'fb_locale', 'en_US');
        $this->injectSdk = Arr::get($config, 'inject_sdk', false);
        $this->sdkSettings = Arr::get($config, 'sdk', []);
        $this->pluginSettings = Arr::get($config, 'plugin', []);
    }

    public function enabled()
    {
        return $this->enabled;
    }

    public function getView()
    {
        if ($this->pageId == 0 || empty($this->pageId)) {
            throw new \RuntimeException("You need a valid Facebook Page ID in order to use the customer chat plugin!");
        }

        $sdk = null;
        if ($this->injectSdk === true) {
            $sdk = $this->sdkSettings;
        }

        return $this->viewFactory->make($this->view, [
            'pageId' => $this->pageId,
            'injectSdk' => $this->injectSdk,
            'locale' => $this->fbLocale,
            'attributes' => $this->getAttributes(),
            'sdk' => $sdk,
        ]);
    }

    public function render()
    {
        if (!$this->enabled()) {
            return null;
        }

        return $this->getView()->render();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function modifyResponse(Request $request, Response $response)
    {
        if (!$this->enabled() || isset($response->exception)
            || $response->isRedirection() || $this->isJsonRequest($request)
            || $request->getRequestFormat() !== 'html' || $response->getContent() === false) {
            return $response;
        }

        $this->injectCustomerChat($response);

        return $response;
    }

    protected function injectCustomerChat(Response $response)
    {
        $content = $response->getContent();

        $renderedContent = $this->render();

        $pos = strripos($content, '</body>');

        if ($pos !== false) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content .= $renderedContent;
        }

        $response->setContent($content);
        $response->headers->remove('Content-Length');
    }

    protected function isJsonRequest(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return true;
        }

        $acceptable = $request->getAcceptableContentTypes();
        return (isset($acceptable[0]) && $acceptable[0] == 'application/json');
    }

    private function getAttributes()
    {
        $htmlAttributes = [];

        $validAttributes = $this->getValidAttributes();

        foreach ($validAttributes as $key => $value) {
            $htmlAttributes[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }

        return $htmlAttributes;
    }

    private function getValidAttributes()
    {
        return array_filter($this->pluginSettings, function($key, $value) {
            return !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

}

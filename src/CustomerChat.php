<?php

namespace Ltsochev\CustomerChat;

use Illuminate\Support\Arr;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerChat
{
    /**
     * True when enabled, false disabled
     *
     * @var bool
     */
    private $enabled = false;

    /**
     * Facebook application id. Can be found in the
     * developer portal
     *
     * @var int
     */
    private $appId = 0;

    /**
     * Facebook page id for the customer chat. Customer chat
     * can only be active with pages
     *
     * @var int
     */
    private $pageId = 0;

    /**
     * Facebook SDK locale. Should be in Facebook's locale format
     *
     * @see https://developers.facebook.com/docs/internationalization/#plugins
     * @var string
     */
    private $fbLocale;

    /**
     * The template for the customer chat box
     *
     * @var string
     */
    private $view;

    /**
     * Should we inject the Facebook SDK into the page or not
     *
     * @var bool
     */
    private $injectSdk;

    /**
     * Laravel' View factory for view creation
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    private $viewFactory;

    /**
     * Facebook SDK specific settings
     *
     * @var array
     */
    private $sdkSettings = [];

    /**
     * Facebook Customer Chat plugin specific settings
     *
     * @var array
     */
    private $pluginSettings = [];

    /**
     * Creates an instance
     *
     * @param Illuminate\Contracts\View\Factory $viewFactory
     * @param array $config
     */
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

    /**
     * TRUE when customer chat is enabled, FALSE otherwise
     *
     * @return bool
     */
    public function enabled()
    {
        return $this->enabled;
    }

    /**
     * Disables the customer chat plugin
     *
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Retrieves Laravel's view for the customer chat
     *
     * @return \Illuminate\View\View
     */
    public function getView()
    {
        if ($this->pageId === 0 || empty($this->pageId)) {
            throw new \RuntimeException("You need a valid Facebook Page ID in order to use the customer chat plugin!");
        }

        $sdk = null;
        if ($this->injectSdk === true) {
            $sdk = $this->sdkSettings;
        }

        return $this->viewFactory->make($this->view, [
            'appId' => $this->appId,
            'pageId' => $this->pageId,
            'injectSdk' => $this->injectSdk,
            'locale' => $this->fbLocale,
            'attributes' => $this->getAttributes(),
            'sdk' => $sdk,
        ]);
    }

    /**
     * If customer chat is enabled, renders the view and returns it
     * as a string. Returns NULL otherwise.
     *
     * @return null|string
     */
    public function render()
    {
        if (!$this->enabled()) {
            return null;
        }

        return $this->getView()->render();
    }

    /**
     * Magic method that calls render() to return the view output
     *
     * @return null|string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Decides whether the current request should be injected with
     * custom HTML and modifies the response if it is.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * Injects customer chat into the request's response
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
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

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    protected function isJsonRequest(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return true;
        }

        $acceptable = $request->getAcceptableContentTypes();
        return (isset($acceptable[0]) && $acceptable[0] == 'application/json');
    }

    /**
     * Compiles plugin settings from configuration
     * into HTML valid attributes
     *
     * @return array
     */
    private function getAttributes()
    {
        $htmlAttributes = [];

        $validAttributes = $this->getValidAttributes();

        foreach ($validAttributes as $key => $value) {
            $htmlAttributes[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }

        return $htmlAttributes;
    }

    /**
     * Filters empty values out of the plugin configuration
     *
     * @return array
     */
    private function getValidAttributes()
    {
        return array_filter($this->pluginSettings, function($value, $key) {
            return !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

}

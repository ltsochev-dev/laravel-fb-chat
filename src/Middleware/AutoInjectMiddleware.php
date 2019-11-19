<?php

namespace Ltsochev\CustomerChat\Middleware;

use Error;
use Closure;
use Exception;
use Ltsochev\CustomerChat\CustomerChat;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class AutoInjectMiddleware
{
    private $chatLib;
    private $container;

    protected $except = [];

    public function __construct(Container $container, CustomerChat $chatLib)
    {
        $this->container = $container;
        $this->chatLib = $chatLib;
        $this->except = config('customerchat.except') ?: [];
    }

    public function handle($request, Closure $next)
    {
        if (!$this->chatLib->enabled() || $this->inExceptArray($request)) {
            return $next($request);
        }

        try {
            $response = $next($request);
        } catch (Exception $e) {
            $response = $this->handleException($request, $e);
        } catch (Error $e) {
            $e = new FatalThrowableError($error);
            $response = $this->handleException($request, $e);
        }

        $this->chatLib->modifyResponse($request, $response);

        return $response;
    }

    /**
     * Handle the given exception.
     *
     * (Copy from Illuminate\Routing\Pipeline by Taylor Otwell)
     *
     * @param $passable
     * @param  Exception $e
     * @return mixed
     * @throws Exception
     */
    protected function handleException($passable, Exception $e)
    {
        if (! $this->container->bound(ExceptionHandler::class) || ! $passable instanceof Request) {
            throw $e;
        }

        $handler = $this->container->make(ExceptionHandler::class);

        $handler->report($e);

        return $handler->render($passable, $e);
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace Legion\Http;

use PHPLegends\Http\Response;
use PHPLegends\Http\JsonResponse;
use PHPLegends\Http\RedirectResponse;
use PHPLegends\View\Factory as ViewFactory;
use PHPLegends\Http\ResponseHeaderCollection;

class ResponseFactory
{
    public function __construct(ViewFactory $viewFactory, ResponseHeaderCollection $headers = null)
    {
        $this->headers = $headers ?: new ResponseHeaderCollection;

        $this->viewFactory = $viewFactory;
    }

    public function getCookies()
    {
        return $this->getHeaders()->getCookies();
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function json($content, $code = 200, \Closure $callback = null)
    {
        return new JsonResponse($content, $code, $this->headers);
    }

    public function create($content, $code = 200, \Closure $callback = null)
    {
        return new Response($content, $code, $this->headers);
    }

    public function redirect($location, $code = 302, \Closure $callback = null)
    {
        return new RedirectResponse($location, $code, $this->headers);
    }

    public function view($view, $data = [], $code = 200, \Closure $callback = null)
    {
        return $this->create($this->viewFactory->create($view, $data), $code, $callback);
    }

    public function __get($key)
    {
        if (in_array($key, ['cookies', 'headers'])) {
            
            return $this->{'get' . ucfirst($key)}();
        }

        throw new \InvalidArgumentException(sprintf(
            'Property %s::$%s doest not exists',
            __CLASS__,
            $key
        ));
    }

}
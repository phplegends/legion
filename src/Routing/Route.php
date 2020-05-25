<?php

namespace Legion\Routing;

use PHPLegends\Routes\Route as BaseRoute;

class Route extends BaseRoute
{

    protected $middlewares = [];

    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    public function addMiddleware($middleware, $name = null)
    {
        if ($name === null) {
            $this->middlewares[] = $middleware;
        } else {
            $this->middlewares[$name] = $middleware;
        }

        return $this;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}
<?php

namespace Legion\Routing;

use PHPLegends\Routes\RoutableInspector;
use PHPLegends\Routes\Router as BaseRouter;

class Router extends BaseRouter
{

    protected $middlewares = [];

    /**
     * Create a new route instance and attach to Collection
     *
     * @param array $verbs
     * @param string $pattern
     * @param string $action
     * @param null|string $name
     * @return \Legion\Routing\Route
     * */
    public function addRoute(array $verbs, $pattern, $action, $name = null)
    {

        $pattern = $this->resolvePatternValue($pattern);

        $action  = $this->resolveActionValue($action);

        $name    = $this->resolveNameValue($name);

        $route   = new Route($pattern, $action, $verbs, $name);

        if ($middlewares = $this->getMiddlewares()) {
            $route->setMiddlewares($middlewares);
        }

        $this->routes->add($route);

        return $route;
    }


    public function setOptions(array $args)
    {
        parent::setOptions($args);

        if (isset($args['middlewares'])) {
            $this->setMiddlewares($args['middlewares']);
        }

        return $this;
    }

    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }


     /**
     *
     * @param string $class
     * @param string|null $prefix
     * */

    public function resource($class, $prefix = null)
    {

        $inspector = new RoutableInspector($class);

        $router = $inspector->generateResourceRoutes(new static(), $prefix);

        $this->mergeRouter($router);

        return $this;

    }
}
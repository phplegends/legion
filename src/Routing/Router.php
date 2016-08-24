<?php

namespace Legion\Routing;

use PHPLegends\Routes\Router as BaseRouter;

class Router extends BaseRouter
{
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

        if ($filters = $this->getDefaultFilters()) {

            $route->setFilters($filters);
        }

        $this->routes->add($route);

        return $route;
    }
}
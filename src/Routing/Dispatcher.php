<?php

namespace Light\Routing;

use PHPLegends\Http\Request;
use PHPLegends\Routes\Route;
use PHPLegends\Http\Response;
use PHPLegends\Routes\Router;
use PHPLegends\Http\JsonResponse;
use PHPLegends\Routes\Dispatchable;
use PHPLegends\Http\Exceptions\HttpException;
use PHPLegends\Http\ResponseHeaderCollection;
use PHPLegends\Routes\Traits\DispatcherTrait;
use PHPLegends\Http\Exceptions\NotFoundException;
use PHPLegends\Http\Exceptions\MethodNotAllowedException;
use PHPLegends\Routes\Exceptions\NotFoundException as RouteNotFoundException;

/**
 *
 * @author Wallace de Souza Vizerra <wallacemaxters@gmail.com>
 * 
 * */
class Dispatcher implements Dispatchable
{

    use DispatcherTrait;

    protected $request;

    protected $responseFactory;

    public function __construct(Request $request, ResponseFactory $responseFactory)
    {
        $this->request = $request;

        $this->responseFactory = $responseFactory;
    }

    /**
     * 
     * @param \PHPLegends\Routes\Router $router
     * @return \PHPLegends\Http\Response
     * */
    public function dispatch(Router $router)
    {
        $route = $this->findRouteByRequest($router, $this->request);

        $this->request->setCurrentRoute($route);

        if (($filter = $this->callRouteFilters($router, $route)) !== null) {
            
            return $this->prepareReponse($filter);
        }

        $this->callRouteAction($route)->send(true);
    }

    /**
     * 
     * @param \PHPLegends\Routes\Route $route
     * @return mixed
     * */
    protected function callRouteAction(Route $route)
    {
        $callable = $this->buildRouteAction($route);

        $parameters = $route->getParameters();

        array_unshift($parameters, $this->request, $this->responseFactory);

        if ($callable instanceof Filterable) {

            $callable[0]->beforeResponse($this->request);

            $response = $this->prepareReponse(
                call_user_func_array($callable, $parameters)
            );

            $callable[0]->afterResponse($this->request, $response);

            return $response;
        }

        return $this->prepareReponse(
            call_user_func_array($callable, $parameters)
        );
    }

    /**
     * 
     * @param \PHPLegends\Routes\Router $router
     * @param \PHPLegends\Routes\Route $route
     * */
    protected function callRouteFilters(Router $router, Route $route)
    {
        foreach ($router->getFilters()->filterByRoute($route) as $filter) {

            $result = call_user_func(
                $filter->getCallback(),
                $this->request,
                $this->responseFactory
            );

            if ($result !== null) {

                return $result;
            }
        }
    }

    /**
     * Overwrites buildRouteAction of Trait
     * 
     * @param PHPLegends\Routes\Route $route
     * @return callable
     * */
    protected function buildRouteAction(Route $route)
    {
        $action = $route->getAction();

        if ($action instanceof \Closure) {

            $controller = new Controller();

            return $action->bindTo($controller, get_class($controller));
        }

        list ($class, $method) = $action;

        $class = new $class;

        return [$class, $method];
    }

    /**
     * 
     * @param \PHPLegends\Routes\Route $router
     * @param \PHPLegends\Http\Request $request
     * @return \PHPLegends\Routes\Route
     * @throws PHPLegends\Http\Exceptions\MethodNotAllowedException
     * */
    protected function findRouteByRequest(Router $router, Request $request)
    {
        try {

            return $router->findRoute(
                $request->getUri()->getPath(),
                $request->getMethod()
            );

        } catch (RouteNotFoundException $e) {

            throw new NotFoundException('Route not found');

        } catch (InvalidVerbException $e) {

            throw MethodNotAllowedException::createFromRequest($request);
        }
    }

    /**
     * 
     * @param PHPLegends\Http\Response $response
     * */
    protected function prepareReponse(Response $response)
    {        

        if ($session = $this->request->getSession()) {

            $response->withSession($session);
        }

        return $response;
    }
}


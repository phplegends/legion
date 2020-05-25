<?php

namespace Legion\Routing;

use PHPLegends\Http\Request;
use PHPLegends\Http\Response;
use Legion\Http\ResponseFactory;
use Legion\Controller\Controller;
use PHPLegends\Routes\Dispatchable;
use PHPLegends\Routes\Route as BaseRoute;
use PHPLegends\Routes\Router as BaseRouter;
use PHPLegends\Http\Exceptions\HttpException;
use PHPLegends\Routes\Traits\DispatcherTrait;
use PHPLegends\Http\Exceptions\NotFoundException;
use PHPLegends\Routes\Exceptions\InvalidVerbException;
use PHPLegends\Http\Exceptions\MethodNotAllowedException;
use PHPLegends\Routes\Exceptions\NotFoundException as RouteNotFoundException;

/**
 *
 * @author Wallace de Souza Vizerra <wallacemaxters@gmail.com>
 * */
class Dispatcher implements Dispatchable
{

    use DispatcherTrait;

    /**
     * 
     * @var \PHPLegends\Http\Request
     * */
    protected $request;

    /**
     * 
     * @var \PHPLegends\Http\ResponseFactory
     * */
    protected $responseFactory;


    /**
     * @var string
     */

     protected $defaultController = Controller::class;

    /**
     * 
     * @param \Legion\Http\Request $request
     * @param \Legion\Http\ResponseFactory $responseFactory
     * */
    public function __construct(Request $request, ResponseFactory $responseFactory)
    {
        $this->request = $request;

        $this->responseFactory = $responseFactory;
    }

    /**
     * 
     * @param \PHPLegends\Routes\Router $router
     * */
    public function dispatch(BaseRouter $router)
    {
        $route = $this->findRouteByRequest($router, $this->request);

        $this->request->setCurrentRoute($route);

        $result = $this->callRouteMiddlewares($route);

        if ($result !== null) {
            return $this->prepareReponse($result)->send();
        }

        $this->callRouteAction($route)->send(true);
    }

    /**
     * 
     * @param \Legion\Routing\Route $route
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
    protected function callRouteFilters(BaseRouter $router, BaseRoute $route)
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

            $controller = new $this->defaultController;

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
     * @param PHPLegends\Http\Response $response
     * 
     * */
    public function prepareReponse(Response $response)
    {    
        if ($session = $this->request->getSession()) {

            $response->withSession($session);
        }

        return $response;
    }


    /**
     * 
     * @param string $controller
     * 
     * @return 
     * */ 
    public function setDefaultController($controller)
    {

        if (! class_exists($controller)) {

            throw new \UnexpectedValueException(sprintf('Controller %s not found', $controller));
        }   

        $this->defaultController = $controller;

        return $this;
    }

    public function getDefaultController()
    {
        return $this->defaultController;
    }


    public function callRouteMiddlewares(Route $route)
    {
        $middlewares = $route->getMiddlewares();

        foreach ($middlewares as $key => $middleware) {

            $instance = new $middleware();

            $result = $instance->handle($this->request, $this->responseFactory);

            if ($result instanceof Response) {
                return $result;
            } elseif ($result === null) {
                continue;
            }


            throw new \UnexpectedValueException('Middleware should be return Response or NULL');
        }

    }
}



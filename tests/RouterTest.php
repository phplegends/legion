<?php


use Legion\Http\Request;
use Legion\Routing\Route;
use Legion\Routing\Router;
use PHPLegends\View\Finder;
use PHPLegends\View\Factory;
use Legion\Routing\Dispatcher;
use PHPUnit\Framework\TestCase;
use Legion\Http\ResponseFactory;

include "FakeController.php";

class RouterTest extends TestCase
{
    public function setUp()
    {
        $this->router = new Router();
    }

    public function testGroup()
    {
        $me = $this;

        $this->router->group([
            'namespace' => 'Fake\Controllers',
            'prefix'    => 'prefix/'
        ], function ($router) use($me) {


            $me->assertEquals($router->getNamespace(), 'Fake\Controllers');

            $router->resource('RouteTestController', 'fake');

            $route_show = $router->findByUriAndVerb('prefix/fake/1', 'GET');

            var_dump(get_class($route_show));
            
        });
    }

}





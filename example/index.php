<?php

include __DIR__ . '/boot.php';

use PHPLegends\View;
use Light\Http\Request;
use PHPLegends\Http\Session;
use Light\Routing\Dispatcher;
use Light\Http\ResponseFactory;

$response = new ResponseFactory(
    new View\Factory(new View\Finder)
);

$config = include 'config.php';

$router = include 'boot.php';

$request = Request::createFromGlobals();

if (isset($config['session']['handler'])) 
{
    $request->setSession(new Session(new $config['session']['handler']));
}

$router->dispatch(new Dispatcher(
    $request,
    $response
));
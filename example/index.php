<?php

include 'constants.php';

$response = new ResponseFactory(
    new View\Factory(new Finder)
);

$router = include 'boot.php';

$request = Request::createFromGlobals();

if ($config['session.enable']) 
{
    $request->setSession(new Session(new $config['session.handler']));
}

$router->dispatch(new Dispatcher(
    $request,
    $response
));
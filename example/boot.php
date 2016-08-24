<?php

use Legion\Routing\Router;

include __DIR__ . '/../vendor/autoload.php';

$router = new Router;

call_user_func(function () use ($router) {
    
    include __DIR__ . '/routes.php';
});

return $router;

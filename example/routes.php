<?php


$router->addFilter('auth', function ($request, $response) {

    if (! $request->getSession()->has('auth')) {

        return $response->redirect('/login');
    }

});

$router->get('login', function ($request, $response) {

    return $response->json('Login form here');
});

$router->group(['filters' => ['auth']], function ($router) {

    $router->get('/', function ($request, $response) {

        $data = $request->getSession()->getFlash('message') ?: 'Hello!';

        return $response->json($data);

    });

});


$router->get('/test', function ($request, $response) {

    $uri = $request->getUri(); // isso é um objeto, mas tem __toString

    $request->getSession()->setFlash('message', sprintf('Redirected from %s', $uri));

    return $response->redirect('/');
});


$router->any('params/{str?}', function ($request, $response, $id = null) {

    $params = [
        'post'    => $request->body,
        'get'     => $request->query,
        'cookies' => $request->cookies,
        'session' => $request->getSession(),
        'id'      => $id,
    ];

    return $response->json($params);
});

$router->get('/flash', function ($request, $response) {

    $request->getSession()->setFlash('message', 'Flash message!');

    return $response->redirect('/');
});
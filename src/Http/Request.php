<?php


namespace Legion\Http;

use PHPLegends\Routes\Route;
use PHPLegends\Session\SessionInterface;
use PHPLegends\Http\Request as BaseRequest;

class Request extends BaseRequest
{
    protected $currentRoute;

    protected $session;

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function setCurrentRoute(Route $route)
    {
        $this->currentRoute = $route;
    }

    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }
}
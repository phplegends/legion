<?php

namespace Legion\Controller;

use Legion\Http\Request;
use PHPLegends\Http\Response;

class Controller implements Filterable
{
    public function beforeResponse(Request $request) {}

    public function afterResponse(Request $request, Response $response) {}
}
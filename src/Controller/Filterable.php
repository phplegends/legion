<?php

namespace Light\Controller;

use Light\Http\Request;
use PHPLegends\Http\Response;

interface Filterable
{
    public function beforeResponse(Request $request);

    public function afterResponse(Request $request, Response $response);    
}
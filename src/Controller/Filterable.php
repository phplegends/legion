<?php

namespace Legion\Controller;

use Legion\Http\Request;
use PHPLegends\Http\Response;

interface Filterable
{

    /**
     * 
     * @param Legion\Http\Request $request
     * */
    public function beforeResponse(Request $request);

    /**
     * 
     * @param Legion\Http\Request $request
     * @param PHPLegends\Http\Response $response
     * */
    public function afterResponse(Request $request, Response $response);    
}
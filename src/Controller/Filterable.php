<?php

namespace Light\Controller;

use Light\Http\Request;
use PHPLegends\Http\Response;

interface Filterable
{

    /**
     * 
     * @param Light\Http\Request $request
     * */
    public function beforeResponse(Request $request);

    /**
     * 
     * @param Light\Http\Request $request
     * @param PHPLegends\Http\Response $response
     * */
    public function afterResponse(Request $request, Response $response);    
}
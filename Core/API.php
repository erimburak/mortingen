<?php

use Response\Response;
use Controller;

abstract class API extends Controller
{
    public function __construct()
    {
        $this->response = new Response();
        $this->response->setContentType(\Response\ContentType::APPLICATION_JSON);
    }
}

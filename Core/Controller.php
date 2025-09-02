<?php

use Response\Response;
use Response\HTTPStatus;
use Request\Request;

abstract class Controller
{
    public Response $response;

    /**
     * Default method for all controllers. If no method is defined in the path, this will be run.
     */
    abstract public function index();

    public function __construct()
    {
        $this->response = new Response();
        $this->response->setContentType(\Response\ContentType::TEXT_HTML);
    }

    /**
     * Accept only the given request methods.
     * 
     * Usage example;
     * 
     * ```
     * $this->setValidRequestMethods('GET', 'POST');
     * ```
     */
    public function setValidRequestMethods(string ...$methods)
    {
        $current_method = Request::getMethod();

        $methods_num = count($methods);

        for ($i = 0; $i < $methods_num; $i++)
        {
            if ($current_method->value == $methods[$i])
            {
                return;
            }
        }

        $this->response->setStatusCode(HTTPStatus::METHOD_NOT_ALLOWED);
        $this->response->setContent("Method not allowed.");
        $this->response->send();

        exit(1);
        
        return;
    }
}

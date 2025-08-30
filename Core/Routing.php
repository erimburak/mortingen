<?php

use Request\Method;
use Request\Request;
use Response\Response;
use Response\HTTPStatus;

class Routing
{
    private static array $pathHandlers = [];

    public static function runController(): void
    {

        $response = new Response();

        // Run path handlers
        static::runPathHandlerPoppers();

        $defaultControllerClass = CoreSettings::appControllersDir . "\\" . CoreSettings::defaultController;
        $controllerClass = $defaultControllerClass;
        $theMethod = CoreSettings::defaultMethod;
        $theArguments = [];

        $pathSegments = Request::getPathSegments();
        $pathSegmentCount = count($pathSegments);

        $fileToCheck = App::getAbsoluteProjectPath() . DS . CoreSettings::appDir . DS . CoreSettings::appControllersDir;
        $controllerToCheck = CoreSettings::appControllersDir;

        for ($i = 0; $i < $pathSegmentCount; $i++)
        {
            $fileToCheck .= DS . $pathSegments[$i];
            $controllerToCheck .= "\\" . $pathSegments[$i];

            if (is_dir($fileToCheck))
            {
                continue;
            }
            elseif (class_exists($controllerToCheck))
            {
                // echo ($fileToCheck);
                $controllerClass = $controllerToCheck;
                if ($i + 1 < $pathSegmentCount)
                {
                    $theMethod = $pathSegments[$i + 1];
                    $i++;
                }
                if ($i + 1 < $pathSegmentCount)
                {
                    $theArguments = array_slice($pathSegments, $i + 1);
                }
                break;
            }
            else
            {
                $response->setContent("Not found.");
                $response->send();
                return;
            }
        }

        // Instantiate the controller class
        $controller = new $controllerClass();

        //method_exists($controller, $theMethod) && //This returns true for check private/protected methods.
        // If the method is not callable, then it is not an actual method of the controller.
        if (!is_callable([$controller, $theMethod]))
        {
            $theArguments = array_merge([$theMethod], $theArguments);
            $theMethod = CoreSettings::defaultMethod;
        }

        // Set the path segments.
        $newPathSegments = &Request::getPathSegments();
        $newPathSegments = $theArguments;

        // Run the controller and send its response.
        $controller->{$theMethod}(...$theArguments);
        $controller->response->send();
    }

    /**
     * This method can be used for path handling. Each registered function will be run before processing the path, by the registration order.
     * A registered path handler can access and modify the full path.
     */
    public static function registerPathHandler(PathHandler $handler): void
    {
        static::$pathHandlers[] = $handler;
    }

    private static function runPathHandlerPoppers(): void
    {
        // while($h = array_shift(static::$pathHandlers))
        // {
        //     $h();
        // }
        $c = count(static::$pathHandlers);
        for ($i = 0; $i < $c; $i++)
        {
            static::$pathHandlers[$i]->popper();
        }
    }

    private static function runPathHandlerPushers(array $pathSegments): array
    {
        $c = count(static::$pathHandlers);
        for ($i = $c - 1; $i <= 0; $i--)
        {
            $segs = new static::$pathHandlers[$i]->pusher();
        }

        return array_merge($segs, $pathSegments);
    }
}

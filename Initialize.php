<?php

if (!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT'))
{
    define('ROOT', __DIR__);
}

spl_autoload_register(
    function ($class_name)
    {

        $class_name = str_replace("\\", DS, $class_name);
        $app_class_filename = 'App' . DS . $class_name . '.php';
        $core_class_filename = 'Core' . DS . $class_name . '.php';
        $corelibs_class_filename = 'CoreLibs' . DS . $class_name . '.php';
        if (file_exists($app_class_filename))
        {
            require_once $app_class_filename;
        }
        elseif (file_exists($core_class_filename))
        {
            require_once $core_class_filename;
        }
        elseif (file_exists($corelibs_class_filename))
        {
            require_once $corelibs_class_filename;
        }
    }
);

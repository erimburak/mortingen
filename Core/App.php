<?php

class App
{
    private static ?string $absoluteProjectPath = null;
    private static ?string $projectPath = null;
    private static ?string $uriRoot = null;

    public static function getAbsoluteProjectPath()
    {
        if (is_null(static::$absoluteProjectPath))
        {
            static::$absoluteProjectPath = realpath(dirname(__DIR__));
        }
        return static::$absoluteProjectPath;
    }

    public static function getProjectPath()
    {
        if (is_null(static::$projectPath))
        {
            $abspath = static::getAbsoluteProjectPath();
            $docroot = realpath($_SERVER['DOCUMENT_ROOT']);
            $project_path = str_replace($docroot, '', $abspath);
            $project_path = str_replace('\\', '/', $project_path);
            $project_path = trim($project_path, '/');
            static::$projectPath = $project_path;
        }
        return static::$projectPath;
    }

    public static function getURIRoot()
    {
        if (is_null(static::$uriRoot))
        {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $projectPath = static::getProjectPath();
            $baseUrl = $scheme . '://' . $host . ($projectPath ? '/' . $projectPath : '');
            static::$uriRoot = $baseUrl;
        }
        return static::$uriRoot;
    }

    public function __construct()
    {
    }

    public function run()
    {
        Routing::runController();
    }
}

<?php

namespace Request;

use Request\Method;

class Request
{
    private static ?string $path = null;
    private static ?array $pathSegments = null;

    private static int $pathSegmentLimit = 16;

    /**
     * Reads from $_GET.
     */
    public static function getQuery(string $key)
    {
        return $_GET[$key] ?? null;
    }

    /**
     * Returns $_GET.
     */
    public static function getQueryAll(): array
    {
        return $_GET;
    }

    /**
     * Reads from $_POST.
     */
    public static function getData(string $key)
    {
        return $_POST[$key] ?? null;
    }

    /**
     * Returns $_POST.
     */
    public static function getDataAll(): array
    {
        return $_POST;
    }

    /**
     * Reads from $_FILES.
     */
    public static function getFile(string $key)
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Returns $_FILES.
     */
    public static function getFileAll(): array
    {
        return $_FILES;
    }

    /**
     * Returns the full path as a string.
     */
    public static function getPath(): string
    {
        if (is_null(static::$path))
        {
            $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
            $scriptDir = realpath(dirname(dirname(__DIR__)));

            // Calculate the relative path of the project folder from the document root
            $baseFolder = str_replace($documentRoot, '', $scriptDir);
            $baseFolder = str_replace('\\', '/', $baseFolder);
            $baseFolder = trim($baseFolder, '/');

            // Get the current request URI
            $requestUri = $_SERVER['REQUEST_URI'];

            // Remove the base folder from the request URI to get the relative path
            $relativePath = str_replace($baseFolder, '', $requestUri);

            // Trim any leading slashes that remain
            $relativePath = trim($relativePath, '/');

            static::$path = static::sanitizePath($relativePath ?? '');
        }

        return static::$path;
    }

    /**
     * Limits the allowed number of segments in a URI.
     * Default is 1024. 
     */
    public static function setPathSegmentLimit(int $limit)
    {
        static::$pathSegmentLimit = $limit;
    }

    /**
     * Returns the path as an array.
     * Example: `/person/3/item/123` becomes `['person', '3', 'item', '123']`
     * Runs the path handlers and removes them from the queue.
     */
    public static function &getPathSegments(): array
    {
        if (is_null(static::$pathSegments))
        {
            $path = static::getPath();
            if (empty($path))
            {
                static::$pathSegments = [];
            }
            else
            {
                static::$pathSegments = explode('/', $path, static::$pathSegmentLimit);
            }
        }

        return static::$pathSegments;
    }

    /**
     * Get the first segment from the path.
     * This modifies the original array.
     */
    public static function popPathSegment(?string $defaultValue = null): ?string
    {
        $pathSegments = &static::getPathSegments();
        $nextValue = array_shift($pathSegments) ?? $defaultValue;
        return $nextValue;
    }

    /**
     * Put an element at the beginning of the path.
     * This modifies the original array.
     */
    public static function pushPathSegment(string $value): void
    {
        $pathSegments = &static::getPathSegments();
        array_unshift($pathSegments, $value);
    }

    /**
     * Returns an element from path segments array by index.
     * If no element at the given index, returns null.
     */
    public static function getArg(int $index): ?string
    {
        $pathSegments = static::getPathSegments();
        if ($index >= count($pathSegments))
        {
            return null;
        }
        return $pathSegments[$index];
    }

    /**
     * Returns path segments as an array.
     * Example: `/person/3/item/123` becomes `['person', '3', 'item', '123']`
     */
    public static function getArgs(): array
    {
        $pathSegments = static::getPathSegments();
        return $pathSegments;
    }

    /**
     * Converts path segments into an associative array.
     * Example: `/person/3/item/123` becomes `['person'=>'3', 'item'=>'123']`
     * If number of segments is odd, last key's value will be null.
     */
    public static function getArgsAssoc(): array
    {
        $array = static::getPathSegments();
        $assocArray = [];
        $count = count($array);
        for ($i = 0; $i < $count; $i += 2)
        {
            if (isset($array[$i + 1]))
            {
                $assocArray[$array[$i]] = $array[$i + 1];
            }
            else
            {
                $assocArray[$array[$i]] = null;
            }
        }
        return $assocArray;
    }

    /**
     * Get the current request method.
     */
    public static function getMethod(): Method
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        return Method::tryFrom($method) ?? Method::GET; // Default to GET if unknown
    }

    /**
     * Clean and sanitize the path info.
     */
    public static function sanitizePath(string $path): string
    {
        // Remove leading and trailing whitespace
        $path = trim($path);

        // Remove any dangerous characters (e.g., null bytes, directory traversal)
        // $path = filter_var($path, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $path = htmlspecialchars($path);

        // Additional sanitization like removing slashes or special characters
        // $path = preg_replace('/[^a-zA-Z0-9\-\/]/', '', $path);

        $path = filter_var($path, FILTER_SANITIZE_URL);

        $path = trim($path, "/");

        return $path;
    }
}

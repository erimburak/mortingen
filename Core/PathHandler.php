<?php

abstract class PathHandler
{
    /**
     * This method is called when a path is parsed before determining the route.
     * It may pop a path segment/path segments.
     */
    abstract public function popper(): void;

    /**
     * This method is called when a URI is generated.
     * It may push a path segment/path segments.
     * 
     * This method should do the opposite of what the popper() method does.
     * 
     * If this object's popper() method doesn't change the path, this can return just an empty array. Like: `return [];`
     * 
     * If the popper() method's changes on the path is optional, this method should consider that.
     */
    abstract public function pusher(): array;
}

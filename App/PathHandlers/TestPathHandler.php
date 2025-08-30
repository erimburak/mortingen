<?php

namespace PathHandlers;

use PathHandler;
use Request\Request;

class TestPathHandler extends PathHandler
{
    public function popper(): void
    {
        $seg = Request::popPathSegment();
        var_dump("Handler works! " . $seg);
    }

    public function pusher(): array
    {
        return [];
    }
}

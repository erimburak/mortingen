<?php

namespace Request;

enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case OPTIONS = 'OPTIONS';
    case HEAD = 'HEAD';
    case CONNECT = 'CONNECT';
    case TRACE = 'TRACE';
    case MOVE = 'MOVE';
    case COPY = 'COPY';
    case LOCK = 'LOCK';
    case UNLOCK = 'UNLOCK';
    case PROPFIND = 'PROPFIND';
    case PROPPATCH = 'PROPPATCH';
}

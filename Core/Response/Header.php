<?php

namespace Response;

enum Header: string
{
    case CONTENT_TYPE = 'Content-Type';
    case CONTENT_LENGTH = 'Content-Length';
    case LOCATION = 'Location';
    case CACHE_CONTROL = 'Cache-Control';
    case EXPIRES = 'Expires';
    case LAST_MODIFIED = 'Last-Modified';
    case ETAG = 'ETag';
    case SET_COOKIE = 'Set-Cookie';
    case AUTHORIZATION = 'Authorization';

        // CORS headers
    case ACCESS_CONTROL_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';
    case ACCESS_CONTROL_ALLOW_CREDENTIALS = 'Access-Control-Allow-Credentials';
    case ACCESS_CONTROL_ALLOW_METHODS = 'Access-Control-Allow-Methods';
    case ACCESS_CONTROL_ALLOW_HEADERS = 'Access-Control-Allow-Headers';
    case ACCESS_CONTROL_EXPOSE_HEADERS = 'Access-Control-Expose-Headers';

        // Security headers
    case CONTENT_SECURITY_POLICY = 'Content-Security-Policy';
    case STRICT_TRANSPORT_SECURITY = 'Strict-Transport-Security';
    case X_CONTENT_TYPE_OPTIONS = 'X-Content-Type-Options';
    case X_FRAME_OPTIONS = 'X-Frame-Options';
    case X_XSS_PROTECTION = 'X-XSS-Protection';
}

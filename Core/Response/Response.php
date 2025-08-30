<?php

namespace Response;

use Response\HTTPStatus;
use Response\ContentType;
use Response\Header;

class Response
{
    private bool $sent = false;

    protected HTTPStatus $statusCode;
    protected array $headers;
    protected string $content;

    public function __construct(string $content = '', HTTPStatus $statusCode = HTTPStatus::OK, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function addContent(string $content): void
    {
        $this->content .= $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setStatusCode(HTTPStatus $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): HTTPStatus
    {
        return $this->statusCode;
    }

    public function setHeader(Header $name, string $value): void
    {
        $this->headers[$name->value] = $value;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setContentType(ContentType $contentType): void
    {
        $this->setHeader(Header::CONTENT_TYPE, $contentType->value);
    }

    // CORS-related methods
    public function allowCredentials(bool $allow): void
    {
        $this->setHeader(Header::ACCESS_CONTROL_ALLOW_CREDENTIALS, $allow ? 'true' : 'false');
    }

    public function allowOrigin(string $origin): void
    {
        $this->setHeader(Header::ACCESS_CONTROL_ALLOW_ORIGIN, $origin);
    }

    public function allowMethods(array $methods): void
    {
        $this->setHeader(Header::ACCESS_CONTROL_ALLOW_METHODS, implode(', ', $methods));
    }

    public function allowHeaders(array $headers): void
    {
        $this->setHeader(Header::ACCESS_CONTROL_ALLOW_HEADERS, implode(', ', $headers));
    }

    public function exposeHeaders(array $headers): void
    {
        $this->setHeader(Header::ACCESS_CONTROL_EXPOSE_HEADERS, implode(', ', $headers));
    }

    // Security-related methods
    public function setContentSecurityPolicy(string $policy): void
    {
        $this->setHeader(Header::CONTENT_SECURITY_POLICY, $policy);
    }

    public function setStrictTransportSecurity(string $policy): void
    {
        $this->setHeader(Header::STRICT_TRANSPORT_SECURITY, $policy);
    }

    public function setXContentTypeOptions(string $option = 'nosniff'): void
    {
        $this->setHeader(Header::X_CONTENT_TYPE_OPTIONS, $option);
    }

    public function setXFrameOptions(string $option): void
    {
        $this->setHeader(Header::X_FRAME_OPTIONS, $option);
    }

    public function setXXSSProtection(string $option = '1; mode=block'): void
    {
        $this->setHeader(Header::X_XSS_PROTECTION, $option);
    }

    public function send(): void
    {
        if ($this->sent)
        {
            return;
        }
        // Send the status code
        http_response_code($this->statusCode->value);

        // Send headers
        foreach ($this->headers as $name => $value)
        {
            header("$name: $value");
        }

        // Send the content
        echo $this->content;

        $this->sent = true;
    }
}

<?php

namespace App;

class Request
{
    private string $method;

    private string $uri;

    private array $queryParams;

    private array $bodyParams;

    public function __construct(string $method, string $uri, array $query = [], array $body = [])
    {
        $this->method = strtoupper($method);

        $this->uri = parse_url($uri, PHP_URL_PATH) ?: '/';

        $this->queryParams = $query;
        $this->bodyParams = $body;
    }

    public static function fromGlobals(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_GET,
            $_POST
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function query(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function input(string $key, $default = null)
    {
        return $this->bodyParams[$key] ?? $default;
    }
}
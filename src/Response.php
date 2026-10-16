<?php

namespace App;

class Response
{
    private string $body;

    private int $statusCode;

    private array $headers;

    public function __construct(string $body = '', int $statusCode = 200, array $headers = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->body;
    }

    public static function html(string $html, int $code = 200): self
    {
        return new self($html, $code, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function json(array $data, int $code = 200): self
    {
        return new self(json_encode($data), $code, ['Content-Type' => 'application/json']);
    }

    public static function redirect(string $url, int $code = 302): self
    {
        return new self('', $code, ['Location' => $url]);
    }
}

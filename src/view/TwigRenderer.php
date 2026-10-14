<?php

declare(strict_types=1);

namespace Framework\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigRenderer
{
    private Environment $twig;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);

        $loader = new FilesystemLoader(
            $root . '/templates'
        );

        $this->twig = new Environment($loader, [
            'autoescape' => 'html',
            'strict_variables' => true,
            'debug' => false,
        ]);
    }

    public function render(
        string $template,
        array $data = []
    ): string {
        return $this->twig->render($template, $data);
    }
}
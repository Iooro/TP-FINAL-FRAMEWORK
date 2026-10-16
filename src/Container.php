<?php
declare(strict_types=1);

namespace App;

use RuntimeException;

class Container
{
    private array $factories = [];
    private array $instances = [];

    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }

    public function get(string $id): object
    {
        if (!isset($this->instances[$id])) {
            if (!isset($this->factories[$id])) {
                throw new RuntimeException("Service inconnu : $id");
            }
            $this->instances[$id] = ($this->factories[$id])($this);
        }
        return $this->instances[$id];
    }
}
<?php

namespace App\Core;

use RuntimeException;

class Container
{
    private static ?Container $instance = null;
    private array $bindings = [];

    public static function getInstance(): Container
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function get(string $abstract): object
    {
        if (!isset($this->bindings[$abstract])) {
            throw new RuntimeException("Binding not found for {$abstract}");
        }

        return $this->bindings[$abstract]($this);
    }
}
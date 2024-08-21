<?php
namespace App\Core\Contracts;

interface RouterInterface
{
    public function get(string $uri, array $handler): void;
    public function post(string $uri, array $handler): void;
}
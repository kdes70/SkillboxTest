<?php

namespace App\Services;

interface StorageInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): void;
    public function getAll(string $key): ?array;
}
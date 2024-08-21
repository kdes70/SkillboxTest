<?php

namespace App\Core\Contracts;

interface ModelInterface
{
    public function list(): array;

    public function create(array $data): void;

    public function delete(int $id): void;
}
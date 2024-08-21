<?php

namespace App\Core\Contracts;
interface ViewInterface
{
    public static function render(string $template, array $data = []): string;
}
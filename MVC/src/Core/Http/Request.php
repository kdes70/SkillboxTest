<?php

namespace App\Core\Http;

class Request
{
    public function input(string $key, int $filter = FILTER_DEFAULT): mixed
    {
        return filter_input(INPUT_POST, $key, $filter);
    }

    public function only(array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $this->input($key);
        }
        return $data;
    }
}
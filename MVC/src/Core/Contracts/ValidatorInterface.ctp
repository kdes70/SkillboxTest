<?php

namespace App\Core\Contracts;

interface ValidatorInterface
{
    public function validate(array $data, array $rules): bool;
}

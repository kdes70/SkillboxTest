<?php

namespace App\Core\Validation;


use App\Core\Contracts\ValidatorInterface;

class Validator implements ValidatorInterface
{
    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $rule) {
            $parts = explode('|', $rule);
            foreach ($parts as $part) {
                $this->makeRules($field, $part, $data);
            }
        }
        return true;
    }

    private function makeRules(string $field, string $rule, array $data): void
    {
        match (true) {
            $rule === 'required' && empty($data[$field]) => throw new \InvalidArgumentException("The $field field is required."),
            $rule === 'string' && !is_string($data[$field]) => throw new \InvalidArgumentException("The $field field must be a string."),
            $rule === 'numeric' && !is_numeric($data[$field]) => throw new \InvalidArgumentException("The $field field must be numeric."),
        };
    }
}

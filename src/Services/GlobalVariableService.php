<?php 

namespace App\Services;

class GlobalVariableService
{
    private $globalVariable = [];

    public function set(string $key, $value): void
    {
        $this->globalVariable[$key] = $value;
    }

    public function get(string $key)
    {
        return $this->globalVariable[$key] ?? null;
    }
}
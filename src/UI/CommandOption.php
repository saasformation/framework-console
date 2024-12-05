<?php

namespace SaaSFormation\Framework\Console\UI;

readonly class CommandOption
{
    public function __construct(private string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }
}
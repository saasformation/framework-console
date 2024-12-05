<?php

namespace SaaSFormation\Framework\Console\UI;

readonly class CommandArgument
{
    public function __construct(private string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }
}
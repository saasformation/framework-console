<?php

namespace SaaSFormation\Framework\Console\UI;

readonly class ArgumentDefinition
{
    public function __construct(public int $index, public string $name)
    {
    }
}
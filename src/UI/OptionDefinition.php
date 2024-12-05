<?php

namespace SaaSFormation\Framework\Console\UI;

readonly class OptionDefinition
{
    public function __construct(public int $index, public string $name, public mixed $default = null)
    {
    }
}
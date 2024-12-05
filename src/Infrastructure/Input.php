<?php

namespace SaaSFormation\Framework\Console\Infrastructure;

use SaaSFormation\Framework\Console\UI\CommandArgumentsCollection;
use SaaSFormation\Framework\Console\UI\CommandOptionsCollection;
use SaaSFormation\Framework\Console\UI\InputInterface;

class Input implements InputInterface
{
    public function __construct(private CommandArgumentsCollection $argumentsCollection, private CommandOptionsCollection $optionsCollection)
    {
    }

    public function arguments(): CommandArgumentsCollection
    {
        return $this->argumentsCollection;
    }

    public function options(): CommandOptionsCollection
    {
        return $this->optionsCollection;
    }
}
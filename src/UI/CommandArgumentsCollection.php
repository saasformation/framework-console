<?php

namespace SaaSFormation\Framework\Console\UI;

class CommandArgumentsCollection
{
    /**
     * @var array<string, CommandArgument>
     */
    private array $arguments = [];

    public function add(string $argumentName, CommandArgument $commandArgument): void
    {
        $this->arguments[$argumentName] = $commandArgument;
    }

    public function get(string $argumentName): CommandArgument
    {
        if(!isset($this->arguments[$argumentName])) {
            throw new \Exception("Argument '{$argumentName}' must be provided.");
        }

        return $this->arguments[$argumentName];
    }
}
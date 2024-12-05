<?php

namespace SaaSFormation\Framework\Console\UI;

class CommandOptionsCollection
{
    /**
     * @var array<string, CommandOption>
     */
    private array $options = [];

    public function add(string $optionName, CommandOption $commandOption): void
    {
        $this->options[$optionName] = $commandOption;
    }

    public function get(string $optionName): CommandOption
    {
        if(!isset($this->options[$optionName])) {
            throw new \Exception("Option '{$optionName}' must be provided.");
        }

        return $this->options[$optionName];
    }

    public function find(string $optionName): ?CommandOption
    {
        $option = null;

        if(isset($this->options[$optionName])) {
            $option = $this->options[$optionName];
        }

        return $option;
    }
}
<?php

namespace SaaSFormation\Framework\Console\Infrastructure;

use Assert\Assert;
use League\CLImate\CLImate;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SaaSFormation\Framework\Console\UI\ArgumentDefinition;
use SaaSFormation\Framework\Console\UI\Command;
use SaaSFormation\Framework\Console\UI\CommandArgument;
use SaaSFormation\Framework\Console\UI\CommandArgumentsCollection;
use SaaSFormation\Framework\Console\UI\CommandOption;
use SaaSFormation\Framework\Console\UI\CommandOptionsCollection;
use SaaSFormation\Framework\Console\UI\OptionDefinition;
use SaaSFormation\Framework\Contracts\Infrastructure\KernelInterface;

class Console
{
    /**
     * @var array<string, Command>
     */
    private array $commands;

    /**
     * @throws ContainerExceptionInterface
     * @throws \ReflectionException
     * @throws NotFoundExceptionInterface
     */
    public function __construct(readonly KernelInterface $kernel)
    {
        $this->commands = (new CommandsProvider($kernel->container()))->provide();
    }

    /**
     * @param array<string> $args
     * @return int
     */
    public function run(array $args): int
    {
        $output = new CLImate();

        try {
            $cliLineParts = $this->getCliLineParts(implode(' ', $args));
            $commandInstance = $this->commands[$cliLineParts['commandPath']];
            $argumentsDefinitions = $this->getArgumentsDefinitionsFromCommandInstance($commandInstance);
            $optionsDefinitions = $this->getOptionsDefinitionsFromCommandInstance($commandInstance);

            $this->validateArguments($argumentsDefinitions, explode(' ', $cliLineParts['arguments']));
            $this->validateOptions($optionsDefinitions, explode(' ', $cliLineParts['options']));

            $commandArgumentsCollection = $this->generateCommandArgumentsCollection(explode(' ', $cliLineParts['arguments']), $argumentsDefinitions);
            $commandOptionsCollection = $this->generateCommandOptionsCollection(explode(' ', $cliLineParts['options']));

            $input = new Input($commandArgumentsCollection, $commandOptionsCollection);

            $exitCode = $commandInstance->execute($input, $output);

            exit($exitCode);
        } catch (\Throwable $e) {
            $output->error($e->getMessage());
            exit(1);
        }
    }

    /**
     * @param Command $command
     * @return ArgumentDefinition[]
     */
    private function getArgumentsDefinitionsFromCommandInstance(Command $command): array
    {
        $cliLine = $command->cliLine();

        $pattern = '/^(\S+)(?:\s+(-{1,2}\w+(?:=\S+)?(?:\s+-{1,2}\w+(?:=\S+)?)*)?)?\s*(.*)?$/';

        preg_match($pattern, $cliLine, $matches);

        $arguments = $matches[3] ?? '';

        $argumentsDefinitions = [];

        foreach(explode(' ', $arguments) as $key => $argument) {
            $argumentsDefinitions[] = new ArgumentDefinition($key, str_replace(':', '', $argument));
        }

        return $argumentsDefinitions;
    }

    /**
     * @param Command $command
     * @return OptionDefinition[]
     */
    private function getOptionsDefinitionsFromCommandInstance(Command $command): array
    {
        $cliLine = $command->cliLine();

        $pattern = '/^(\S+)(?:\s+(-{1,2}\w+(?:=\S+)?(?:\s+-{1,2}\w+(?:=\S+)?)*)?)?\s*(.*)?$/';

        preg_match($pattern, $cliLine, $matches);

        $options = $matches[2] ?? '';

        $argumentsDefinitions = [];

        foreach(explode(' ', $options) as $key => $option) {
            $argumentsDefinitions[] = new OptionDefinition($key, $option);
        }

        return $argumentsDefinitions;
    }

    /**
     * @return array{'commandPath': string, 'options': string, 'arguments': string}
     */
    private function getCliLineParts(string $cliLine): array
    {
        $pattern = '/^(\S+)(\S+)(?:\s+(-{1,2}\w+(?:=\S+)?(?:\s+-{1,2}\w+(?:=\S+)?)*)?)?\s*(.*)?$/';

        preg_match($pattern, $cliLine, $matches);

        Assert::that(isset($matches[2]))->true();

        $commandPath = $matches[2];

        $options = $matches[3] ?? '';
        $arguments = $matches[4] ?? '';

        return [
            'commandPath' => $commandPath,
            'options' => $options,
            'arguments' => $arguments
        ];
    }

    /**
     * @param ArgumentDefinition[] $argumentsDefinitions
     * @param array<string> $arguments
     * @return void
     * @throws \Exception
     */
    private function validateArguments(array $argumentsDefinitions, array $arguments)
    {
        $totalMissing = count($argumentsDefinitions) - count($arguments);
        if($totalMissing > 0) {
            $names = [];
            for($i = count($argumentsDefinitions) - 1; $i >= count($argumentsDefinitions) - $totalMissing; $i--) {
                $names[] = $argumentsDefinitions[$i]->name;
            }
            $names = implode(' ', $names);
            throw new \Exception("Arguments $names missing. Please, provide them");
        }
    }

    /**
     * @param OptionDefinition[] $optionsDefinitions
     * @param array<int, string> $options
     * @return void
     * @throws \Exception
     */
    private function validateOptions(array $optionsDefinitions, mixed $options)
    {
        foreach($options as $option) {
            $parts = explode('=', $option);
            $name = $parts[0];
            $found = false;

            foreach($optionsDefinitions as $optionDefinition) {
                if($optionDefinition->name === $name) {
                    $found = true;
                }
            }

            if(!$found) {
                throw new \Exception("Provided option $name not found in command definition");
            }
        }
    }

    /**
     * @param array<int, string> $arguments
     * @param ArgumentDefinition[] $argumentsDefinitions
     * @return CommandArgumentsCollection
     */
    public function generateCommandArgumentsCollection(array $arguments, array $argumentsDefinitions): CommandArgumentsCollection
    {
        $commandArgumentsCollection = new CommandArgumentsCollection();
        for ($i = 0; $i < count($arguments); $i++) {
            $commandArgumentsCollection->add($argumentsDefinitions[$i]->name, new CommandArgument($arguments[$i]));
        }
        return $commandArgumentsCollection;
    }

    /**
     * @param array<int, string> $options
     * @return CommandOptionsCollection
     */
    public function generateCommandOptionsCollection(array $options): CommandOptionsCollection
    {
        $commandOptionsCollection = new CommandOptionsCollection();
        for ($i = 0; $i < count($options); $i++) {
            $optionParts = explode('=', $options[$i]);
            $commandOptionsCollection->add($optionParts[0], new CommandOption($optionParts[1]));
        }
        return $commandOptionsCollection;
    }
}
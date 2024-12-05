<?php declare(strict_types=1);

namespace SaaSFormation\Framework\Console\Infrastructure;

use Assert\Assert;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use SaaSFormation\Framework\Console\UI\Command;

class CommandsProvider
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @return array<string, Command>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function provide(): array
    {
        $commands = [];
        foreach ($this->getCommands() as $class) {
            if(class_exists($class)) {
                $command = $this->container->get($class);

                Assert::that($command)->isInstanceOf(Command::class);

                $commands[explode(' ', $command->cliLine())[0]] = $command;
            }
        }

        return $commands;
    }

    /**
     * @return array<int, string>
     * @throws ReflectionException
     */
    private function getCommands(): array
    {
        $classes = get_declared_classes();
        $endpoints = [];

        foreach ($classes as $class) {
            $reflectedClass = new ReflectionClass($class);
            if(!$reflectedClass->isAbstract() && $reflectedClass->isSubclassOf( Command::class)) {
                $endpoints[] = $class;
            }
        }

        return $endpoints;
    }
}
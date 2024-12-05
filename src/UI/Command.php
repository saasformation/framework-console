<?php declare(strict_types=1);

namespace SaaSFormation\Framework\Console\UI;

use League\CLImate\CLImate;

abstract readonly class Command
{
    public abstract function cliLine(): string;

    public abstract function description(): string;

    public abstract function execute(InputInterface $input, CLImate $output): int;
}
<?php declare(strict_types=1);

use SaaSFormation\Framework\Console\Infrastructure\Console;
use SaaSFormation\Framework\Projects\Infrastructure\EnvVarsManagerProvider;
use SaaSFormation\Framework\Projects\Infrastructure\Kernel;
use SaaSFormation\Framework\Projects\Infrastructure\SymfonyContainerProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Kernel(
    new EnvVarsManagerProvider(__DIR__ . '/../config/vars.yaml'),
    new SymfonyContainerProvider(__DIR__ . '/../config/services/services.yaml')
);

(new Console($kernel))->run($argv);
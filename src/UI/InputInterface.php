<?php

namespace SaaSFormation\Framework\Console\UI;

interface InputInterface
{
    public function arguments(): CommandArgumentsCollection;

    public function options(): CommandOptionsCollection;
}
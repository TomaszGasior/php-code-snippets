<?php

namespace App;

use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    final public function __construct(protected readonly Config $config)
    {
        parent::__construct();
    }
}

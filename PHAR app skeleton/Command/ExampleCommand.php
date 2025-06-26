<?php

namespace App\Command;

use App\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleCommand extends Command
{
    use LockableTrait;

    protected function configure()
    {
        $this
            ->setName('print-example')
            ->setDescription('This is an example command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock('example')) {
            $output->writeln('This command is already running in another process.');

            return 1;
        }

        dump($this->config->get('element'));

        return 0;
    }
}

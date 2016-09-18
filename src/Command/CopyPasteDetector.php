<?php

namespace Webs\QA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class Command extends BaseCommand
{
    protected function configure()
    {
        $this->setName('qa:copy-paste-detector');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Verificando por preguiça crônica...');
    }
}

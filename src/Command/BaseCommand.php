<?php

namespace SymEnvSync\SymfonyEnvSync\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    private $projectDir;

    /**
     * BaseCommand constructor.
     * @param string|null $projectDir
     */
    public function __construct(string $projectDir = null)
    {
        parent::__construct();
        $this->projectDir = $projectDir;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    public function getSrcAndDest(InputInterface $input, OutputInterface $output): array
    {
        if ($input->getOption('src') !== null || $input->getOption('dest') !== null) {
            if ($input->getOption('src') === null || $input->getOption('dest') === null) {
                $output->writeln('You must use either both src and dest arguments, or none.');
                exit(1);
            }
        }

        $src = $input->getOption('src') ?: $this->projectDir . '/.env.dist';
        $dest = $input->getOption('dest') ?: $this->projectDir. '/.env';

        return [$src, $dest];
    }

    protected function configure(): void
    {
        $this
            ->addOption('src', null,InputOption::VALUE_OPTIONAL, 'Source env file')
            ->addOption('dest', null,InputOption::VALUE_OPTIONAL, 'Destination env file')
            ->addOption('reverse',null,InputOption::VALUE_OPTIONAL, 'Revers source and destination env files');
    }
}
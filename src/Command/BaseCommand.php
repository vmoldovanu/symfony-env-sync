<?php

namespace SymEnvSync\SymfonyEnvSync\Command;

use SymEnvSync\SymfonyEnvSync\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{
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

        $src = $input->getOption('src') ?: $this->getProjectDir() . '/.env.dist';
        $dest = $input->getOption('dest') ?: $this->getProjectDir(). '/.env';

        return [$src, $dest];
    }

    protected function configure(): void
    {
        $this
            ->addOption('src', null,InputOption::VALUE_OPTIONAL, 'Source env file')
            ->addOption('dest', null,InputOption::VALUE_OPTIONAL, 'Destination env file')
            ->addOption('reverse',null,InputOption::VALUE_OPTIONAL, 'Revers source and destination env files');
    }

    /**
     * Gets the application root dir (path of the project's composer file).
     *
     * @return string The project root dir
     */
    public function getProjectDir(): string
    {
        return (new Kernel('prod', false))->getProjectDir();
    }
}
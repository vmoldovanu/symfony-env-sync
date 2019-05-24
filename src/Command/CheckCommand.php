<?php

namespace SymEnvSync\SymfonyEnvSync\Command;

use SymEnvSync\SymfonyEnvSync\FileNotFound;
use SymEnvSync\SymfonyEnvSync\SyncService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends BaseCommand
{
    protected static $defaultName = 'env:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if your envs files are in sync';

    /**
     * @var SyncService
     */
    private $sync;

    /**
     * Create a new command instance.
     *
     * @param SyncService $sync
     */
    public function __construct(SyncService $sync)
    {
        parent::__construct();
        $this->sync = $sync;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('Check if your envs files are in sync');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        [$src, $dest] = $this->getSrcAndDest($input, $output);

        if ($input->getOption('reverse')) {
            $switch = $src;
            $src = $dest;
            $dest = $switch;
            unset($switch);
        }

        try {
            $diffs = $this->sync->getDiff($src, $dest);

            if (count($diffs) === 0) {
                $output->writeln(sprintf('Your %s file is already in sync with %s', basename($src), basename($dest)));
                return 0;
            }

            $output->writeln(sprintf('The following variables are not present in your %s file : ', basename($dest)));
            foreach ($diffs as $key => $diff) {
                $output->writeln(sprintf("\t- %s = %s", $key, $diff));
            }

            $output->writeln(sprintf('You can use `bin/console env:sync%s` to synchronise them', $input->getOption('reverse') ? ' --reverse' : ''));

            return 1;
        } catch (FileNotFound $e) {

        }
    }
}

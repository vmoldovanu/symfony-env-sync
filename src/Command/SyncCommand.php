<?php

namespace SymEnvSync\SymfonyEnvSync\Command;

use SymEnvSync\SymfonyEnvSync\Exception\FileNotFound;
use SymEnvSync\SymfonyEnvSync\Service\SyncService;
use SymEnvSync\SymfonyEnvSync\Writer\WriterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class SyncCommand extends BaseCommand
{
    private const YES = 'y';
    private const NO = 'n';
    private const CHANGE = 'c';

    protected static $defaultName = 'env:sync';

    /**
     * @var SyncService
     */
    private $sync;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * Create a new command instance.
     *
     * @param SyncService $sync
     * @param WriterInterface $writer
     * @param string|null $projectDir
     */
    public function __construct(SyncService $sync, WriterInterface $writer, string $projectDir = null)
    {
        parent::__construct($projectDir);
        $this->sync = $sync;
        $this->writer = $writer;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('Synchronise the .env & .env.example files');
    }

    /**
     * Execute the console command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     * @throws FileNotFound
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        [$src, $dest] = $this->getSrcAndDest($input, $output);

        if ($input->getOption('reverse')) {
            $switch = $src;
            $src = $dest;
            $dest = $switch;
            unset($switch);
        }

        $forceCopy = $input->getOption('no-interaction');
        if ($forceCopy) {
            $output->writeln('--no-interaction flag detected - will copy all new keys');
        }

        $diffs = $this->sync->getDiff($src, $dest);
        $helper = $this->getHelper('question');

        foreach ($diffs as $key => $diff) {
            $action = self::YES;
            if (!$forceCopy) {
                $question = sprintf("'%s' is not present into your %s file. Its default value is '%s'. Would you like to add it ?", $key, basename($dest), $diff);

                $action = new ChoiceQuestion(
                    $question,
                    [
                        self::YES => 'Copy the default value',
                        self::CHANGE => 'Change the default value',
                        self::NO => 'Skip'
                    ],
                    self::YES
                );

                $action->setErrorMessage('Action %s is invalid.');

                $action = $helper->ask($input, $output, $action);
            }

            if ($action === self::NO) {
                continue;
            }

            if ($action === self::CHANGE) {
                $question = new Question(sprintf("Please choose a value for '%s' : ", $key), $diff);
                $diff = $helper->ask($input, $output, $question);
            }

            $this->writer->append($dest, $key, $diff);
        }

        $output->writeln($dest . ' is now synced with ' . $src);
    }
}

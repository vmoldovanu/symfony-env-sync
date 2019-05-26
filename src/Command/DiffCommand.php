<?php

namespace SymEnvSync\SymfonyEnvSync\Command;

use SymEnvSync\SymfonyEnvSync\Reader\ReaderInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiffCommand extends BaseCommand
{
    protected static $defaultName = 'env:diff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the difference between env files';

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * Create a new command instance.
     * @param ReaderInterface $reader
     * @param string|null $projectDir
     */
    public function __construct(ReaderInterface $reader, string $projectDir = null)
    {
        parent::__construct($projectDir);
        $this->reader = $reader;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('Show the difference between env files');
    }

    /**
     * Execute the console command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        [$src, $dest] = $this->getSrcAndDest($input, $output);

        $envValues = $this->reader->read($dest);
        $distValues = $this->reader->read($src);

        $keys = array_unique(array_merge(array_keys($envValues), array_keys($distValues)));
        sort($keys);

        $header = ['Key', basename($dest), basename($src)];
        $lines = [];
        foreach ($keys as $key) {
            $envVal = $envValues[$key] ?? '<error>NOT FOUND</error>';
            $distVal = $distValues[$key] ?? '<error>NOT FOUND</error>';
            $lines[] = [$key, $envVal, $distVal];
        }

        $table = new Table($output);
        $table
            ->setHeaders($header)
            ->setRows($lines)
        ;
        $table->render();
    }
}

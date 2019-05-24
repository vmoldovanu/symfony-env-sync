<?php

namespace SymEnvSync\SymfonyEnvSync;


use SymEnvSync\SymfonyEnvSync\Reader\ReaderInterface;

class SyncService
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param $source
     * @param $destination
     * @return array
     * @throws FileNotFound
     */
    public function getDiff($source, $destination): array
    {
        $this->ensureFileExists($source, $destination);

        $sourceValues = $this->reader->read($source);
        $destinationValues = $this->reader->read($destination);

        return array_diff_key($sourceValues, $destinationValues);
    }

    private function ensureFileExists(...$files): void
    {
        foreach ($files as $file) {
            if (!file_exists($file)) {
                throw new FileNotFound(sprintf('%s must exists', $file));
            }
        }
    }
}

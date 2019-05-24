<?php

namespace SymEnvSync\SymfonyEnvSync\Reader\File;

use SymEnvSync\SymfonyEnvSync\Reader\ReaderInterface;
use Symfony\Component\Dotenv\Dotenv;

class EnvFileReader implements ReaderInterface
{
    /**
     * Load `.env` file in given directory.
     *
     * @param string $resource
     *
     * @return array
     *
     * @throws FileRequired
     */
    public function read($resource = null)
    {
        if ($resource === null) {
            throw new FileRequired();
        }
        $dotenv = new Dotenv();
        return $dotenv->parse(file_get_contents($resource), $resource);
    }
}

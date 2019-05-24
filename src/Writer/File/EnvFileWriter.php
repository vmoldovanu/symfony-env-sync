<?php

namespace SymEnvSync\SymfonyEnvSync\Writer\File;

use SymEnvSync\SymfonyEnvSync\Writer\WriterInterface;

class EnvFileWriter implements WriterInterface
{
    /**
     * Append a new par of key/value to an env resource
     *
     * @param string|null $resource resource where is located the env content
     * @param $key
     * @param $value
     */
    public function append($resource, $key, $value)
    {
        $lastChar = substr(file_get_contents($resource), -1);

        $prefix = '';
        if ($lastChar !== "\n" && $lastChar !== "\r" && strlen($lastChar) === 1) {
            $prefix = PHP_EOL;
        }

        if (strpos($value, ' ') !== false && strpos($value, '"') === false) {
            $value = '"' . $value . '"';
        }

        file_put_contents($resource, $prefix . $key . '=' . $value, FILE_APPEND);
    }
}

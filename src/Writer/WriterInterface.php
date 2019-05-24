<?php

namespace SymEnvSync\SymfonyEnvSync\Writer;

interface WriterInterface
{
    /**
     * Append a new par of key/value to an env resource
     *
     * @param string|null $resource resource where is located the env content
     * @param $key
     * @param $value
     */
    public function append($resource, $key, $value);
}
<?php

use PHPUnit\Framework\TestCase;
use SymEnvSync\SymfonyEnvSync\Writer\File\EnvFileWriter;
use SymEnvSync\SymfonyEnvSync\Writer\WriterInterface;

class EnvFileWriterTest extends TestCase
{
    /**
     * @var WriterInterface
     */
    private $writer;

    private $root;

    protected function setUp(): void
    {
        $this->writer = new EnvFileWriter();
        $this->root = __DIR__ . '/../../..';
    }

    public function testAppendContentToFile(): void
    {
        // Arrange
        $filePath = $this->root . '/.env';
        $lines = [
            'test=foo',
            'foo=baz',
        ];
        file_put_contents($filePath, implode(PHP_EOL, $lines));

        // Act
        $this->writer->append($filePath, 'phpunit', 'rocks hard');

        // Assert
        $lines = file($filePath);
        $this->assertEquals([
            "test=foo\n",
            "foo=baz\n",
            'phpunit="rocks hard"'
        ], $lines);
    }
}
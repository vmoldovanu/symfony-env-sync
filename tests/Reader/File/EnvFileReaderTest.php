<?php

use PHPUnit\Framework\TestCase;
use SymEnvSync\SymfonyEnvSync\Reader\File\EnvFileReader;
use SymEnvSync\SymfonyEnvSync\Reader\File\FileRequired;
use SymEnvSync\SymfonyEnvSync\Reader\ReaderInterface;

class EnvFileReaderTest extends TestCase
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    private $root;

    protected function setUp(): void
    {
        $this->reader = new EnvFileReader();
        $this->root = __DIR__ . '/../../..';
    }

    public function testReturnArrayFromFileContent(): void
    {
        // Arrange
        $filePath = $this->root . '/.env';
        file_put_contents($filePath, <<<TAG
APP_SECRET=FOO
APP_TEST=BAR
# COMMENT
TEST=ZOO
TAG
        );
        // Act
        $result = $this->reader->read($filePath);
        // Assert
        $this->assertEquals([
            'APP_SECRET' => 'FOO',
            'APP_TEST' => 'BAR',
            'TEST' => 'ZOO',
        ], $result);
    }

    public function testThrowExceptionWhenNoFilePassed(): void
    {
        $this->expectException(FileRequired::class);
        // Act
        $this->reader->read();
    }
}
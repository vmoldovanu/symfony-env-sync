<?php

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SymEnvSync\SymfonyEnvSync\Reader\ReaderInterface;
use SymEnvSync\SymfonyEnvSync\Service\SyncService;

class SyncServiceTest extends TestCase
{
    /** @var ReaderInterface|MockObject $readerInterface */
    private $readerInterface;

    private $syncService;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        // prepare mocks
        $this->readerInterface = $this->createMock(ReaderInterface::class);
        $this->syncService = $this->createMock(SyncService::class);
    }

    public function testReturnTheDifferenceBetweenFiles(): void
    {
        $root = __DIR__ . '/../..';
        $source = $root . '/.source';
        $destination = $root . '/.dest';
        $this->readerInterface->expects($this->exactly(2))->method('read')->willReturn([
                'foo' => 'bar',
                'bar' => 'baz',
                'baz' => 'foo'
            ],[
                'foo' => 'bar',
                'baz' => 'foo',
            ]);
        $sync = new SyncService($this->readerInterface);
        $result = $sync->getDiff($source, $destination);

        $this->assertEquals([
            'bar' => 'baz'
        ], $result);
    }
}
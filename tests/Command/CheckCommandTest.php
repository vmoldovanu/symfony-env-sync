<?php

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SymEnvSync\SymfonyEnvSync\Command\CheckCommand;
use SymEnvSync\SymfonyEnvSync\Kernel;
use SymEnvSync\SymfonyEnvSync\Reader\File\EnvFileReader;
use SymEnvSync\SymfonyEnvSync\SyncService;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CheckCommandTest extends TestCase
{
    /** @var SyncService|MockObject */
    private $syncService;

    private $root;

    /** @var CommandTester */
    private $commandTester;

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();

        $reader = new EnvFileReader();
        $this->syncService = new SyncService($reader);
//        $this->root = __DIR__ . '/../..';
        $this->root = (new Kernel('prod', false))->getProjectDir();
        $application = new Application();
        $application->add(new CheckCommand($this->syncService));
        $command = $application->find('env:check');
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->commandTester = null;
    }

    public function testKeysAreInBothFiles(): void
    {
        // Arrange
        $dist = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $env = "BAR=BAZ\nFOO=BAR\nBAZ=FOO";
        file_put_contents($this->root . '/.env.dist', $dist);
        file_put_contents($this->root . '/.env', $env);

        // Act
        $this->commandTester->execute([]);

        // Assert
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testFilesAreDifferent(): void
    {
        // Arrange
        $dist = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $env = "FOO=BAR\nBAZ=FOO";
        file_put_contents($this->root . '/.env.dist', $dist);
        file_put_contents($this->root . '/.env', $env);

        // Act
        $this->commandTester->execute([]);

        // Assert
        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testReverseMode()
    {
        // Arrange
        $env = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $dist = "FOO=BAR\nBAZ=FOO";
        file_put_contents($this->root . '/.env.dist', $dist);
        file_put_contents($this->root . '/.env', $env);

        // Act
        $this->commandTester->execute([
            '--reverse' => true
        ]);

        // Assert
        $this->assertSame(1, $this->commandTester->getStatusCode());
    }
}
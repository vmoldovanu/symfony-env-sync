<?php

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SymEnvSync\SymfonyEnvSync\Command\SyncCommand;
use SymEnvSync\SymfonyEnvSync\Kernel;
use SymEnvSync\SymfonyEnvSync\Reader\File\EnvFileReader;
use SymEnvSync\SymfonyEnvSync\SyncService;
use SymEnvSync\SymfonyEnvSync\Writer\File\EnvFileWriter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SyncCommandTest extends TestCase
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

        $writer = new EnvFileWriter();
        $reader = new EnvFileReader();
        $this->syncService = new SyncService($reader);
        $this->root = (new Kernel('prod', false))->getProjectDir();
        $application = new Application();
        $application->add(new SyncCommand($this->syncService, $writer));
        $command = $application->find('env:sync');
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->commandTester = null;
    }
    
    public function testFillTheEnvFileFromEnvExample(): void
    {
        // Arrange
        $dist = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $env = "FOO=BAR\nBAZ=FOO";
        file_put_contents($this->root . '/.env.dist', $dist);
        file_put_contents($this->root . '/.env', $env);

        // Act
        $this->commandTester->execute([
            '--no-interaction' => true
        ]);

        // Assert
        $expected = "FOO=BAR\nBAZ=FOO\nBAR=BAZ";
        $this->assertEquals($expected, file_get_contents($this->root . '/.env'));
    }

    public function testWorkInReverseMode(): void
    {
        // Arrange
        $env = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $dist = "FOO=BAR\nBAZ=FOO";
        file_put_contents($this->root . '/.env.dist', $dist);
        file_put_contents($this->root . '/.env', $env);

        // Act
        $this->commandTester->execute([
            '--no-interaction' => true,
            '--reverse' => true
        ]);

        // Assert
        $expected = "FOO=BAR\nBAZ=FOO\nBAR=BAZ";
        $this->assertEquals($expected, file_get_contents($this->root . '/.env.dist'));
    }

    public function testWorkWhenProvidingSrcAndDest(): void
    {
        // Arrange
        $dist = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $env = "FOO=BAR\nBAZ=FOO";
        file_put_contents($this->root . '/.foo', $dist);
        file_put_contents($this->root . '/.bar', $env);

        // Act
        $this->commandTester->execute([
            '--no-interaction' => true,
            '--src' => $this->root .'/.foo',
            '--dest' => $this->root .'/.bar'
        ]);

        // Assert
        $expected = "FOO=BAR\nBAZ=FOO\nBAR=BAZ";
        $this->assertEquals($expected, file_get_contents($this->root . '/.bar'));
    }
}
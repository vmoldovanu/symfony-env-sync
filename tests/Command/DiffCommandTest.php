<?php

use PHPUnit\Framework\TestCase;
use SymEnvSync\SymfonyEnvSync\Command\DiffCommand;
use SymEnvSync\SymfonyEnvSync\Reader\File\EnvFileReader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DiffCommandTest extends TestCase
{
    private $root;

    /** @var CommandTester */
    private $commandTester;

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();
        $reader = new EnvFileReader();
        $this->root = __DIR__ . '/../..';
        $application = new Application();
        $application->add(new DiffCommand($reader, $this->root));
        $command = $application->find('env:diff');
        $this->commandTester = new CommandTester($command);
    }

    public function testFillTheEnvFileFromEnvExample(): void
    {
        // Arrange
        $example = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
        $env = "FOO=BAR\nBAZ=FOO";
        file_put_contents($this->root . '/.env.dist', $example);
        file_put_contents($this->root . '/.env', $env);

        // Act
        $this->commandTester->execute([]);

        // Assert
        $expected = <<<TABLE
+-----+-----------+-----------+
| Key | .env      | .env.dist |
+-----+-----------+-----------+
| BAR | NOT FOUND | BAZ       |
| BAZ | FOO       | FOO       |
| FOO | BAR       | BAR       |
+-----+-----------+-----------+

TABLE;
        $this->assertEquals($expected, $this->commandTester->getDisplay());
    }

    protected function tearDown(): void
    {
        $this->commandTester = null;
    }
}
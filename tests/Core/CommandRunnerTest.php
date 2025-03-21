<?php

namespace MulerTech\MTerm\Tests\Core;

use MulerTech\MTerm\Core\CommandRunner;
use PHPUnit\Framework\TestCase;

class CommandRunnerTest extends TestCase
{
    private CommandRunner $commandRunner;

    protected function setUp(): void
    {
        $this->commandRunner = new CommandRunner();
    }

    public function testRunWithSuccessfulCommand(): void
    {
        $result = $this->commandRunner->run('echo "Hello World"');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('output', $result);
        $this->assertArrayHasKey('returnCode', $result);
        $this->assertEquals(0, $result['returnCode']);

        // Output may vary by platform, check it contains "Hello World"
        $this->assertStringContainsString('Hello World', implode(' ', $result['output']));
    }

    public function testRunWithFailingCommand(): void
    {
        $result = $this->commandRunner->run('command_that_does_not_exist_123456789');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('output', $result);
        $this->assertArrayHasKey('returnCode', $result);
        $this->assertNotEquals(0, $result['returnCode']);
    }

    public function testRunWithStderrSuccessfulCommand(): void
    {
        $result = $this->commandRunner->runWithStderr('echo "Hello World"');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('stdout', $result);
        $this->assertArrayHasKey('stderr', $result);
        $this->assertArrayHasKey('returnCode', $result);
        $this->assertStringContainsString('Hello World', $result['stdout']);
        $this->assertEquals('', $result['stderr']);
        $this->assertEquals(0, $result['returnCode']);
    }

    public function testRunWithStderrFailingCommand(): void
    {
        $command = DIRECTORY_SEPARATOR === '/' ? 'ls /nonexistentdirectory' : 'dir /nonexistentdirectory';
        $result = $this->commandRunner->runWithStderr($command);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('stdout', $result);
        $this->assertArrayHasKey('stderr', $result);
        $this->assertArrayHasKey('returnCode', $result);
        $this->assertNotEquals(0, $result['returnCode']);
        $this->assertNotEmpty($result['stderr']);
    }
}
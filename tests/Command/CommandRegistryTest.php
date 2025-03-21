<?php

namespace MulerTech\MTerm\Tests\Command;

use InvalidArgumentException;
use MulerTech\MTerm\Command\CommandInterface;
use MulerTech\MTerm\Command\CommandRegistry;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CommandRegistryTest extends TestCase
{
    private CommandRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new CommandRegistry();
    }

    /**
     * @throws Exception
     */
    public function testRegisterAddsCommandAndReturnsSelf(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $command->method('getName')->willReturn('test-command');

        $result = $this->registry->register($command);

        $this->assertSame($this->registry, $result);
        $this->assertTrue($this->registry->has('test-command'));
    }

    /**
     * @throws Exception
     */
    public function testHasReturnsTrueForRegisteredCommand(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $command->method('getName')->willReturn('test-command');

        $this->registry->register($command);

        $this->assertTrue($this->registry->has('test-command'));
        $this->assertFalse($this->registry->has('non-existent-command'));
    }

    /**
     * @throws Exception
     */
    public function testGetReturnsCorrectCommandForRegisteredName(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $command->method('getName')->willReturn('test-command');

        $this->registry->register($command);

        $this->assertSame($command, $this->registry->get('test-command'));
        $this->assertNull($this->registry->get('non-existent-command'));
    }

    /**
     * @throws Exception
     */
    public function testGetAllReturnsAllRegisteredCommands(): void
    {
        $command1 = $this->createMock(CommandInterface::class);
        $command1->method('getName')->willReturn('command-1');

        $command2 = $this->createMock(CommandInterface::class);
        $command2->method('getName')->willReturn('command-2');

        $this->registry->register($command1);
        $this->registry->register($command2);

        $commands = $this->registry->getAll();

        $this->assertCount(2, $commands);
        $this->assertSame($command1, $commands['command-1']);
        $this->assertSame($command2, $commands['command-2']);
    }

    /**
     * @throws Exception
     */
    public function testExecuteCallsCommandWithCorrectArguments(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $command->method('getName')->willReturn('test-command');
        $command->expects($this->once())
            ->method('execute')
            ->with($this->equalTo(['arg1', 'arg2']))
            ->willReturn(0);

        $this->registry->register($command);
        $result = $this->registry->execute('test-command', ['arg1', 'arg2']);

        $this->assertEquals(0, $result);
    }

    public function testExecuteThrowsExceptionForNonExistentCommand(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Command 'non-existent' not found");

        $this->registry->execute('non-existent');
    }
}
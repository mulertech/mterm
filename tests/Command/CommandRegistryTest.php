<?php

namespace MulerTech\MTerm\Tests\Command;

use InvalidArgumentException;
use MulerTech\MTerm\Command\CommandInterface;
use MulerTech\MTerm\Command\CommandRegistry;
use MulerTech\MTerm\Command\HelpCommand;
use MulerTech\MTerm\Core\Terminal;
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

        $this->assertCount(3, $commands);
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

    /**
     * @throws Exception
     */
    public function testHelpCommandConstructorSetsNameAndDescription(): void
    {
        $terminal = $this->createMock(Terminal::class);
        $registry = new CommandRegistry();
        
        $helpCommand = new HelpCommand($terminal, $registry);
        
        $this->assertEquals('help', $helpCommand->getName());
        $this->assertEquals('Display help information about available commands', $helpCommand->getDescription());
    }

    /**
     * @throws Exception
     */
    public function testHelpCommandShowAllCommands(): void
    {
        $terminal = $this->createMock(Terminal::class);
        $registry = new CommandRegistry();
        
        // Mock commands for testing
        $command1 = $this->createMock(CommandInterface::class);
        $command1->method('getName')->willReturn('command1');
        $command1->method('getDescription')->willReturn('Command 1 description');
        
        $command2 = $this->createMock(CommandInterface::class);
        $command2->method('getName')->willReturn('command2');
        $command2->method('getDescription')->willReturn('Command 2 description');
        $command2->method('showHelp');
        
        $registry->register($command1);
        $registry->register($command2);
        
        // Terminal expectations
        $terminal->expects($this->exactly(8))
            ->method('writeLine');
            
        $terminal->expects($this->exactly(3))
            ->method('write');
        
        $helpCommand = new HelpCommand($terminal, $registry);
        $result = $helpCommand->execute();
        
        $this->assertEquals(0, $result);
    }

    /**
     * @throws Exception
     */
    public function testHelpCommandShowSpecificCommandHelp(): void
    {
        $terminal = $this->createMock(Terminal::class);
        $registry = new CommandRegistry();
        
        $testCommand = $this->createMock(CommandInterface::class);
        $testCommand->method('getName')->willReturn('test');
        $testCommand->method('getDescription')->willReturn('Test command description');
        
        $registry->register($testCommand);
        
        // Terminal expectations for successful command help
        $terminal->expects($this->exactly(6))
            ->method('writeLine');
            
        $helpCommand = new HelpCommand($terminal, $registry);
        $result = $helpCommand->execute(['test']);
        
        $this->assertEquals(0, $result);
    }

    /**
     * @throws Exception
     */
    public function testHelpCommandShowNonExistentCommandHelp(): void
    {
        $terminal = $this->createMock(Terminal::class);
        $registry = new CommandRegistry();
        
        // Terminal expectations for non-existent command
        $terminal->expects($this->once())
            ->method('writeLine')
            ->with("Command 'non-existent' not found", 'red');
        
        $helpCommand = new HelpCommand($terminal, $registry);
        $result = $helpCommand->execute(['non-existent']);
        
        $this->assertEquals(1, $result);
    }

    /**
     * @throws Exception
     */
    public function testHelpCommandIsAutoRegistered(): void
    {
        $registry = new CommandRegistry();
        
        // Register any command to trigger help auto-registration
        $command = $this->createMock(CommandInterface::class);
        $command->method('getName')->willReturn('test-command');
        $registry->register($command);
        
        // Verify help command is registered
        $this->assertTrue($registry->has('help'));
        $helpCommand = $registry->get('help');
        $this->assertInstanceOf(HelpCommand::class, $helpCommand);
    }
}

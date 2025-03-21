<?php

namespace MulerTech\MTerm\Tests\Command;

use MulerTech\MTerm\Command\AbstractCommand;
use MulerTech\MTerm\Core\Terminal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

class AbstractCommandTest extends TestCase
{
    private Terminal $terminal;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->terminal = $this->createMock(Terminal::class);
    }

    /**
     * @throws ReflectionException
     */
    public function testNameIsReturnedCorrectly(): void
    {
        // Create a concrete implementation of the abstract class
        $command = new class($this->terminal) extends AbstractCommand {
            public function execute(array $args = []): int
            {
                return 0;
            }
        };

        $reflection = new ReflectionProperty($command, 'name');
        $reflection->setValue($command, 'test-command');

        $this->assertEquals('test-command', $command->getName());
    }

    /**
     * @throws ReflectionException
     */
    public function testDescriptionIsReturnedCorrectly(): void
    {
        $command = new class($this->terminal) extends AbstractCommand {
            public function execute(array $args = []): int
            {
                return 0;
            }
        };

        $reflection = new ReflectionProperty($command, 'description');
        $reflection->setValue($command, 'Test command description');

        $this->assertEquals('Test command description', $command->getDescription());
    }

    /**
     * @throws ReflectionException
     */
    public function testShowHelpDisplaysNameAndDescription(): void
    {
        $command = new class($this->terminal) extends AbstractCommand {
            public function execute(array $args = []): int
            {
                return 0;
            }
        };

        $reflection = new ReflectionProperty($command, 'name');
        $reflection->setValue($command, 'test-command');

        $descReflection = new ReflectionProperty($command, 'description');
        $descReflection->setValue($command, 'Test command description');

        $this->terminal->expects($this->once())
            ->method('writeLine')
            ->with(
                $this->equalTo('test-command: Test command description'),
                $this->equalTo('cyan')
            );

        $command->showHelp();
    }
}
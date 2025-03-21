<?php

namespace MulerTech\MTerm\Tests\Core;

use MulerTech\MTerm\Core\Application;
use MulerTech\MTerm\Core\CommandRunner;
use MulerTech\MTerm\Core\Terminal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

class ApplicationTest extends TestCase
{
    protected function setUp(): void
    {
        // Réinitialiser l'instance singleton
        $reflection = new ReflectionProperty(Application::class, 'instance');
        $reflection->setValue(null, null);
    }

    protected function tearDown(): void
    {
        // Réinitialiser l'instance singleton après chaque test
        $reflection = new ReflectionProperty(Application::class, 'instance');
        $reflection->setValue(null, null);
    }

    public function testGetInstanceReturnsSingletonInstance(): void
    {
        $instance1 = Application::getInstance();
        $instance2 = Application::getInstance();

        $this->assertInstanceOf(Application::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    public function testGetTerminalReturnsTerminalInstance(): void
    {
        $application = Application::getInstance();
        $terminal = $application->getTerminal();

        $this->assertInstanceOf(Terminal::class, $terminal);
    }

    public function testGetCommandRunnerReturnsCommandRunnerInstance(): void
    {
        $application = Application::getInstance();
        $commandRunner = $application->getCommandRunner();

        $this->assertInstanceOf(CommandRunner::class, $commandRunner);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testRunDisplaysStartMessage(): void
    {
        $terminalMock = $this->createMock(Terminal::class);
        $terminalMock->expects($this->once())
            ->method('writeLine')
            ->with("MTerm Application Started", "green");

        $application = Application::getInstance();

        // Inject mock terminal
        $reflection = new ReflectionProperty($application, 'terminal');
        $reflection->setValue($application, $terminalMock);

        $application->run();
    }
}
<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\PasswordField;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PasswordFieldTest extends TestCase
{
    private PasswordField $field;
    private Terminal $terminal;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->terminal = $this->createMock(Terminal::class);
        $this->field = new PasswordField('password', 'Password');
        $this->field->setTerminal($this->terminal);
    }

    public function testDefaultValues(): void
    {
        $this->assertTrue($this->field->isMaskInput());
        $this->assertEquals('*', $this->field->getMaskChar());
    }

    public function testSetMaskInput(): void
    {
        // Test disabling mask
        $result = $this->field->setMaskInput(false);
        $this->assertSame($this->field, $result);
        $this->assertFalse($this->field->isMaskInput());

        // Test enabling mask
        $this->field->setMaskInput();
        $this->assertTrue($this->field->isMaskInput());
    }

    public function testSetMaskChar(): void
    {
        $result = $this->field->setMaskChar('•');
        $this->assertSame($this->field, $result);
        $this->assertEquals('•', $this->field->getMaskChar());
    }

    public function testProcessInputWithoutMaskInput(): void
    {
        $this->field->setMaskInput(false)->setDefault('secret');
        $result = $this->field->processInput();
        $this->assertEquals('secret', $result);
    }

    public function testProcessInputWithoutTerminalSet(): void
    {
        $field = new PasswordField('password', 'Password');
        $field->setDefault('secret');
        $this->expectException(RuntimeException::class);
        $field->processInput();
    }

    public function testProcessInputWithNonEmptyValue(): void
    {
        $this->terminal
            ->method('write')
            ->withAnyParameters();
        $this->terminal->expects($this->once())->method('specialMode');
        $this->terminal
            ->method('readChar')
            // Simulate deleting c character
            ->willReturnOnConsecutiveCalls('s', 'e', 'c', "\x7F", 'r', 'e', 't', PHP_EOL);
        $this->terminal->expects($this->once())->method('writeLine');
        $this->terminal->expects($this->once())->method('normalMode');

        $result = $this->field->processInput('secret');
        $this->assertEquals('seret', $result);
    }

    public function testProcessInputWithEmptyValue(): void
    {
        $this->terminal
            ->method('write')
            ->withAnyParameters();
        $this->terminal->expects($this->exactly(2))->method('specialMode');
        $this->terminal
            ->method('readChar')
            ->willReturn(PHP_EOL);
        $this->terminal->expects($this->exactly(2))->method('writeLine');
        $this->terminal->expects($this->exactly(2))->method('normalMode');

        // Test with no default value
        $result = $this->field->processInput();
        $this->assertEquals('', $result);
        $this->field->setDefault('default');
        $result = $this->field->processInput();
        $this->assertEquals('default', $result);
    }

    public function testInheritedValidation(): void
    {
        // Test that validation is inherited from TextField
        $this->field->setRequired();
        $errors = $this->field->validate('');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);

        // Test min length validation
        $this->field->clearErrors();
        $this->field->setMinLength(8);
        $errors = $this->field->validate('short');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('at least 8', $errors[0]);

        // Test max length validation
        $this->field->clearErrors();
        $this->field->setMaxLength(12);
        $errors = $this->field->validate('thispasswordistoolong');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('cannot exceed 12', $errors[0]);

        // Test valid password
        $this->field->clearErrors();
        $errors = $this->field->validate('valid-pass');
        $this->assertEmpty($errors);
    }
}

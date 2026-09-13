<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\PasswordField;
use MulerTech\MTerm\Tests\Support\TerminalDouble;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PasswordFieldTest extends TestCase
{
    private PasswordField $field;

    protected function setUp(): void
    {
        $this->field = new PasswordField('password', 'Password');
        $this->field->setTerminal((new TerminalDouble())->terminal);
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
        // The c character is deleted by the backspace
        $double = new TerminalDouble("sec\x7Fret\n");
        $field = new PasswordField('password', 'Password');
        $field->setTerminal($double->terminal);

        $this->assertEquals('seret', $field->processInput('secret'));
        $this->assertEquals("Password: ***\x08 \x08***".PHP_EOL, $double->display());
        $this->assertFalse($double->terminal->isRawMode());
    }

    public function testProcessInputWithEmptyValue(): void
    {
        $double = new TerminalDouble("\n");
        $field = new PasswordField('password', 'Password');
        $field->setTerminal($double->terminal);

        $this->assertEquals('', $field->processInput());

        $double = new TerminalDouble("\n");
        $field->setTerminal($double->terminal)->setDefault('default');

        $this->assertEquals('default', $field->processInput());
    }

    public function testAnAccentedPasswordIsReadWhole(): void
    {
        $double = new TerminalDouble("naïve\n");
        $field = new PasswordField('password', 'Password');
        $field->setTerminal($double->terminal);

        $this->assertEquals('naïve', $field->processInput());
    }

    public function testAnExhaustedInputEndsTheEntry(): void
    {
        $double = new TerminalDouble('ab');
        $field = new PasswordField('password', 'Password');
        $field->setTerminal($double->terminal);

        $this->assertEquals('ab', $field->processInput());
        $this->assertFalse($double->terminal->isRawMode());
    }

    public function testAControlCharacterIsNotAddedToThePassword(): void
    {
        $double = new TerminalDouble("a\x01b\n");
        $field = new PasswordField('password', 'Password');
        $field->setTerminal($double->terminal);

        $this->assertEquals('ab', $field->processInput());
    }

    public function testABackspaceOnAnEmptyPasswordChangesNothing(): void
    {
        $double = new TerminalDouble("\x7Fa\n");
        $field = new PasswordField('password', 'Password');
        $field->setTerminal($double->terminal);

        $this->assertEquals('a', $field->processInput());
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

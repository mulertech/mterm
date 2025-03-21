<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\PasswordField;
use PHPUnit\Framework\TestCase;

class PasswordFieldTest extends TestCase
{
    private PasswordField $field;

    protected function setUp(): void
    {
        $this->field = new PasswordField('password', 'Password');
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

    public function testProcessInputWithNonEmptyValue(): void
    {
        $result = $this->field->processInput('mysecretpassword');
        $this->assertEquals('mysecretpassword', $result);
    }

    public function testProcessInputWithEmptyValue(): void
    {
        // Test with no default value
        $result = $this->field->processInput('');
        $this->assertEquals('', $result);

        // Test with default value
        $this->field->setDefault('default_password');
        $result = $this->field->processInput('');
        $this->assertEquals('default_password', $result);
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
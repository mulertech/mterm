<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\SelectField;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

class SelectFieldTest extends TestCase
{
    private SelectField $field;

    protected function setUp(): void
    {
        $this->terminal = $this->createMock(Terminal::class);
        $this->field = new SelectField('select', 'Select Field');
        $this->field->setTerminal($this->terminal);
    }

    public function testSetOptions(): void
    {
        $options = [
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ];

        $result = $this->field->setOptions($options);
        $this->assertSame($this->field, $result);

        // Test validation with valid option
        $errors = $this->field->validate('opt2');
        $this->assertEmpty($errors);

        // Test validation with invalid option
        $errors = $this->field->validate('invalid');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid option', $errors[0]);
    }

    public function testSetMultipleSelection(): void
    {
        $result = $this->field->setMultipleSelection();
        $this->assertSame($this->field, $result);

        $reflection = new ReflectionClass($this->field);
        $property = $reflection->getProperty('multipleSelection');
        $this->assertTrue($property->getValue($this->field));
    }

    public function testValidateMultipleSelection(): void
    {
        $options = [
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ];

        $this->field->setOptions($options);
        $this->field->setMultipleSelection();

        // Test with valid options
        $errors = $this->field->validate(['opt1' => 'Option 1', 'opt3' => 'Option 3']);
        $this->assertEmpty($errors);

        // Test with mix of valid and invalid options
        $errors = $this->field->validate(['opt1' => 'Option 1', 'invalid' => 'Invalid', 'opt3' => 'Option 3']);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid option: \'invalid\'', $errors[0]);
    }

    public function testValidateWithEmptyValue(): void
    {
        $this->field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ]);

        $this->field->setRequired(false);
        $errors = $this->field->validate('');
        $this->assertEmpty($errors);

        $this->field->setRequired();
        $errors = $this->field->validate('');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);
    }

    public function testProcessInputWithoutTerminalSet(): void
    {
        $field = new SelectField('password', 'Password');
        $field->setDefault('secret');
        self::expectException(RuntimeException::class);
        $field->processInput();
    }

}
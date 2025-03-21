<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\RadioField;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class RadioFieldTest extends TestCase
{
    private RadioField $field;

    protected function setUp(): void
    {
        $this->field = new RadioField('choice', 'Choose option');
    }

    public function testInstanceCreation(): void
    {
        $this->assertInstanceOf(RadioField::class, $this->field);
    }

    public function testInheritedAddOption(): void
    {
        $result = $this->field->setOptions(['opt1' => 'Option 1', 'opt2' => 'Option 2', 'opt3' => 'Option 3']);
        $this->assertSame($this->field, $result);

        $errors = $this->field->validate('opt2');
        $this->assertEmpty($errors);
    }

    public function testSetDefaultValue(): void
    {
        $this->field->setOptions(['opt1' => 'Option 1', 'opt2' => 'Option 2']);

        $result = $this->field->setDefault('opt2');
        $this->assertSame($this->field, $result);

        // Test default value is used when input is empty
        $value = $this->field->processInput('');
        $this->assertEquals('o  Option 1' . PHP_EOL . '   Option 2' . PHP_EOL, $value);
    }

    public function testCursorCharacterIsSet(): void
    {
        // Use reflection to access the protected property
        $reflection = new ReflectionClass($this->field);
        $property = $reflection->getProperty('cursorPresent');

        $value = $property->getValue($this->field);
        $this->assertEquals('o', $value);
    }

    public function testValidateWithInvalidOption(): void
    {
        $this->field->setOptions(['opt1' => 'Option 1', 'opt2' => 'Option 2']);

        $errors = $this->field->validate('invalid_option');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid option', $errors[0]);
    }

    public function testValidateWithValidOption(): void
    {
        $this->field->setOptions(['opt1' => 'Option 1', 'opt2' => 'Option 2']);

        $errors = $this->field->validate('opt1');
        $this->assertEmpty($errors);
    }

    /**
     * @throws ReflectionException
     */
    public function testCannotBeSetToMultiple(): void
    {
        // RadioField is a single-select field by design
        // Attempting to set it to multiple should have no effect
        $field = $this->field->setMultipleSelection();

        // Use reflection to check if the field is actually multiple
        $reflection = new ReflectionClass($field);
        $property = $reflection->getProperty('multipleSelection');

        $value = $property->getValue($field);
        $this->assertFalse($value);
    }
}
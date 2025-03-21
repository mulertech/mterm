<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\SelectField;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SelectFieldTest extends TestCase
{
    private SelectField $field;

    protected function setUp(): void
    {
        $this->field = new SelectField('select', 'Select Field');
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

    public function testProcessInputSingleSelection(): void
    {
        $this->field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ]);

        // Initially selected option should be first one
        $this->assertEquals('opt1', $this->field->getCurrentOption());

        // Move cursor down
        $this->field->processInput('down');
        $this->assertEquals('opt2', $this->field->getCurrentOption());

        // Move cursor down again
        $this->field->processInput('down');
        $this->assertEquals('opt3', $this->field->getCurrentOption());

        // Move cursor down beyond limit (should stay at last option)
        $this->field->processInput('down');
        $this->assertEquals('opt3', $this->field->getCurrentOption());

        // Move cursor up
        $this->field->processInput('up');
        $this->assertEquals('opt2', $this->field->getCurrentOption());

        // Move cursor up beyond limit (should stay at first option)
        $this->field->processInput('up');
        $this->field->processInput('up');
        $this->assertEquals('opt1', $this->field->getCurrentOption());
    }

    public function testProcessInputMultipleSelection(): void
    {
        $this->field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ]);
        $this->field->setMultipleSelection();

        // Initially no options selected
        $this->assertEmpty($this->field->getSelectedOptions());

        // Select first option
        $this->field->processInput('space');
        $this->assertCount(1, $this->field->getSelectedOptions());
        $this->assertArrayHasKey('opt1', $this->field->getSelectedOptions());

        // Move down and select second option
        $this->field->processInput('down');
        $this->field->processInput('space');
        $this->assertCount(2, $this->field->getSelectedOptions());
        $this->assertArrayHasKey('opt2', $this->field->getSelectedOptions());

        // Deselect second option
        $this->field->processInput('space');
        $this->assertCount(1, $this->field->getSelectedOptions());
        $this->assertArrayNotHasKey('opt2', $this->field->getSelectedOptions());

        // Select all
        $this->field->processInput('a');
        $this->assertCount(3, $this->field->getSelectedOptions());
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
}
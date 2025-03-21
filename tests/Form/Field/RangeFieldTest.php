<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\RangeField;
use PHPUnit\Framework\TestCase;

class RangeFieldTest extends TestCase
{
    private RangeField $field;

    protected function setUp(): void
    {
        $this->field = new RangeField('range', 'Range Field');
    }

    public function testDefaultValues(): void
    {
        // Test default min value (0)
        $errors = $this->field->validate('-1');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value must be at least 0', $errors[0]);

        // Test default max value (100)
        $errors = $this->field->validate('101');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value cannot exceed 100', $errors[0]);

        // Test default step value (1)
        $this->assertEquals(1, $this->field->getStep());
    }

    public function testSetStep(): void
    {
        // Test fluent interface
        $result = $this->field->setStep(5);
        $this->assertSame($this->field, $result);
        $this->assertEquals(5, $this->field->getStep());

        // Test with negative value (should be set to 1)
        $this->field->setStep(-3);
        $this->assertEquals(1, $this->field->getStep());

        // Test with zero (should be set to 1)
        $this->field->setStep(0);
        $this->assertEquals(1, $this->field->getStep());
    }

    public function testValidateWithStep(): void
    {
        // Test with step=1 (default)
        $errors = $this->field->validate('42');
        $this->assertEmpty($errors);

        // Test with step=5
        $this->field->setStep(5);

        // Values that are multiples of 5
        $errors = $this->field->validate('0');
        $this->assertEmpty($errors);

        $errors = $this->field->validate('5');
        $this->assertEmpty($errors);

        $errors = $this->field->validate('100');
        $this->assertEmpty($errors);

        // Values that are not multiples of 5
        $errors = $this->field->validate('7');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value must be a multiple of 5', $errors[0]);

        $errors = $this->field->validate('98');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value must be a multiple of 5', $errors[0]);
    }

    public function testValidateWithNullOrEmptyValue(): void
    {
        // Non-required field should accept null/empty values
        $this->field->setRequired(false);
        $errors = $this->field->validate(null);
        $this->assertEmpty($errors);

        $errors = $this->field->validate('');
        $this->assertEmpty($errors);

        // Required field should not accept null/empty values
        $this->field->setRequired();
        $errors = $this->field->validate(null);
        $this->assertNotEmpty($errors);

        $errors = $this->field->validate('');
        $this->assertNotEmpty($errors);
    }

    public function testInheritedValidation(): void
    {
        // Test min/max validation from NumberField
        $this->field->setMin(10)->setMax(50);

        $errors = $this->field->validate('5');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value must be at least 10', $errors[0]);

        $errors = $this->field->validate('60');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value cannot exceed 50', $errors[0]);

        // Test float validation from NumberField
        $this->field->setAllowFloat(false);

        $errors = $this->field->validate('25.5');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Please enter an integer value', $errors[0]);
    }

    public function testProcessInput(): void
    {
        // Test inherited processInput from NumberField
        $result = $this->field->processInput('42');
        $this->assertSame(42.0, $result);
        $this->assertIsFloat($result);

        $this->field->setAllowFloat(false);
        $result = $this->field->processInput('42');
        $this->assertSame(42, $result);
        $this->assertIsInt($result);
    }
}
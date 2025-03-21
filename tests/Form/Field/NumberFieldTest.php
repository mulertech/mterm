<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\NumberField;
use PHPUnit\Framework\TestCase;

class NumberFieldTest extends TestCase
{
    private NumberField $field;

    protected function setUp(): void
    {
        $this->field = new NumberField('number', 'Number Field');
    }

    public function testSetMin(): void
    {
        $result = $this->field->setMin(5.0);
        $this->assertSame($this->field, $result);

        $errors = $this->field->validate('3');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value must be at least 5', $errors[0]);

        $errors = $this->field->validate('5');
        $this->assertEmpty($errors);

        $errors = $this->field->validate('10');
        $this->assertEmpty($errors);
    }

    public function testSetMax(): void
    {
        $result = $this->field->setMax(10.0);
        $this->assertSame($this->field, $result);

        $errors = $this->field->validate('15');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value cannot exceed 10', $errors[0]);

        $errors = $this->field->validate('10');
        $this->assertEmpty($errors);

        $errors = $this->field->validate('5');
        $this->assertEmpty($errors);
    }

    public function testSetMinAndMax(): void
    {
        $this->field->setMin(5.0)->setMax(10.0);

        $errors = $this->field->validate('3');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value must be at least 5', $errors[0]);

        $errors = $this->field->validate('15');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Value cannot exceed 10', $errors[0]);

        $errors = $this->field->validate('7');
        $this->assertEmpty($errors);
    }

    public function testAllowFloat(): void
    {
        // By default, floats are allowed
        $errors = $this->field->validate('7.5');
        $this->assertEmpty($errors);

        $this->field->setAllowFloat(false);

        $errors = $this->field->validate('7.5');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Please enter an integer value', $errors[0]);

        $errors = $this->field->validate('7');
        $this->assertEmpty($errors);
    }

    public function testProcessInput(): void
    {
        // Test with float allowed (default)
        $result = $this->field->processInput('7.5');
        $this->assertSame(7.5, $result);
        $this->assertIsFloat($result);

        $result = $this->field->processInput('7');
        $this->assertSame(7.0, $result);
        $this->assertIsFloat($result);

        // Test with float not allowed
        $this->field->setAllowFloat(false);

        $result = $this->field->processInput('7.5');
        $this->assertSame(7, $result);
        $this->assertIsInt($result);

        $result = $this->field->processInput('7');
        $this->assertSame(7, $result);
        $this->assertIsInt($result);
    }

    public function testProcessInputWithEmptyValue(): void
    {
        $this->field->setDefault(100);
        $result = $this->field->processInput('');
        $this->assertSame(100, $result);
    }

    public function testValidateNonNumericValue(): void
    {
        $errors = $this->field->validate('not a number');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Please enter a valid number', $errors[0]);
    }

    public function testValidateWithNullValue(): void
    {
        $this->field->setRequired(false);
        $errors = $this->field->validate(null);
        $this->assertEmpty($errors);

        $this->field->setRequired();
        $errors = $this->field->validate(null);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);
    }

    public function testValidateWithEmptyString(): void
    {
        $this->field->setRequired(false);
        $errors = $this->field->validate('');
        var_dump($errors);
        $this->assertEmpty($errors);

        $this->field->setRequired();
        $errors = $this->field->validate('');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);
    }
}
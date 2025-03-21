<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\ColorField;
use PHPUnit\Framework\TestCase;

class ColorFieldTest extends TestCase
{
    private ColorField $field;

    protected function setUp(): void
    {
        $this->field = new ColorField('color', 'Color');
    }

    public function testValidHexColorFormatsPassValidation(): void
    {
        // Test 6-digit hex format
        $this->assertEmpty($this->field->validate('#123456'));
        $this->assertEmpty($this->field->validate('#ABCDEF'));
        $this->assertEmpty($this->field->validate('#abcdef'));
        $this->assertEmpty($this->field->validate('#A1B2C3'));

        // Test 3-digit hex format
        $this->assertEmpty($this->field->validate('#123'));
        $this->assertEmpty($this->field->validate('#ABC'));
        $this->assertEmpty($this->field->validate('#abc'));
        $this->assertEmpty($this->field->validate('#A1B'));
    }

    public function testInvalidHexColorFormatsFailValidation(): void
    {
        // Missing hash prefix
        $this->assertNotEmpty($this->field->validate('123456'));

        // Invalid characters
        $this->assertNotEmpty($this->field->validate('#GHIJKL'));
        $this->assertNotEmpty($this->field->validate('#XYZ'));

        // Wrong length
        $this->assertNotEmpty($this->field->validate('#12'));
        $this->assertNotEmpty($this->field->validate('#1234'));
        $this->assertNotEmpty($this->field->validate('#12345'));
        $this->assertNotEmpty($this->field->validate('#1234567'));
    }

    public function testEmptyValuePassesValidationForNonRequiredField(): void
    {
        $this->field->setRequired(false);
        $this->assertEmpty($this->field->validate(''));
        $this->assertEmpty($this->field->validate(null));
    }

    public function testEmptyValueFailsValidationForRequiredField(): void
    {
        $this->field->setRequired();
        $this->assertNotEmpty($this->field->validate(''));
        $this->assertNotEmpty($this->field->validate(null));
    }

    public function testInheritedTextFieldValidation(): void
    {
        // Test that max length validation is inherited from TextField
        $this->field->setMaxLength(5);
        $this->assertNotEmpty($this->field->validate('#123456'));

        $this->field->setMaxLength(7);
        $this->assertEmpty($this->field->validate('#123456'));
    }

    public function testProcessInputInheritedFromTextField(): void
    {
        // Test default processing
        $this->assertEquals('#ABC123', $this->field->processInput('#ABC123'));

        // Test with empty value and default
        $this->field->setDefault('#000000');
        $this->assertEquals('#000000', $this->field->processInput(''));
    }
}
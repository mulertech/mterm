<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\NumericRangeValidator;
use PHPUnit\Framework\TestCase;

class NumericRangeValidatorTest extends TestCase
{
    public function testValidateWithMinAndMax(): void
    {
        $validator = new NumericRangeValidator(10, 20);

        // Valid values
        $this->assertNull($validator->validate(10));    // Exactly min
        $this->assertNull($validator->validate(15));    // Between min and max
        $this->assertNull($validator->validate(20));    // Exactly max
        $this->assertNull($validator->validate("15.5")); // String numeric between min and max

        // Invalid values
        $this->assertEquals(
            'Value must be between 10 and 20.',
            $validator->validate(9.9)  // Too small
        );
        $this->assertEquals(
            'Value must be between 10 and 20.',
            $validator->validate(20.1)  // Too large
        );
    }

    public function testValidateWithMinOnly(): void
    {
        $validator = new NumericRangeValidator(5);

        // Valid values
        $this->assertNull($validator->validate(5));     // Exactly min
        $this->assertNull($validator->validate(100));   // Above min
        $this->assertNull($validator->validate("5.01")); // String numeric above min

        // Invalid values
        $this->assertEquals(
            'Value must be at least 5.',
            $validator->validate(4.99)  // Below min
        );
    }

    public function testValidateWithMaxOnly(): void
    {
        $validator = new NumericRangeValidator(null, 30);

        // Valid values
        $this->assertNull($validator->validate(30));    // Exactly max
        $this->assertNull($validator->validate(15));    // Below max
        $this->assertNull($validator->validate(-10));   // Negative number below max

        // Invalid values
        $this->assertEquals(
            'Value cannot exceed 30.',
            $validator->validate(30.01)  // Above max
        );
    }

    public function testValidateWithNoConstraints(): void
    {
        $validator = new NumericRangeValidator();

        // All numeric values should pass when no constraints are set
        $this->assertNull($validator->validate(0));
        $this->assertNull($validator->validate(-100));
        $this->assertNull($validator->validate(999999));
        $this->assertNull($validator->validate(3.14159));
    }

    public function testValidateNonNumericValue(): void
    {
        $validator = new NumericRangeValidator(1, 10);

        $this->assertEquals(
            'Value must be a number.',
            $validator->validate('not-a-number')
        );

        $this->assertEquals(
            'Value must be a number.',
            $validator->validate('abc123')
        );

        $this->assertEquals(
            'Value must be a number.',
            $validator->validate('10px')
        );
    }

    public function testValidateWithNullOrEmptyValue(): void
    {
        $validator = new NumericRangeValidator(1, 10);

        // Null or empty values are always valid
        $this->assertNull($validator->validate(null));
        $this->assertNull($validator->validate(''));
    }

    public function testValidateWithCustomErrorMessage(): void
    {
        $customMessage = 'Custom range error message.';
        $validator = new NumericRangeValidator(10, 20, $customMessage);

        $this->assertEquals(
            $customMessage,
            $validator->validate(5)  // Below min
        );

        $this->assertEquals(
            $customMessage,
            $validator->validate(25)  // Above max
        );
    }

    public function testValidateFloatValues(): void
    {
        $validator = new NumericRangeValidator(0.5, 2.5);

        // Valid float values
        $this->assertNull($validator->validate(0.5));   // Exactly min
        $this->assertNull($validator->validate(1.75));  // Between min and max
        $this->assertNull($validator->validate(2.5));   // Exactly max

        // Invalid float values
        $this->assertEquals(
            'Value must be between 0.5 and 2.5.',
            $validator->validate(0.499)  // Just below min
        );
        $this->assertEquals(
            'Value must be between 0.5 and 2.5.',
            $validator->validate(2.501)  // Just above max
        );
    }

    public function testValidateWithZeroValues(): void
    {
        // Test with zero as min
        $validatorZeroMin = new NumericRangeValidator(0, 10);
        $this->assertNull($validatorZeroMin->validate(0));
        $this->assertEquals(
            'Value must be between 0 and 10.',
            $validatorZeroMin->validate(-0.1)
        );

        // Test with zero as max
        $validatorZeroMax = new NumericRangeValidator(-10, 0);
        $this->assertNull($validatorZeroMax->validate(0));
        $this->assertEquals(
            'Value must be between -10 and 0.',
            $validatorZeroMax->validate(0.1)
        );
    }

    public function testValidateWithNegativeValues(): void
    {
        $validator = new NumericRangeValidator(-20, -10);

        // Valid negative values
        $this->assertNull($validator->validate(-20));   // Exactly min
        $this->assertNull($validator->validate(-15));   // Between min and max
        $this->assertNull($validator->validate(-10));   // Exactly max

        // Invalid negative values
        $this->assertEquals(
            'Value must be between -20 and -10.',
            $validator->validate(-21)  // Too small (more negative)
        );
        $this->assertEquals(
            'Value must be between -20 and -10.',
            $validator->validate(-9)   // Too large (less negative)
        );
    }
}
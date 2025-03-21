<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\LengthValidator;
use PHPUnit\Framework\TestCase;

class LengthValidatorTest extends TestCase
{
    public function testValidateWithMinAndMax(): void
    {
        $validator = new LengthValidator(3, 8);

        // Valid lengths
        $this->assertNull($validator->validate('abc'));     // Exactly min
        $this->assertNull($validator->validate('abcdef'));  // Between min and max
        $this->assertNull($validator->validate('abcdefgh')); // Exactly max

        // Invalid lengths
        $this->assertEquals(
            'Value must be between 3 and 8 characters.',
            $validator->validate('ab')  // Too short
        );
        $this->assertEquals(
            'Value must be between 3 and 8 characters.',
            $validator->validate('abcdefghi')  // Too long
        );
    }

    public function testValidateWithMinOnly(): void
    {
        $validator = new LengthValidator(5);

        // Valid lengths
        $this->assertNull($validator->validate('abcde'));     // Exactly min
        $this->assertNull($validator->validate('abcdefghij')); // More than min

        // Invalid lengths
        $this->assertEquals(
            'Value must be at least 5 characters.',
            $validator->validate('abcd')  // Too short
        );
    }

    public function testValidateWithMaxOnly(): void
    {
        $validator = new LengthValidator(null, 6);

        // Valid lengths
        $this->assertNull($validator->validate(''));        // Empty string
        $this->assertNull($validator->validate('abc'));     // Less than max
        $this->assertNull($validator->validate('abcdef'));  // Exactly max

        // Invalid lengths
        $this->assertEquals(
            'Value cannot exceed 6 characters.',
            $validator->validate('abcdefg')  // Too long
        );
    }

    public function testValidateWithNoConstraints(): void
    {
        $validator = new LengthValidator();

        // All values should pass when no constraints are set
        $this->assertNull($validator->validate(''));
        $this->assertNull($validator->validate('abc'));
        $this->assertNull($validator->validate('very long string with many characters'));
    }

    public function testValidateWithNullOrEmptyValue(): void
    {
        $validator = new LengthValidator(5, 10);

        // Null or empty values are always valid
        $this->assertNull($validator->validate(null));
        $this->assertNull($validator->validate(''));
    }

    public function testValidateWithMultiByteCharacters(): void
    {
        $validator = new LengthValidator(2, 5);

        // Valid multi-byte inputs
        $this->assertNull($validator->validate('東京'));     // 2 characters
        $this->assertNull($validator->validate('こんにちは'));  // 5 characters

        // Invalid multi-byte inputs
        $this->assertEquals(
            'Value must be between 2 and 5 characters.',
            $validator->validate('あ')  // 1 character - too short
        );
        $this->assertEquals(
            'Value must be between 2 and 5 characters.',
            $validator->validate('こんにちは世界')  // 7 characters - too long
        );
    }

    public function testConstructorWithCustomErrorMessage(): void
    {
        $customMessage = 'Custom length error message.';
        $validator = new LengthValidator(3, 8, $customMessage);

        $this->assertEquals(
            $customMessage,
            $validator->validate('ab')  // Too short
        );

        $this->assertEquals(
            $customMessage,
            $validator->validate('abcdefghi')  // Too long
        );
    }

    public function testValidateWithSpecialCharacters(): void
    {
        $validator = new LengthValidator(3, 10);

        // Valid with special characters
        $this->assertNull($validator->validate('a!@'));     // 3 characters
        $this->assertNull($validator->validate('123\n456')); // Newline character

        // Invalid with special characters
        $this->assertEquals(
            'Value must be between 3 and 10 characters.',
            $validator->validate('a&')  // 2 characters - too short
        );
        $this->assertEquals(
            'Value must be between 3 and 10 characters.',
            $validator->validate('!@#$%^&*()_+')  // 12 characters - too long
        );
    }

    public function testNonStringInputs(): void
    {
        $validator = new LengthValidator(2, 5);

        // Numbers get converted to strings
        $this->assertNull($validator->validate(123));     // '123' - 3 chars
        $this->assertEquals(
            'Value must be between 2 and 5 characters.',
            $validator->validate(123456)  // '123456' - 6 chars - too long
        );
    }
}
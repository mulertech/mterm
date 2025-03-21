<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\PatternValidator;
use PHPUnit\Framework\TestCase;

class PatternValidatorTest extends TestCase
{
    public function testValidateWithMatchingValue(): void
    {
        // Pattern for alphanumeric string
        $validator = new PatternValidator('/^[a-zA-Z0-9]+$/');

        // Matching values
        $this->assertNull($validator->validate('abc123'));
        $this->assertNull($validator->validate('ABC123'));
        $this->assertNull($validator->validate('123abc'));
    }

    public function testValidateWithNonMatchingValue(): void
    {
        // Pattern for alphanumeric string
        $validator = new PatternValidator('/^[a-zA-Z0-9]+$/');

        // Non-matching values
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('abc-123')  // Contains hyphen
        );
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('abc 123')  // Contains space
        );
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('abc@123')  // Contains special character
        );
    }

    public function testValidateWithNullOrEmptyValue(): void
    {
        $validator = new PatternValidator('/^[a-z]+$/');

        // Null or empty values are always valid
        $this->assertNull($validator->validate(null));
        $this->assertNull($validator->validate(''));
    }

    public function testValidateWithCustomErrorMessage(): void
    {
        $customMessage = 'Input must be alphanumeric.';
        $validator = new PatternValidator('/^[a-zA-Z0-9]+$/', $customMessage);

        $this->assertEquals(
            $customMessage,
            $validator->validate('abc-123')  // Non-matching value
        );
    }

    public function testValidateWithEmailPattern(): void
    {
        $emailPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        $validator = new PatternValidator($emailPattern);

        // Valid emails
        $this->assertNull($validator->validate('test@example.com'));
        $this->assertNull($validator->validate('user.name+tag@domain.co.uk'));

        // Invalid emails
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('invalid-email')
        );
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('missing@domain')
        );
    }

    public function testValidateWithPhoneNumberPattern(): void
    {
        $phonePattern = '/(\+1-)?\d{3}-?\d{3}-?\d{4}$/';
        $validator = new PatternValidator($phonePattern);

        // Valid phone numbers
        $this->assertNull($validator->validate('+1-555-123-4567'));
        $this->assertNull($validator->validate('555-123-4567'));
        $this->assertNull($validator->validate('5551234567'));

        // Invalid phone numbers
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('abc-def-ghij')
        );
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('12-34')
        );
    }

    public function testValidateWithDatePattern(): void
    {
        $datePattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/';
        $validator = new PatternValidator($datePattern);

        // Valid dates (format validation only, not logical validation)
        $this->assertNull($validator->validate('2023-01-15'));
        $this->assertNull($validator->validate('2000-12-31'));

        // Invalid dates (format-wise)
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('01/15/2023')
        );
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate('2023.01.15')
        );
    }

    public function testNonStringValues(): void
    {
        $validator = new PatternValidator('/^[0-9]+$/');

        // Integer gets converted to string and matches
        $this->assertNull($validator->validate(12345));

        // Float gets converted but doesn't match due to decimal point
        $this->assertEquals(
            'Value does not match required pattern.',
            $validator->validate(123.45)
        );
    }
}
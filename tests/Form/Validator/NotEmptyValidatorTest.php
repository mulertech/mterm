<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\NotEmptyValidator;
use PHPUnit\Framework\TestCase;

class NotEmptyValidatorTest extends TestCase
{
    public function testValidateWithNullValue(): void
    {
        $validator = new NotEmptyValidator();
        $this->assertEquals(
            'This value cannot be empty.',
            $validator->validate(null)
        );
    }

    public function testValidateWithEmptyString(): void
    {
        $validator = new NotEmptyValidator();
        $this->assertEquals(
            'This value cannot be empty.',
            $validator->validate('')
        );
    }

    public function testValidateWithEmptyArray(): void
    {
        $validator = new NotEmptyValidator();
        $this->assertEquals(
            'This value cannot be empty.',
            $validator->validate([])
        );
    }

    public function testValidateWithNonEmptyString(): void
    {
        $validator = new NotEmptyValidator();
        $this->assertNull($validator->validate('Hello'));
    }

    public function testValidateWithZeroString(): void
    {
        $validator = new NotEmptyValidator();
        $this->assertNull($validator->validate('0'));
    }

    public function testValidateWithZeroInteger(): void
    {
        $validator = new NotEmptyValidator();
        $this->assertNull($validator->validate(0));
    }

    public function testValidateWithFalseBoolean(): void
    {
        $validator = new NotEmptyValidator();
        $this->assertNull($validator->validate(false));
    }

    public function testValidateWithNonEmptyArray(): void
    {
        $validator = new NotEmptyValidator();
        $this->assertNull($validator->validate(['item']));
    }

    public function testValidateWithCustomErrorMessage(): void
    {
        $customMessage = 'Field is required.';
        $validator = new NotEmptyValidator($customMessage);

        $this->assertEquals(
            $customMessage,
            $validator->validate(null)
        );
    }

    public function testValidateWithEmptyWhitespaceString(): void
    {
        $validator = new NotEmptyValidator();
        // Whitespace-only string is not considered empty by the validator
        $this->assertNull($validator->validate('   '));
    }
}
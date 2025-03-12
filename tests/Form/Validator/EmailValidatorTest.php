<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    private EmailValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new EmailValidator();
    }

    public function testValidEmailReturnsNull(): void
    {
        $this->assertNull($this->validator->validate('test@example.com'));
    }

    public function testInvalidEmailReturnsError(): void
    {
        $this->assertNotNull($this->validator->validate('not-an-email'));
    }

    public function testEmptyValueReturnsNull(): void
    {
        $this->assertNull($this->validator->validate(''));
        $this->assertNull($this->validator->validate(null));
    }

    public function testCustomErrorMessage(): void
    {
        $customMessage = 'Custom email error';
        $validator = new EmailValidator($customMessage);
        $this->assertSame($customMessage, $validator->validate('invalid-email'));
    }
}
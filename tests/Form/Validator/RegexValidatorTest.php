<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\RegexValidator;
use PHPUnit\Framework\TestCase;

class RegexValidatorTest extends TestCase
{
    public function testPatternMatching(): void
    {
        $validator = new RegexValidator('/^\d{3}-\d{3}-\d{4}$/');

        $this->assertNull($validator->validate('123-456-7890'));
        $this->assertNotNull($validator->validate('123-45-7890'));
        $this->assertNotNull($validator->validate('abc-def-ghij'));
    }

    public function testEmptyValueReturnsNull(): void
    {
        $validator = new RegexValidator('/\w+/');

        $this->assertNull($validator->validate(''));
        $this->assertNull($validator->validate(null));
    }

    public function testCustomErrorMessage(): void
    {
        $customMessage = 'Invalid format';
        $validator = new RegexValidator('/^\d+$/', $customMessage);

        $this->assertSame($customMessage, $validator->validate('abc'));
    }
}
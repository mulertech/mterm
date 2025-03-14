<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\AbstractValidator;
use PHPUnit\Framework\TestCase;

class AbstractValidatorTest extends TestCase
{
    public function testConstructorSetsErrorMessage(): void
    {
        $validator = new class('Custom error message') extends AbstractValidator {
            public function validate(mixed $value): ?string
            {
                return null;
            }
        };

        $this->assertSame('Custom error message', $validator->getErrorMessage());
    }
}
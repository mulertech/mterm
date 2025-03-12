<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use DateTime;
use MulerTech\MTerm\Form\Validator\DateValidator;
use PHPUnit\Framework\TestCase;

class DateValidatorTest extends TestCase
{
    private DateValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new DateValidator('Y-m-d');
    }

    public function testValidDateReturnsNull(): void
    {
        $this->assertNull($this->validator->validate('2023-01-15'));
    }

    public function testInvalidDateReturnsError(): void
    {
        $this->assertNotNull($this->validator->validate('2023-13-45'));
        $this->assertNotNull($this->validator->validate('not-a-date'));
    }

    public function testEmptyValueReturnsNull(): void
    {
        $this->assertNull($this->validator->validate(''));
        $this->assertNull($this->validator->validate(null));
    }

    public function testMinDateValidation(): void
    {
        $minDate = new DateTime('2023-01-01');
        $validator = new DateValidator('Y-m-d', $minDate);

        $this->assertNull($validator->validate('2023-01-01'));
        $this->assertNull($validator->validate('2023-02-01'));
        $this->assertNotNull($validator->validate('2022-12-31'));
    }

    public function testMaxDateValidation(): void
    {
        $maxDate = new DateTime('2023-12-31');
        $validator = new DateValidator('Y-m-d', null, $maxDate);

        $this->assertNull($validator->validate('2023-12-31'));
        $this->assertNull($validator->validate('2023-01-01'));
        $this->assertNotNull($validator->validate('2024-01-01'));
    }

    public function testCustomFormat(): void
    {
        $validator = new DateValidator('d/m/Y');

        $this->assertNull($validator->validate('15/01/2023'));
        $this->assertNotNull($validator->validate('2023-01-15'));
    }
}
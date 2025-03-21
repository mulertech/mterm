<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\EmailField;
use PHPUnit\Framework\TestCase;

class EmailFieldTest extends TestCase
{
    private EmailField $field;

    protected function setUp(): void
    {
        $this->field = new EmailField('email', 'Email Address');
    }

    public function testValidEmailAddressPassesValidation(): void
    {
        $this->assertEmpty($this->field->validate('user@example.com'));
    }

    public function testInvalidEmailAddressFailsValidation(): void
    {
        $this->assertNotEmpty($this->field->validate('not-an-email'));
        $this->assertNotEmpty($this->field->validate('user@'));
        $this->assertNotEmpty($this->field->validate('@example.com'));
    }

    public function testEmptyValuePassesValidationForNonRequiredField(): void
    {
        $this->field->setRequired(false);
        $this->assertEmpty($this->field->validate(''));
    }

    public function testEmptyValueFailsValidationForRequiredField(): void
    {
        $this->field->setRequired();
        $this->assertNotEmpty($this->field->validate(''));
    }

    public function testInheritedMinLengthValidation(): void
    {
        $this->field->setMinLength(10);

        $this->assertEmpty($this->field->validate('user@example.com'));
        $this->assertNotEmpty($this->field->validate('u@ex.com'));
    }
}
<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\UrlField;
use MulerTech\MTerm\Form\Validator\RegexValidator;
use PHPUnit\Framework\TestCase;

class UrlFieldTest extends TestCase
{
    private UrlField $field;

    protected function setUp(): void
    {
        $this->field = new UrlField('url', 'URL Field');
    }

    public function testValidateValidUrl(): void
    {
        $errors = $this->field->validate('https://www.example.com');
        $this->assertEmpty($errors);

        $errors = $this->field->validate('https://localhost');
        $this->assertEmpty($errors);

        $errors = $this->field->validate('ftp://ftp.example.org');
        $this->assertEmpty($errors);
    }

    public function testValidateInvalidUrl(): void
    {
        $errors = $this->field->validate('not-a-url');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('valid URL', $errors[0]);

        $errors = $this->field->validate('www.example.com');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('valid URL', $errors[0]);

        $errors = $this->field->validate('https://');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('valid URL', $errors[0]);
    }

    public function testValidateNullValue(): void
    {
        // Non-required field should accept null
        $this->field->setRequired(false);
        $errors = $this->field->validate(null);
        $this->assertEmpty($errors);

        // Required field should not accept null
        $this->field->setRequired();
        $errors = $this->field->validate(null);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);
    }

    public function testValidateEmptyString(): void
    {
        // Non-required field should accept empty string
        $this->field->setRequired(false);
        $errors = $this->field->validate('');
        $this->assertEmpty($errors);

        // Required field should not accept empty string
        $this->field->setRequired();
        $errors = $this->field->validate('');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);
    }

    public function testInheritedValidation(): void
    {
        // Test min length validation
        $this->field->setMinLength(17);
        $errors = $this->field->validate('https://short.io');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('at least 17', $errors[0]);

        // Test max length validation
        $this->field->setMinLength(0);
        $this->field->setMaxLength(20);
        $errors = $this->field->validate('https://verylongdomainname.com');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('cannot exceed 20', $errors[0]);

        // Test pattern validation
        $this->field->setMinLength(0);
        $this->field->setMaxLength(100);
        $this->field->addValidator(new RegexValidator('/^https:/i', 'URL must begin with https://'));

        $errors = $this->field->validate('ftp://example.com');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('must begin with https://', $errors[0]);
    }

    public function testProcessInput(): void
    {
        $input = 'https://example.com';
        $result = $this->field->processInput($input);
        $this->assertEquals($input, $result);

        // Empty input should return default value
        $this->field->setDefault('https://default.com');
        $result = $this->field->processInput('');
        $this->assertEquals('https://default.com', $result);

    }

    public function testMultipleValidationErrors(): void
    {
        $this->field->setRequired();
        $this->field->setMinLength(20);

        // Valid URL but too short
        $errors = $this->field->validate('https://short.com');
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('at least 20', $errors[0]);

        // Invalid URL and too short
        $errors = $this->field->validate('invalid');
        $this->assertCount(2, $errors);

        // Check both errors are present
        $hasUrlError = false;
        $hasLengthError = false;

        foreach ($errors as $error) {
            if (str_contains($error, 'valid URL')) {
                $hasUrlError = true;
            }
            if (str_contains($error, 'at least 20')) {
                $hasLengthError = true;
            }
        }

        $this->assertTrue($hasUrlError && $hasLengthError);
    }
}
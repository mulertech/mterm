<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\DateField;
use PHPUnit\Framework\TestCase;

class DateFieldTest extends TestCase
{
    private DateField $field;

    protected function setUp(): void
    {
        $this->field = new DateField('date', 'Date');
    }

    public function testGetSetFormat(): void
    {
        $this->assertEquals('Y-m-d', $this->field->getFormat());

        $this->field->setFormat('d/m/Y');
        $this->assertEquals('d/m/Y', $this->field->getFormat());
    }

    public function testProcessInputWithValidDate(): void
    {
        $this->field->setFormat('Y-m-d');
        $this->assertEquals('2023-01-15', $this->field->processInput('2023-01-15'));
    }

    public function testProcessInputWithInvalidDate(): void
    {
        $this->field->setFormat('Y-m-d');
        $this->assertEquals('not-a-date', $this->field->processInput('not-a-date'));
    }

    public function testProcessInputWithEmptyValue(): void
    {
        $this->field->setDefault('2023-01-01');
        $this->assertEquals('2023-01-01', $this->field->processInput(''));
    }

    public function testValidateWithValidDate(): void
    {
        $this->assertEmpty($this->field->validate('2023-01-15'));
    }

    public function testValidateWithInvalidDate(): void
    {
        $this->assertNotEmpty($this->field->validate('2023-13-32'));
        $this->assertNotEmpty($this->field->validate('not-a-date'));
    }

    public function testValidateWithCustomFormat(): void
    {
        $this->field->setFormat('d/m/Y');

        $this->assertEmpty($this->field->validate('15/01/2023'));
        $this->assertNotEmpty($this->field->validate('2023-01-15'));
    }
}
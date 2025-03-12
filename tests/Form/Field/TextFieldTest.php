<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\TextField;
use MulerTech\MTerm\Form\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class TextFieldTest extends TestCase
{
    private TextField $field;

    protected function setUp(): void
    {
        $this->field = new TextField('username', 'Username');
    }

    public function testGetNameReturnsFieldName(): void
    {
        $this->assertEquals('username', $this->field->getName());
    }

    public function testGetLabelReturnsFieldLabel(): void
    {
        $this->assertEquals('Username', $this->field->getLabel());
    }

    public function testSetDescriptionSetsDescription(): void
    {
        $this->field->setDescription('Enter your username');
        $this->assertEquals('Enter your username', $this->field->getDescription());
    }

    public function testSetRequiredSetsRequiredFlag(): void
    {
        $this->assertFalse($this->field->isRequired());

        $this->field->setRequired(true);
        $this->assertTrue($this->field->isRequired());

        $this->field->setRequired(false);
        $this->assertFalse($this->field->isRequired());
    }

    public function testSetAndGetDefault(): void
    {
        $this->field->setDefault('default_user');
        $this->assertEquals('default_user', $this->field->getDefault());
    }

    public function testProcessInputReturnsInputOrDefault(): void
    {
        $this->field->setDefault('default_value');

        $this->assertEquals('test_input', $this->field->processInput('test_input'));
        $this->assertEquals('default_value', $this->field->processInput(''));
    }

    public function testValidateWithRequiredField(): void
    {
        $this->field->setRequired(true);

        $this->assertCount(1, $this->field->validate(''));
        $this->assertCount(1, $this->field->validate(null));
        $this->assertEmpty($this->field->validate('value'));
    }

    public function testMinLength(): void
    {
        $this->field->setMinLength(5);

        $this->assertEmpty($this->field->validate('12345'));
        $this->assertCount(1, $this->field->validate('1234'));
    }

    public function testMaxLength(): void
    {
        $this->field->setMaxLength(5);

        $this->assertEmpty($this->field->validate('12345'));
        $this->assertCount(1, $this->field->validate('123456'));
    }

    public function testCustomValidator(): void
    {
        $mockValidator = $this->createMock(ValidatorInterface::class);
        $mockValidator->expects($this->once())
            ->method('validate')
            ->with('test_value')
            ->willReturn('Custom validation error');

        $this->field->addValidator($mockValidator);

        $errors = $this->field->validate('test_value');
        $this->assertCount(1, $errors);
        $this->assertEquals('Custom validation error', $errors[0]);
    }
}
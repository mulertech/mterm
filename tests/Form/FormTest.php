<?php

namespace MulerTech\MTerm\Tests\Form;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\FieldInterface;
use MulerTech\MTerm\Form\Field\SelectField;
use MulerTech\MTerm\Form\Field\Template\SelectMultipleArrowTemplate;
use MulerTech\MTerm\Form\Form;
use MulerTech\MTerm\Form\FormRenderer;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    private Terminal $terminal;
    private Form $form;

    protected function setUp(): void
    {
        $this->terminal = $this->createMock(Terminal::class);
        $this->form = new Form($this->terminal);
    }

    public function testAddFieldReturnsFormInstance(): void
    {
        $field = $this->createMock(FieldInterface::class);
        $field->method('getName')->willReturn('testField');

        $result = $this->form->addField($field);

        $this->assertInstanceOf(Form::class, $result);
        $this->assertSame($this->form, $result);
    }

    public function testSubmittedFormWithValidFieldsIsValid(): void
    {
        $field = $this->createMock(FieldInterface::class);
        $field->method('getName')->willReturn('name');
        $field->method('validate')->willReturn([]);

        $this->form->addField($field);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderField')->willReturn('John Doe');

        $reflection = new \ReflectionProperty($this->form, 'renderer');
        $reflection->setAccessible(true);
        $reflection->setValue($this->form, $formRenderer);

        $result = $this->form->handle();

        $this->assertTrue($result);
        $this->assertTrue($this->form->isSubmitted());
        $this->assertTrue($this->form->isValid());
        $this->assertEquals(['name' => 'John Doe'], $this->form->getValues());
        $this->assertEquals('John Doe', $this->form->getValue('name'));
        $this->assertEquals([], $this->form->getErrors());
    }

    public function testSubmittedFormWithInvalidFieldsIsNotValid(): void
    {
        $field = $this->createMock(FieldInterface::class);
        $field->method('getName')->willReturn('email');
        $field->method('validate')->willReturn(['Invalid email format']);

        $this->form->addField($field);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderField')->willReturn('invalid-email');
        $formRenderer->expects($this->once())->method('renderErrors');

        $reflection = new \ReflectionProperty($this->form, 'renderer');
        $reflection->setAccessible(true);
        $reflection->setValue($this->form, $formRenderer);

        $result = $this->form->handle();

        $this->assertFalse($result);
        $this->assertTrue($this->form->isSubmitted());
        $this->assertFalse($this->form->isValid());
        $this->assertEquals(['email' => 'invalid-email'], $this->form->getValues());
        $this->assertEquals(['email' => ['Invalid email format']], $this->form->getErrors());
    }

    public function testMultipleFieldsAreProcessedCorrectly(): void
    {
        $nameField = $this->createMock(FieldInterface::class);
        $nameField->method('getName')->willReturn('name');
        $nameField->method('validate')->willReturn([]);

        $emailField = $this->createMock(FieldInterface::class);
        $emailField->method('getName')->willReturn('email');
        $emailField->method('validate')->willReturn([]);

        $this->form->addField($nameField);
        $this->form->addField($emailField);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderField')
            ->willReturnMap([
                [$nameField, 'John Doe'],
                [$emailField, 'john@example.com']
            ]);

        $reflection = new \ReflectionProperty($this->form, 'renderer');
        $reflection->setAccessible(true);
        $reflection->setValue($this->form, $formRenderer);

        $this->form->handle();

        $this->assertEquals([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ], $this->form->getValues());
    }

    public function testGetNonExistentValueReturnsNull(): void
    {
        $this->assertNull($this->form->getValue('non_existent'));
    }

    public function initialStateIsNotSubmitted(): void
    {
        $this->assertFalse($this->form->isSubmitted());
        $this->assertFalse($this->form->isValid());
        $this->assertEquals([], $this->form->getValues());
        $this->assertEquals([], $this->form->getErrors());
    }

    public function testMixedValidAndInvalidFieldsMarksFormAsInvalid(): void
    {
        $validField = $this->createMock(FieldInterface::class);
        $validField->method('getName')->willReturn('name');
        $validField->method('validate')->willReturn([]);

        $invalidField = $this->createMock(FieldInterface::class);
        $invalidField->method('getName')->willReturn('email');
        $invalidField->method('validate')->willReturn(['Invalid email']);

        $this->form->addField($validField);
        $this->form->addField($invalidField);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderField')
            ->willReturnMap([
                [$validField, 'John Doe'],
                [$invalidField, 'invalid']
            ]);
        $formRenderer->expects($this->once())->method('renderErrors');

        $reflection = new \ReflectionProperty($this->form, 'renderer');
        $reflection->setAccessible(true);
        $reflection->setValue($this->form, $formRenderer);

        $result = $this->form->handle();

        $this->assertFalse($result);
        $this->assertFalse($this->form->isValid());
        $this->assertEquals(['name' => 'John Doe', 'email' => 'invalid'], $this->form->getValues());
        $this->assertEquals(['email' => ['Invalid email']], $this->form->getErrors());
    }

    public function testInitialStateIsNotSubmitted(): void
    {
        $this->assertFalse($this->form->isSubmitted());
        $this->assertFalse($this->form->isValid());
        $this->assertEquals([], $this->form->getValues());
        $this->assertEquals([], $this->form->getErrors());
    }

    public function testHandleSingleSelectField(): void
    {
        $field = $this->getMockBuilder(SelectField::class)
            ->setConstructorArgs(['country', 'Country'])
            ->onlyMethods(['getName', 'validate', 'processInput'])
            ->getMock();

        $field->method('getName')->willReturn('country');
        $field->method('validate')->willReturn([]);
        $field->method('processInput')->willReturn('US');

        $this->form->addField($field);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderSelectSingleField')->willReturn('US');

        $reflection = new \ReflectionProperty($this->form, 'renderer');
        $reflection->setAccessible(true);
        $reflection->setValue($this->form, $formRenderer);

        $result = $this->form->handle();

        $this->assertTrue($result);
        $this->assertTrue($this->form->isValid());
        $this->assertEquals(['country' => 'US'], $this->form->getValues());
    }

    public function testHandleMultiSelectField(): void
    {
        $field = $this->getMockBuilder(SelectField::class)
            ->setConstructorArgs(['interests', 'Interests', true])
            ->onlyMethods(['getName', 'validate', 'getSelectedOptions'])
            ->getMock();

        $field->method('getName')->willReturn('interests');
        $field->method('validate')->willReturn([]);
        $field->method('getSelectedOptions')->willReturn(['sport', 'music']);

        $this->form->addField($field);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderSelectMultipleField')->willReturn('1,2');

        $reflection = new \ReflectionProperty($this->form, 'renderer');
        $reflection->setAccessible(true);
        $reflection->setValue($this->form, $formRenderer);

        $result = $this->form->handle();

        $this->assertTrue($result);
        $this->assertTrue($this->form->isValid());
        $this->assertEquals(['interests' => '1,2'], $this->form->getValues());
    }

    public function testInvalidSelectFieldSelection(): void
    {
        $field = $this->getMockBuilder(SelectField::class)
            ->setConstructorArgs(['category', 'Category'])
            ->onlyMethods(['validate', 'getName'])
            ->getMock();

        $field->method('getName')->willReturn('category');
        $field->method('validate')->willReturn(['Invalid option selected']);

        $this->form->addField($field);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderSelectMultipleField')->willReturn('invalid-category');
        $formRenderer->expects($this->once())->method('renderErrors');

        $reflection = new \ReflectionProperty($this->form, 'renderer');
        $reflection->setAccessible(true);
        $reflection->setValue($this->form, $formRenderer);

        $result = $this->form->handle();

        $this->assertFalse($result);
        $this->assertFalse($this->form->isValid());
        $this->assertEquals(['category' => ['Invalid option selected']], $this->form->getErrors());
    }

    public function testMultiSelectFieldWithInvalidOptions(): void
    {
        $field = $this->getMockBuilder(SelectField::class)
            ->setConstructorArgs(['tags', 'Tags', true])
            ->onlyMethods(['getName', 'validate', 'getSelectedOptions'])
            ->getMock();

        $field->method('getName')->willReturn('tags');
        $field->method('validate')->willReturn(['One or more invalid options selected']);
        $field->method('getSelectedOptions')->willReturn(['valid-tag', 'invalid-tag']);

        $this->form->addField($field);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderSelectMultipleField')->willReturn('1,2');
        $formRenderer->expects($this->once())->method('renderErrors');

        $reflection = new \ReflectionProperty($this->form, 'renderer');
        $reflection->setAccessible(true);
        $reflection->setValue($this->form, $formRenderer);

        $result = $this->form->handle();

        $this->assertFalse($result);
        $this->assertFalse($this->form->isValid());
        $this->assertEquals(['tags' => ['One or more invalid options selected']], $this->form->getErrors());
    }
}
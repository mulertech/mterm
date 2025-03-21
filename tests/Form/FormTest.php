<?php

namespace MulerTech\MTerm\Tests\Form;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\FieldInterface;
use MulerTech\MTerm\Form\Form;
use MulerTech\MTerm\Form\FormRenderer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

class FormTest extends TestCase
{
    private Form $form;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $terminal = $this->createMock(Terminal::class);
        $this->form = new Form($terminal);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testSubmittedFormWithValidFieldsIsValid(): void
    {
        $field = $this->createMock(FieldInterface::class);
        $field->method('getName')->willReturn('name');
        $field->method('validate')->willReturn([]);

        $this->form->addField($field);

        $formRenderer = $this->createMock(FormRenderer::class);
        $formRenderer->method('renderField')->willReturn('John Doe');

        $reflection = new ReflectionProperty($this->form, 'renderer');
        $reflection->setValue($this->form, $formRenderer);

        $this->form->handle();

        $this->assertTrue($this->form->isSubmitted());
        $this->assertTrue($this->form->isValid());
        $this->assertEquals(['name' => 'John Doe'], $this->form->getValues());
        $this->assertEquals('John Doe', $this->form->getValue('name'));
    }
}
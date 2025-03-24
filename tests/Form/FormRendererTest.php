<?php

namespace MulerTech\MTerm\Tests\Form;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\PasswordField;
use MulerTech\MTerm\Form\Field\SelectField;
use MulerTech\MTerm\Form\Field\Template\SelectMultipleArrowTemplate;
use MulerTech\MTerm\Form\Field\Template\SelectSingleArrowTemplate;
use MulerTech\MTerm\Form\Field\TextField;
use MulerTech\MTerm\Form\Form;
use MulerTech\MTerm\Form\FormRenderer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class FormRendererTest extends TestCase
{
    private Terminal $terminal;
    private FormRenderer $renderer;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->terminal = $this->createMock(Terminal::class);
        $this->renderer = new FormRenderer($this->terminal);
    }

    public function testRenderPasswordField(): void
    {
        $field = new PasswordField('password', 'Password');
        $form = new Form($this->terminal);
        $form->addField($field);

        $this->terminal
            ->expects($this->exactly(8))
            ->method('write')
            ->withAnyParameters();
        $this->terminal->expects($this->once())->method('specialMode');
        $this->terminal
            ->method('readChar')
            // Simulate deleting c character
            ->willReturnOnConsecutiveCalls('s', 'e', 'c', "\x7F", 'r', 'e', 't', PHP_EOL);
        $this->terminal->expects($this->once())->method('normalMode');

        $form->handle();
        $this->assertEquals(['password' => 'seret'], $form->getValues());
    }

    public function testRenderFieldWithDescription(): void
    {
        $field = new TextField('test_field', 'Test field');
        $field->setDescription('Test description')->setRequired();

        $form = new Form($this->terminal);
        $form->addField($field);

        $this->terminal->expects($this->once())
            ->method('writeLine')
            ->with('Test description', 'cyan');

        $this->terminal->expects($this->once())
            ->method('read')
            ->with('Test field (required): ')
            ->willReturn('test value');

        $form->handle();
        $this->assertEquals(['test_field' => 'test value'], $form->getValues());
    }

    public function testRenderFieldWithoutDescription(): void
    {
        $field = new TextField('test_field', 'Test field');
        $field->setRequired(false);

        $form = new Form($this->terminal);
        $form->addField($field);

        $this->terminal->expects($this->never())->method('writeLine');
        $this->terminal->expects($this->once())
            ->method('read')
            ->with('Test field: ')
            ->willReturn('test value');

        $form->handle();
        $this->assertEquals(['test_field' => 'test value'], $form->getValues());
    }

    public function testRenderSelectSingleField(): void
    {
        $field = new SelectSingleArrowTemplate('choice', 'Select option');
        $field->setDescription('Choose one option')
            ->setOptions([
                'opt1' => 'Option 1',
                'opt2' => 'Option 2',
                'opt3' => 'Option 3',
            ]);

        $form = new Form($this->terminal);
        $form->addField($field);

        $this->terminal->expects($this->once())->method('specialMode');
        $this->terminal->expects($this->exactly(2))->method('clear');
        $this->terminal->expects($this->once())->method('normalMode');

        // Terminal input sequence: Press Enter to select current option
        $this->terminal->method('readChar')
            ->willReturn(PHP_EOL);

        $form->handle();
        $this->assertEquals(['choice' => 'opt1'], $form->getValues());
    }

    public function testRenderSelectSingleFieldWithNavigation(): void
    {
        $field = new SelectField('choice', 'Select option');
        $field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3',
        ]);

        $form = new Form($this->terminal);
        $form->addField($field);

        $this->terminal->expects($this->once())->method('specialMode');
        $this->terminal->expects($this->exactly(5))->method('clear');
        $this->terminal
            ->expects($this->exactly(8))
            ->method('write')
            ->withAnyParameters();
        $this->terminal->expects($this->once())->method('normalMode');

        // Simulate keyboard navigation: Down arrow then Enter
        $this->terminal->method('readChar')
            ->willReturnOnConsecutiveCalls(
                "\033", '[', 'B', // Down arrow
                "\033", '[', 'B', // Down arrow
                "\033", '[', 'A', // Up arrow
                PHP_EOL
            );

        $form->handle();
        $this->assertEquals(['choice' => 'opt2'], $form->getValues());
    }

    public function testRenderSelectMultipleField(): void
    {
        $field = new SelectMultipleArrowTemplate('choices', 'Select options');
        $field->setMultipleSelection()
            ->setOptions([
                'opt1' => 'Option 1',
                'opt2' => 'Option 2',
                'opt3' => 'Option 3',
            ]);

        $form = new Form($this->terminal);
        $form->addField($field);

        $this->terminal->expects($this->once())->method('specialMode');
        $this->terminal->expects($this->once())->method('normalMode');

        // Space to select first option, 1 down arrow, 1 up arrow, 2 down arrow, space to select second option, enter to confirm
        $this->terminal->method('readChar')
            ->willReturnOnConsecutiveCalls(
                ' ', // Space to select first option
                "\033", '[', 'B', // Down arrow
                ' ', // Space to select second option
                ' ', // Space to deselect second option
                "\033", '[', 'A', // Up arrow
                "\033", '[', 'B', // Down arrow
                "\033", '[', 'B', // Down arrow
                ' ', // Space to select third option
                PHP_EOL // Enter to confirm
            );

        $form->handle();
        $this->assertEquals(['choices' => ['opt1' => 'Option 1', 'opt3' => 'Option 3']], $form->getValues());
    }

    public function testRenderSelectMultipleFieldSelectAll(): void
    {
        $field = new SelectMultipleArrowTemplate('choices', 'Select options');
        $field->setMultipleSelection()
            ->setOptions([
                'opt1' => 'Option 1',
                'opt2' => 'Option 2',
                'opt3' => 'Option 3',
            ]);

        $form = new Form($this->terminal);
        $form->addField($field);

        $this->terminal->expects($this->once())->method('specialMode');
        $this->terminal->expects($this->once())->method('normalMode');

        // Space to select first option, 1 down arrow, 1 up arrow, 2 down arrow, space to select second option, enter to confirm
        $this->terminal->method('readChar')
            ->willReturnOnConsecutiveCalls(
                'a', // Press 'a' to select all options
                PHP_EOL // Enter to confirm
            );

        $form->handle();
        $this->assertEquals(['choices' => [
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ]], $form->getValues());
    }

    public function testRenderSelectMultipleWithDefaultField(): void
    {
        $field = new SelectField('choices', 'Select options');
        $field->setMultipleSelection()
            ->setOptions([
                'opt1' => 'Option 1',
                'opt2' => 'Option 2',
                'opt3' => 'Option 3',
            ])
            ->setDefault(['opt2' => 'Option 2', 'opt3' => 'Option 3']);

        $form = new Form($this->terminal);
        $form->addField($field);

        $this->terminal->expects($this->once())->method('specialMode');
        $this->terminal->expects($this->atLeastOnce())->method('clear');
        $this->terminal->expects($this->once())->method('normalMode');

        // enter to confirm
        $this->terminal->method('readChar')
            ->willReturnOnConsecutiveCalls(PHP_EOL);

        $form->handle();
        $this->assertEquals(['choices' => ['opt2' => 'Option 2', 'opt3' => 'Option 3']], $form->getValues());
    }

    public function testRenderErrors(): void
    {
        $field = new TextField('test_field', 'Test field');
        $field->setRequired();

        $form = new Form($this->terminal);
        $form->addField($field);

        // A first empty input, then a valid input
        $this->terminal->method('read')->willReturnOnConsecutiveCalls('', 'test value');
        $this->terminal
            ->expects($this->exactly(2))
            ->method('writeLine')
            ->withAnyParameters();

        $form->handle();
        $this->assertEquals(['test_field' => 'test value'], $form->getValues());
    }

    // Verify that terminal is correctly passed to fields
    public function testTerminalInjection(): void
    {
        $field = $this->createMock(PasswordField::class);
        $field->expects($this->once())->method('clearErrors');
        $field->expects($this->once())->method('setTerminal')->with($this->terminal);
        $field->expects($this->once())->method('isMaskInput')->willReturn(true);
        $field->expects($this->once())->method('processInput')->willReturn('test');

        $this->renderer->renderField($field);
    }
}

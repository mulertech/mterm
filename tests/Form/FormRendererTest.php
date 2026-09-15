<?php

namespace MulerTech\MTerm\Tests\Form;

use MulerTech\MTerm\Form\Field\PasswordField;
use MulerTech\MTerm\Form\Field\SelectField;
use MulerTech\MTerm\Form\Field\Template\SelectMultipleArrowTemplate;
use MulerTech\MTerm\Form\Field\Template\SelectSingleArrowTemplate;
use MulerTech\MTerm\Form\Field\TextField;
use MulerTech\MTerm\Form\Form;
use MulerTech\MTerm\Form\FormRenderer;
use MulerTech\MTerm\Tests\Support\TerminalDouble;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class FormRendererTest extends TestCase
{
    public function testRenderPasswordField(): void
    {
        // The c character is deleted by the backspace
        $double = new TerminalDouble("sec\x7Fret\n");
        $form = new Form($double->terminal);
        $form->addField(new PasswordField('password', 'Password'));

        $form->handle();

        $this->assertEquals(['password' => 'seret'], $form->getValues());
        $this->assertStringContainsString('Password: ', $double->display());
    }

    public function testRenderFieldWithDescription(): void
    {
        $field = new TextField('test_field', 'Test field');
        $field->setDescription('Test description')->setRequired();

        $double = new TerminalDouble("test value\n");
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['test_field' => 'test value'], $form->getValues());
        $this->assertStringContainsString('Test description', $double->display());
        $this->assertStringContainsString('Test field (required): ', $double->display());
    }

    public function testRenderFieldWithoutDescription(): void
    {
        $field = new TextField('test_field', 'Test field');
        $field->setRequired(false);

        $double = new TerminalDouble("test value\n");
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['test_field' => 'test value'], $form->getValues());
        $this->assertStringContainsString('Test field: ', $double->display());
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

        // Enter selects the option under the cursor
        $double = new TerminalDouble("\n");
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['choice' => 'opt1'], $form->getValues());
        $this->assertStringContainsString('>  Option 1', $double->display());
    }

    public function testRenderSelectSingleFieldWithNavigation(): void
    {
        $field = new SelectField('choice', 'Select option');
        $field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3',
        ]);

        // Down, down, up, then Enter
        $double = new TerminalDouble("\033[B\033[B\033[A\n");
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['choice' => 'opt2'], $form->getValues());
    }

    public function testAKeyTheSelectionIgnoresChangesNothing(): void
    {
        $field = new SelectField('choice', 'Select option');
        $field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
        ]);

        $double = new TerminalDouble("z\n");
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['choice' => 'opt1'], $form->getValues());
    }

    public function testASelectFieldWithoutAnswerAndWithoutDefaultIsEmpty(): void
    {
        $field = new SelectField('choice', 'Select option');
        $field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
        ]);

        $double = new TerminalDouble();
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['choice' => ''], $form->getValues());
    }

    public function testASelectFieldWithoutAnswerFallsBackOnItsDefault(): void
    {
        $field = new SelectField('choice', 'Select option');
        $field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
        ])->setDefault('opt2');

        $double = new TerminalDouble();
        $form = new Form($double->terminal);
        $form->addField($field);

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

        // Select the first option, then select and deselect the second one,
        // then walk down to the third one and select it
        $double = new TerminalDouble(" \033[B  \033[A\033[B\033[B \n");
        $form = new Form($double->terminal);
        $form->addField($field);

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

        // The a key selects every option
        $double = new TerminalDouble("a\n");
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['choices' => [
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3',
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
            ->setDefault(['opt2', 'opt3']);

        $double = new TerminalDouble("\n");
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['choices' => ['opt2' => 'Option 2', 'opt3' => 'Option 3']], $form->getValues());
    }

    public function testRenderErrors(): void
    {
        $field = new TextField('test_field', 'Test field');
        $field->setRequired();

        // An empty answer first, then a valid one
        $double = new TerminalDouble("\ntest value\n");
        $form = new Form($double->terminal);
        $form->addField($field);

        $form->handle();

        $this->assertEquals(['test_field' => 'test value'], $form->getValues());
        $this->assertStringContainsString('Please correct the following errors:', $double->display());
    }

    /**
     * @throws Exception
     */
    public function testTerminalInjection(): void
    {
        $double = new TerminalDouble();
        $renderer = new FormRenderer($double->terminal);

        $field = $this->createMock(PasswordField::class);
        $field->expects($this->once())->method('clearErrors');
        $field->expects($this->once())->method('setTerminal')->with($double->terminal);
        $field->expects($this->once())->method('isMaskInput')->willReturn(true);
        $field->expects($this->once())->method('processInput')->willReturn('test');

        $this->assertEquals('test', $renderer->renderField($field));
    }

    public function testClearGoesThroughTheTerminal(): void
    {
        $double = new TerminalDouble('', true);
        (new FormRenderer($double->terminal))->clear();

        $this->assertEquals("\033[H\033[2J\033[3J", $double->display());
    }
}

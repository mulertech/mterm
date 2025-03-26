<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\SelectField;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

class SelectFieldTest extends TestCase
{
    private SelectField $field;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $terminal = $this->createMock(Terminal::class);
        $this->field = new SelectField('select', 'Select Field');
        $this->field->setTerminal($terminal);
    }

    public function testSetOptions(): void
    {
        $options = [
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ];

        $result = $this->field->setOptions($options);
        $this->assertSame($this->field, $result);

        // Test validation with valid option
        $errors = $this->field->validate('opt2');
        $this->assertEmpty($errors);

        // Test validation with invalid option
        $errors = $this->field->validate('invalid');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid option', $errors[0]);
    }

    public function testSetMultipleSelection(): void
    {
        $result = $this->field->setMultipleSelection();
        $this->assertSame($this->field, $result);

        $reflection = new ReflectionClass($this->field);
        $property = $reflection->getProperty('multipleSelection');
        $this->assertTrue($property->getValue($this->field));
    }

    public function testValidateMultipleSelection(): void
    {
        $options = [
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ];

        $this->field->setOptions($options);
        $this->field->setMultipleSelection();

        // Test with valid options
        $errors = $this->field->validate(['opt1' => 'Option 1', 'opt3' => 'Option 3']);
        $this->assertEmpty($errors);

        // Test with mix of valid and invalid options
        $errors = $this->field->validate(['opt1' => 'Option 1', 'invalid' => 'Invalid', 'opt3' => 'Option 3']);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid option: \'invalid\'', $errors[0]);
    }

    public function testValidateWithEmptyValue(): void
    {
        $this->field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ]);

        $this->field->setRequired(false);
        $errors = $this->field->validate('');
        $this->assertEmpty($errors);

        $this->field->setRequired();
        $errors = $this->field->validate('');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('required', $errors[0]);
    }

    public function testSetDefaultBeforeOptions(): void
    {
        $this->expectException(RuntimeException::class);
        $this->field->setDefault('opt1');
    }

    public function testSetBadDefaultValues(): void
    {
        $this->field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ]);

        $this->expectException(RuntimeException::class);
        $this->field->setRequired(false)
            ->setDefault(['opt2', 'opt3', 'opt4']);
    }

    public function testSetNumericDefaultValues(): void
    {
        $this->field->setOptions([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3'
        ]);

        $this->expectException(RuntimeException::class);
        $this->field->setRequired(false)
            ->setDefault(55);
    }

    public function testProcessInputWithoutTerminalSet(): void
    {
        $field = new SelectField('color', 'Select Color');
        $field->setOptions([
            'red' => 'Red',
            'green' => 'Green',
            'blue' => 'Blue'
        ])
            ->setDefault('green');
        $this->expectException(RuntimeException::class);
        $field->processInput();
    }
}
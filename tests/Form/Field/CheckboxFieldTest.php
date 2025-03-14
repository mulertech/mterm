<?php

namespace MulerTech\MTerm\Tests\Form\Field;

use MulerTech\MTerm\Form\Field\CheckboxField;
use PHPUnit\Framework\TestCase;

class CheckboxFieldTest extends TestCase
{
    private CheckboxField $field;

    protected function setUp(): void
    {
        $this->field = new CheckboxField('consent', 'Do you agree?');
    }

    public function testDefaultValues(): void
    {
        // Test default behavior with true values
        $this->assertEquals('1', $this->field->processInput('yes'));

        // Test default behavior with false values
        $this->assertEquals('0', $this->field->processInput('no'));
    }

    public function testSetCheckedValue(): void
    {
        $this->field->setCheckedValue('accepted');

        // With custom checked value, true inputs should return that value
        $this->assertEquals('accepted', $this->field->processInput('yes'));
        $this->assertEquals('accepted', $this->field->processInput('1'));
    }

    public function testSetUncheckedValue(): void
    {
        $this->field->setUncheckedValue('declined');

        // With custom unchecked value, false inputs should return that value
        $this->assertEquals('declined', $this->field->processInput('no'));
        $this->assertEquals('declined', $this->field->processInput('whatever'));
    }

    public function testProcessInputWithEmptyInput(): void
    {
        // Test empty input (should return default value)
        $this->field->setDefault('1');
        $this->assertEquals('1', $this->field->processInput(''));

        // Change default and test again
        $this->field->setDefault('0');
        $this->assertEquals('0', $this->field->processInput(''));
    }

    public function testProcessInputWithTrueValues(): void
    {
        // Test various values recognized as "true"
        $this->assertEquals('1', $this->field->processInput('yes'));
        $this->assertEquals('1', $this->field->processInput('y'));
        $this->assertEquals('1', $this->field->processInput('true'));
        $this->assertEquals('1', $this->field->processInput('t'));
        $this->assertEquals('1', $this->field->processInput('1'));
    }

    public function testProcessInputWithFalseValues(): void
    {
        // Test values recognized as "false"
        $this->assertEquals('0', $this->field->processInput('no'));
        $this->assertEquals('0', $this->field->processInput('n'));
        $this->assertEquals('0', $this->field->processInput('false'));
        $this->assertEquals('0', $this->field->processInput('f'));
        $this->assertEquals('0', $this->field->processInput('0'));
        $this->assertEquals('0', $this->field->processInput('whatever'));
    }

    public function testProcessInputWithCustomValues(): void
    {
        $this->field->setCheckedValue('oui');
        $this->field->setUncheckedValue('non');

        // Test with custom values
        $this->assertEquals('oui', $this->field->processInput('yes'));
        $this->assertEquals('oui', $this->field->processInput('oui'));
        $this->assertEquals('non', $this->field->processInput('no'));
        $this->assertEquals('non', $this->field->processInput('whatever'));
    }

    public function testInputIsTrimmed(): void
    {
        // Check that input is trimmed
        $this->assertEquals('1', $this->field->processInput('  yes  '));
        $this->assertEquals('0', $this->field->processInput('  no  '));
    }

    public function testInputIsCaseInsensitive(): void
    {
        // Check that input is case-insensitive
        $this->assertEquals('1', $this->field->processInput('YES'));
        $this->assertEquals('1', $this->field->processInput('True'));
        $this->assertEquals('0', $this->field->processInput('NO'));
        $this->assertEquals('0', $this->field->processInput('False'));
    }
}
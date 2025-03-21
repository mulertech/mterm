<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\ChoiceValidator;
use PHPUnit\Framework\TestCase;

class ChoiceValidatorTest extends TestCase
{
    public function testValidateWithValidStringChoice(): void
    {
        $validator = new ChoiceValidator(['red', 'green', 'blue']);
        $this->assertNull($validator->validate('red'));
        $this->assertNull($validator->validate('green'));
        $this->assertNull($validator->validate('blue'));
    }

    public function testValidateWithInvalidStringChoice(): void
    {
        $validator = new ChoiceValidator(['red', 'green', 'blue']);
        $this->assertEquals(
            'Selected value is not a valid choice.',
            $validator->validate('yellow')
        );
    }

    public function testValidateWithNumericChoices(): void
    {
        $validator = new ChoiceValidator([1, 2, 3]);
        $this->assertNull($validator->validate(1));
        $this->assertNull($validator->validate(2));
        $this->assertNull($validator->validate(3));
        $this->assertEquals(
            'Selected value is not a valid choice.',
            $validator->validate(4)
        );
    }

    public function testValidateWithStrictComparison(): void
    {
        $validator = new ChoiceValidator([1, 2, 3], true);
        // String "1" should fail with strict comparison
        $this->assertEquals(
            'Selected value is not a valid choice.',
            $validator->validate('1')
        );
    }

    public function testValidateWithNonStrictComparison(): void
    {
        $validator = new ChoiceValidator([1, 2, 3], false);
        // String "1" should pass with non-strict comparison
        $this->assertNull($validator->validate('1'));
    }

    public function testValidateWithNullOrEmptyValue(): void
    {
        $validator = new ChoiceValidator(['red', 'green', 'blue']);
        $this->assertNull($validator->validate(null));
        $this->assertNull($validator->validate(''));
    }

    public function testValidateWithCustomErrorMessage(): void
    {
        $customMessage = 'Please select a valid color.';
        $validator = new ChoiceValidator(['red', 'green', 'blue'], true, $customMessage);
        $this->assertEquals(
            $customMessage,
            $validator->validate('yellow')
        );
    }

    public function testValidateWithArrayContainingNullValue(): void
    {
        $validator = new ChoiceValidator(['red', null, 'blue']);
        $this->assertNull($validator->validate(null));
        $this->assertNull($validator->validate('red'));
        $this->assertEquals(
            'Selected value is not a valid choice.',
            $validator->validate('green')
        );
    }
}
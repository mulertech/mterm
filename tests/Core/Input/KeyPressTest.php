<?php

namespace MulerTech\MTerm\Tests\Core\Input;

use MulerTech\MTerm\Core\Input\Key;
use MulerTech\MTerm\Core\Input\KeyPress;
use PHPUnit\Framework\TestCase;

class KeyPressTest extends TestCase
{
    public function testAKeyPressCarriesItsKey(): void
    {
        $press = KeyPress::ofKey(Key::Up);

        $this->assertTrue($press->is(Key::Up));
        $this->assertFalse($press->is(Key::Down));
        $this->assertFalse($press->isCharacter());
        $this->assertFalse($press->isEndOfInput());
    }

    public function testACharacterPressCarriesNoKey(): void
    {
        $press = KeyPress::ofCharacter('a');

        $this->assertNull($press->key);
        $this->assertTrue($press->isCharacter());
        $this->assertEquals('a', $press->character);
        $this->assertFalse($press->is(Key::Enter));
    }

    public function testEndOfInputIsNeitherKeyNorCharacter(): void
    {
        $press = KeyPress::endOfInput();

        $this->assertTrue($press->isEndOfInput());
        $this->assertFalse($press->isCharacter());
        $this->assertNull($press->key);
    }
}

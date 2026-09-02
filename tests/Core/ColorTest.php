<?php

namespace MulerTech\MTerm\Tests\Core;

use MulerTech\MTerm\Core\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testSequenceWithoutBold(): void
    {
        $this->assertEquals("\033[0;32m", Color::Green->sequence());
    }

    public function testSequenceWithBold(): void
    {
        $this->assertEquals("\033[1;31m", Color::Red->sequence(true));
    }

    public function testEveryColorHasItsOwnCode(): void
    {
        $codes = array_map(static fn (Color $color): string => $color->value, Color::cases());

        $this->assertCount(count($codes), array_unique($codes));
    }

    public function testResetClosesEveryDecoration(): void
    {
        $this->assertEquals("\033[0m", Color::RESET);
    }
}

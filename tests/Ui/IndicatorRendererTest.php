<?php

namespace MulerTech\MTerm\Tests\Ui;

use MulerTech\MTerm\Core\Color;
use MulerTech\MTerm\Tests\Support\TerminalDouble;
use MulerTech\MTerm\Ui\Indicator;
use MulerTech\MTerm\Ui\IndicatorRenderer;
use MulerTech\MTerm\Ui\IndicatorStatus;
use PHPUnit\Framework\TestCase;

class IndicatorRendererTest extends TestCase
{
    public function testAnIndicatorWithoutRemedyIsOneLine(): void
    {
        $double = new TerminalDouble();
        (new IndicatorRenderer($double->terminal))->render(Indicator::compliant('Containers running'));

        $this->assertEquals('✔ Containers running'.PHP_EOL, $double->display());
    }

    public function testRemediesShareOneColumn(): void
    {
        $double = new TerminalDouble();

        (new IndicatorRenderer($double->terminal))->render(
            Indicator::failing('TLS certificate expired', 'renew it'),
            Indicator::watch('Disk at 82%', 'prune the images'),
        );

        $this->assertEquals(
            '✘ TLS certificate expired  → renew it'.PHP_EOL
            .'▲ Disk at 82%              → prune the images'.PHP_EOL,
            $double->display()
        );
    }

    public function testAlignmentCountsDisplayedWidthNotBytes(): void
    {
        $double = new TerminalDouble();

        (new IndicatorRenderer($double->terminal))->render(
            Indicator::watch('Sauvegardé', 'à vérifier'),
            Indicator::watch('Disk usage', 'prune'),
        );

        $lines = explode(PHP_EOL, trim($double->display()));

        $this->assertEquals(mb_strpos($lines[0], '→'), mb_strpos($lines[1], '→'));
    }

    public function testEachStatusIsWrittenInItsColor(): void
    {
        $double = new TerminalDouble('', true);
        (new IndicatorRenderer($double->terminal))->render(Indicator::failing('Down', 'restart it'));

        $this->assertStringStartsWith(Color::Red->sequence(), $double->display());
        $this->assertStringContainsString('→ restart it', $double->display());
    }

    public function testEveryStatusIsRendered(): void
    {
        $double = new TerminalDouble();
        $renderer = new IndicatorRenderer($double->terminal);

        $renderer->render(
            Indicator::compliant('One'),
            Indicator::watch('Two'),
            Indicator::failing('Three', 'fix it'),
            Indicator::unavailable('Four'),
        );

        foreach (IndicatorStatus::cases() as $status) {
            $this->assertStringContainsString($status->symbol(), $double->display());
        }
    }

    public function testRenderingNothingDisplaysNothing(): void
    {
        $double = new TerminalDouble();
        (new IndicatorRenderer($double->terminal))->render();

        $this->assertEquals('', $double->display());
    }
}

<?php

namespace MulerTech\MTerm\Tests\Ui;

use InvalidArgumentException;
use MulerTech\MTerm\Core\Color;
use MulerTech\MTerm\Ui\Indicator;
use MulerTech\MTerm\Ui\IndicatorStatus;
use PHPUnit\Framework\TestCase;

class IndicatorTest extends TestCase
{
    public function testACompliantIndicatorNeedsNoRemedy(): void
    {
        $indicator = Indicator::compliant('Containers running');

        $this->assertEquals(IndicatorStatus::Compliant, $indicator->status);
        $this->assertEquals('Containers running', $indicator->label);
        $this->assertNull($indicator->remedy);
    }

    public function testAWatchedIndicatorMayCarryARemedy(): void
    {
        $this->assertNull(Indicator::watch('Disk at 78%')->remedy);
        $this->assertEquals('prune the unused images', Indicator::watch('Disk at 78%', 'prune the unused images')->remedy);
    }

    public function testAFailingIndicatorCarriesItsRemedy(): void
    {
        $indicator = Indicator::failing('Certificate expired', 'renew it');

        $this->assertEquals(IndicatorStatus::Failing, $indicator->status);
        $this->assertEquals('renew it', $indicator->remedy);
    }

    public function testAFailingIndicatorWithoutRemedyIsRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must carry the label of its remedy');

        Indicator::of(IndicatorStatus::Failing, 'Certificate expired');
    }

    public function testAFailingIndicatorWithABlankRemedyIsRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Indicator::failing('Certificate expired', '   ');
    }

    public function testAnUnavailableIndicatorMayCarryItsReason(): void
    {
        $indicator = Indicator::unavailable('Backup age', 'host unreachable');

        $this->assertEquals(IndicatorStatus::Unavailable, $indicator->status);
        $this->assertEquals('host unreachable', $indicator->remedy);
        $this->assertNull(Indicator::unavailable('Backup age')->remedy);
    }

    public function testAnIndicatorWithoutLabelIsRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must carry a label');

        Indicator::compliant(' ');
    }

    public function testARuntimeStatusGoesThroughTheSameRule(): void
    {
        $indicator = Indicator::of(IndicatorStatus::Watch, 'Disk at 78%');

        $this->assertEquals(IndicatorStatus::Watch, $indicator->status);
    }

    public function testEachStatusHasItsOwnColorAndShape(): void
    {
        $colors = array_map(static fn (IndicatorStatus $status): Color => $status->color(), IndicatorStatus::cases());
        $symbols = array_map(static fn (IndicatorStatus $status): string => $status->symbol(), IndicatorStatus::cases());

        $this->assertCount(4, array_unique($colors, SORT_REGULAR));
        $this->assertCount(4, array_unique($symbols));
    }

    public function testTheColorsFollowTheAgreedMeaning(): void
    {
        $this->assertEquals(Color::Green, IndicatorStatus::Compliant->color());
        $this->assertEquals(Color::Yellow, IndicatorStatus::Watch->color());
        $this->assertEquals(Color::Red, IndicatorStatus::Failing->color());
        $this->assertEquals(Color::Black, IndicatorStatus::Unavailable->color());
    }
}

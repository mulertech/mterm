<?php

namespace MulerTech\MTerm\Ui;

use MulerTech\MTerm\Core\Color;

/**
 * The four states a checked item can be in.
 *
 * Each carries a shape as well as a color, so a colorless output — a redirected
 * report, a terminal answering NO_COLOR — still tells the four apart.
 *
 * @author Sébastien Muler
 */
enum IndicatorStatus
{
    /** Conforms to what is expected. */
    case Compliant;

    /** Works, but drifts towards a failure worth preventing. */
    case Watch;

    /** Does not conform, and says how to put it right. */
    case Failing;

    /** Cannot be checked at all, so says nothing about conformity. */
    case Unavailable;

    public function color(): Color
    {
        return match ($this) {
            self::Compliant => Color::Green,
            self::Watch => Color::Yellow,
            self::Failing => Color::Red,
            self::Unavailable => Color::Black,
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::Compliant => '✔',
            self::Watch => '▲',
            self::Failing => '✘',
            self::Unavailable => '·',
        };
    }
}

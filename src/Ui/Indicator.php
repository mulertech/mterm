<?php

namespace MulerTech\MTerm\Ui;

/**
 * One checked item, its state, and what to do about it.
 *
 * A failing indicator without the label of its remedy is refused: a panel can
 * therefore not announce a problem while leaving the reader without a move.
 *
 * @author Sébastien Muler
 */
final readonly class Indicator
{
    private function __construct(
        public IndicatorStatus $status,
        public string $label,
        public ?string $remedy,
    ) {
        if ('' === trim($label)) {
            throw new \InvalidArgumentException('An indicator must carry a label.');
        }

        if (IndicatorStatus::Failing === $status && (null === $remedy || '' === trim($remedy))) {
            throw new \InvalidArgumentException(sprintf('The failing indicator "%s" must carry the label of its remedy.', $label));
        }
    }

    /**
     * Build an indicator whose state is only known at runtime.
     */
    public static function of(IndicatorStatus $status, string $label, ?string $remedy = null): self
    {
        return new self($status, $label, $remedy);
    }

    public static function compliant(string $label): self
    {
        return new self(IndicatorStatus::Compliant, $label, null);
    }

    public static function watch(string $label, ?string $remedy = null): self
    {
        return new self(IndicatorStatus::Watch, $label, $remedy);
    }

    public static function failing(string $label, string $remedy): self
    {
        return new self(IndicatorStatus::Failing, $label, $remedy);
    }

    public static function unavailable(string $label, ?string $reason = null): self
    {
        return new self(IndicatorStatus::Unavailable, $label, $reason);
    }
}

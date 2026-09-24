<?php

namespace MulerTech\MTerm\Ui;

/**
 * One line of a menu: either an action to run, or a menu to enter.
 *
 * An action states its confirmation rather than asking for one itself, so a
 * destructive move cannot reach the user unconfirmed through inattention.
 *
 * A line may carry an indicator, drawn at its left in the colour of its state:
 * where to go, read from the menu itself. It is asked for at every drawing, so
 * a line shows what its owner knows at that moment rather than what it knew
 * when the menu was built; `null` draws no indicator — nothing known is not
 * something fine.
 *
 * @author Sébastien Muler
 */
final readonly class MenuItem
{
    /**
     * @param \Closure():void|null                   $action
     * @param \Closure():(IndicatorStatus|null)|null $status
     */
    private function __construct(
        public string $label,
        private ?\Closure $action,
        private ?Menu $submenu,
        public ?string $confirmation,
        private ?\Closure $status,
        private bool $awaitsKey,
    ) {
        if ('' === trim($label)) {
            throw new \InvalidArgumentException('A menu item must carry a label.');
        }
    }

    /**
     * An action whose report stays on screen until a key is pressed — unless it
     * has nothing to show, and hands the menu back at once.
     *
     * @param callable():void                        $action
     * @param string|null                            $confirmation Question to answer before running the action
     * @param callable():(IndicatorStatus|null)|null $status
     */
    public static function action(
        string $label,
        callable $action,
        ?string $confirmation = null,
        ?callable $status = null,
        bool $awaitsKey = true,
    ): self {
        return new self($label, $action(...), null, $confirmation, null === $status ? null : $status(...), $awaitsKey);
    }

    /**
     * @param callable():(IndicatorStatus|null)|null $status
     */
    public static function menu(string $label, Menu $submenu, ?callable $status = null): self
    {
        return new self($label, null, $submenu, null, null === $status ? null : $status(...), true);
    }

    public function getSubmenu(): ?Menu
    {
        return $this->submenu;
    }

    /**
     * @return \Closure():void|null
     */
    public function getAction(): ?\Closure
    {
        return $this->action;
    }

    /**
     * Whether the line has an indicator at all, known yet or not.
     */
    public function hasStatus(): bool
    {
        return null !== $this->status;
    }

    /**
     * What the line's owner knows now, or `null` when it knows nothing yet.
     */
    public function status(): ?IndicatorStatus
    {
        return null === $this->status ? null : ($this->status)();
    }

    /**
     * Whether the menu waits for a key after the action, so that its report is
     * read before the menu draws itself over it.
     */
    public function awaitsKey(): bool
    {
        return $this->awaitsKey;
    }
}

<?php

namespace MulerTech\MTerm\Ui;

/**
 * One line of a menu: either an action to run, or a menu to enter.
 *
 * An action states its confirmation rather than asking for one itself, so a
 * destructive move cannot reach the user unconfirmed through inattention.
 *
 * @author Sébastien Muler
 */
final readonly class MenuItem
{
    /**
     * @param \Closure():void|null $action
     */
    private function __construct(
        public string $label,
        private ?\Closure $action,
        private ?Menu $submenu,
        public ?string $confirmation,
    ) {
        if ('' === trim($label)) {
            throw new \InvalidArgumentException('A menu item must carry a label.');
        }
    }

    /**
     * @param callable():void $action
     * @param string|null     $confirmation Question to answer before running the action
     */
    public static function action(string $label, callable $action, ?string $confirmation = null): self
    {
        return new self($label, $action(...), null, $confirmation);
    }

    public static function menu(string $label, Menu $submenu): self
    {
        return new self($label, null, $submenu, null);
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
}

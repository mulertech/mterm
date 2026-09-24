<?php

namespace MulerTech\MTerm\Ui;

use MulerTech\MTerm\Core\Color;
use MulerTech\MTerm\Core\Input\Key;
use MulerTech\MTerm\Core\Terminal;

/**
 * A list of choices, nestable, driven with the arrow keys.
 *
 * The menu carries the cycle around an action — confirm, run, catch, return —
 * and knows nothing of what the action does. A panel therefore cannot invent
 * its own way of confirming, or forget to.
 *
 * @author Sébastien Muler
 */
class Menu
{
    /**
     * @var list<MenuItem>
     */
    private array $items = [];

    public function __construct(
        private readonly Terminal $terminal,
        private readonly string $title,
    ) {
        if ('' === trim($title)) {
            throw new \InvalidArgumentException('A menu must carry a title.');
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return list<MenuItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return $this
     */
    public function add(MenuItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Display the menu until the user leaves its top level.
     */
    public function run(): void
    {
        $levels = [$this];
        $cursors = [0];

        $this->terminal->enableRawMode();
        $this->terminal->hideCursor();

        try {
            while ([] !== $levels) {
                $depth = count($levels) - 1;
                $menu = $levels[$depth];
                $menu->requireItems();
                $menu->paint($this->path($levels), $cursors[$depth], $depth > 0);

                $press = $this->terminal->readKey();

                if ($press->isEndOfInput() || $press->is(Key::Escape) || 'q' === $press->character) {
                    array_pop($levels);
                    array_pop($cursors);

                    continue;
                }

                if ($press->is(Key::Up)) {
                    $cursors[$depth] = max(0, $cursors[$depth] - 1);

                    continue;
                }

                if ($press->is(Key::Down)) {
                    $cursors[$depth] = min(count($menu->items) - 1, $cursors[$depth] + 1);

                    continue;
                }

                if (!$press->is(Key::Enter)) {
                    continue;
                }

                $item = $menu->items[$cursors[$depth]];
                $submenu = $item->getSubmenu();

                if (null !== $submenu) {
                    $levels[] = $submenu;
                    $cursors[] = 0;

                    continue;
                }

                $this->invoke($item);
            }
        } finally {
            $this->terminal->showCursor();
            $this->terminal->disableRawMode();
            $this->terminal->writeLine();
        }
    }

    private function requireItems(): void
    {
        if ([] === $this->items) {
            throw new \RuntimeException(sprintf('The menu "%s" holds no item to display.', $this->title));
        }
    }

    private function paint(string $path, int $cursor, bool $nested): void
    {
        $this->terminal->clear();
        $this->terminal->writeLine($path, Color::Cyan, true);
        $this->terminal->writeLine();

        // A column of indicators as soon as one line carries one, the others
        // left blank in it so that the labels stay aligned.
        $indicators = [] !== array_filter($this->items, static fn (MenuItem $item): bool => $item->hasStatus());

        foreach ($this->items as $position => $item) {
            $selected = $position === $cursor;
            $suffix = null === $item->getSubmenu() ? '' : ' ›';
            $this->terminal->write($selected ? '❯ ' : '  ', $selected ? Color::Green : null, $selected);

            if ($indicators) {
                $status = $item->status();
                $this->terminal->write(null === $status ? '  ' : $status->symbol().' ', $status?->color());
            }

            $this->terminal->writeLine($item->label.$suffix, $selected ? Color::Green : null, $selected);
        }

        $this->terminal->writeLine();
        $this->terminal->writeLine(
            '↑/↓ move   ENTER select   ESC '.($nested ? 'back' : 'quit'),
            Color::Blue
        );
    }

    /**
     * Confirm, run, report a failure, and hand the terminal back to the menu.
     */
    private function invoke(MenuItem $item): void
    {
        $action = $item->getAction();

        if (null === $action) {
            return;
        }

        if (null !== $item->confirmation && !$this->confirm($item->confirmation)) {
            return;
        }

        $this->terminal->showCursor();
        $this->terminal->disableRawMode();
        $this->terminal->clear();

        try {
            $action();
        } catch (\Throwable $failure) {
            $this->terminal->writeLine();
            $this->terminal->writeLine($failure->getMessage(), Color::Red, true);
        }

        $this->terminal->enableRawMode();
        $this->terminal->hideCursor();
        // After raw mode, which releases a line typed without its Enter: a key
        // struck during the action would otherwise dismiss its report unread.
        $this->terminal->discardPendingInput();

        if (!$item->awaitsKey()) {
            return;
        }

        $this->terminal->writeLine();
        $this->terminal->write('Press any key to return.', Color::Blue);
        $this->terminal->readKey();
    }

    private function confirm(string $question): bool
    {
        $this->terminal->writeLine();
        $this->terminal->write($question.' [y/N] ', Color::Yellow, true);

        return 'y' === mb_strtolower($this->terminal->readKey()->character);
    }

    /**
     * @param list<self> $levels
     */
    private function path(array $levels): string
    {
        return implode(' › ', array_map(static fn (self $menu): string => $menu->title, $levels));
    }
}

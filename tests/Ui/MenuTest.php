<?php

namespace MulerTech\MTerm\Tests\Ui;

use InvalidArgumentException;
use MulerTech\MTerm\Tests\Support\TerminalDouble;
use MulerTech\MTerm\Ui\Menu;
use MulerTech\MTerm\Ui\MenuItem;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MenuTest extends TestCase
{
    public function testAMenuWithoutTitleIsRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Menu((new TerminalDouble())->terminal, ' ');
    }

    public function testAMenuWithoutItemIsRefused(): void
    {
        $double = new TerminalDouble("\n");
        $menu = new Menu($double->terminal, 'Empty');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('holds no item');

        $menu->run();
    }

    public function testAddReturnsTheMenuAndKeepsTheOrder(): void
    {
        $menu = new Menu((new TerminalDouble())->terminal, 'Root');
        $first = MenuItem::action('First', static fn () => null);
        $second = MenuItem::action('Second', static fn () => null);

        $this->assertSame($menu, $menu->add($first));
        $menu->add($second);

        $this->assertSame([$first, $second], $menu->getItems());
        $this->assertEquals('Root', $menu->getTitle());
    }

    public function testEscapeLeavesTheMenu(): void
    {
        $double = new TerminalDouble("\033");
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('First', static fn () => null));

        $menu->run();

        $this->assertStringContainsString('Root', $double->display());
        $this->assertStringContainsString('❯ First', $double->display());
        $this->assertStringContainsString('ESC quit', $double->display());
    }

    public function testTheLetterQLeavesTheMenu(): void
    {
        $double = new TerminalDouble('q');
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('First', static fn () => null));

        $menu->run();

        $this->assertFalse($double->terminal->isRawMode());
    }

    public function testAnExhaustedInputLeavesTheMenu(): void
    {
        $double = new TerminalDouble();
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('First', static fn () => null));

        $menu->run();

        $this->assertStringContainsString('First', $double->display());
    }

    public function testTheArrowsMoveTheSelection(): void
    {
        $double = new TerminalDouble("\033[B\n ");
        $ran = [];
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('First', static function () use (&$ran): void {
            $ran[] = 'first';
        }));
        $menu->add(MenuItem::action('Second', static function () use (&$ran): void {
            $ran[] = 'second';
        }));

        $menu->run();

        $this->assertSame(['second'], $ran);
    }

    public function testTheSelectionStopsAtTheEdges(): void
    {
        $double = new TerminalDouble("\033[A\033[B\033[B\n ");
        $ran = [];
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('First', static function () use (&$ran): void {
            $ran[] = 'first';
        }));
        $menu->add(MenuItem::action('Second', static function () use (&$ran): void {
            $ran[] = 'second';
        }));

        $menu->run();

        $this->assertSame(['second'], $ran);
    }

    public function testAnUnknownKeyChangesNothing(): void
    {
        $double = new TerminalDouble("z\n ");
        $ran = 0;
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('First', static function () use (&$ran): void {
            ++$ran;
        }));

        $menu->run();

        $this->assertEquals(1, $ran);
    }

    public function testASubmenuIsEnteredAndLeft(): void
    {
        $double = new TerminalDouble("\n\033");
        $ran = 0;
        $submenu = new Menu($double->terminal, 'Containers');
        $submenu->add(MenuItem::action('Restart', static function () use (&$ran): void {
            ++$ran;
        }));

        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::menu('Containers', $submenu));

        $menu->run();

        $this->assertEquals(0, $ran);
        $this->assertStringContainsString('Root › Containers', $double->display());
        $this->assertStringContainsString('Containers ›', $double->display());
        $this->assertStringContainsString('ESC back', $double->display());
    }

    public function testAConfirmedActionRuns(): void
    {
        $double = new TerminalDouble("\ny ");
        $ran = 0;
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('Drop', static function () use (&$ran): void {
            ++$ran;
        }, 'Really drop it?'));

        $menu->run();

        $this->assertEquals(1, $ran);
        $this->assertStringContainsString('Really drop it? [y/N]', $double->display());
    }

    public function testARefusedActionDoesNotRun(): void
    {
        $double = new TerminalDouble("\nn");
        $ran = 0;
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('Drop', static function () use (&$ran): void {
            ++$ran;
        }, 'Really drop it?'));

        $menu->run();

        $this->assertEquals(0, $ran);
    }

    public function testAFailingActionReportsAndReturnsToTheMenu(): void
    {
        $double = new TerminalDouble("\n ");
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('Deploy', static function (): void {
            throw new RuntimeException('the registry refused the image');
        }));

        $menu->run();

        $this->assertStringContainsString('the registry refused the image', $double->display());
        $this->assertStringContainsString('Press any key to return.', $double->display());
    }

    public function testTheTerminalIsHandedBackWhenTheMenuEnds(): void
    {
        $double = new TerminalDouble("\033");
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::action('First', static fn () => null));

        $menu->run();

        $this->assertFalse($double->terminal->isRawMode());
        $this->assertEquals(
            ['-g', '-icanon -echo min 1 time 0', 'saved-state'],
            $double->mode->calls
        );
    }

    public function testTheTerminalIsHandedBackWhenAnActionEscapes(): void
    {
        $double = new TerminalDouble("\n");
        $menu = new Menu($double->terminal, 'Root');
        $menu->add(MenuItem::menu('Broken', new Menu($double->terminal, 'Broken')));

        try {
            $menu->run();
        } catch (RuntimeException) {
            $this->assertFalse($double->terminal->isRawMode());

            return;
        }

        $this->fail('An empty submenu should have interrupted the menu.');
    }
}

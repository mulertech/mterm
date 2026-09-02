<?php

namespace MulerTech\MTerm\Tests\Ui;

use InvalidArgumentException;
use MulerTech\MTerm\Tests\Support\TerminalDouble;
use MulerTech\MTerm\Ui\Menu;
use MulerTech\MTerm\Ui\MenuItem;
use PHPUnit\Framework\TestCase;

class MenuItemTest extends TestCase
{
    public function testAnActionItemCarriesItsAction(): void
    {
        $ran = false;
        $item = MenuItem::action('Deploy', static function () use (&$ran): void {
            $ran = true;
        });

        $this->assertEquals('Deploy', $item->label);
        $this->assertNull($item->getSubmenu());
        $this->assertNull($item->confirmation);

        $action = $item->getAction();
        $this->assertNotNull($action);
        $action();
        $this->assertTrue($ran);
    }

    public function testAnActionItemStatesItsConfirmation(): void
    {
        $item = MenuItem::action('Drop the database', static fn () => null, 'Really drop it?');

        $this->assertEquals('Really drop it?', $item->confirmation);
    }

    public function testASubmenuItemCarriesItsMenu(): void
    {
        $submenu = new Menu((new TerminalDouble())->terminal, 'Containers');
        $item = MenuItem::menu('Containers', $submenu);

        $this->assertSame($submenu, $item->getSubmenu());
        $this->assertNull($item->getAction());
    }

    public function testAnItemWithoutLabelIsRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must carry a label');

        MenuItem::action(' ', static fn () => null);
    }
}

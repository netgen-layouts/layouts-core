<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\View\View\ZoneView;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ZoneView::class)]
final class ZoneViewTest extends TestCase
{
    private Layout $layout;

    private Zone $zone;

    private BlockList $blocks;

    private ZoneView $view;

    protected function setUp(): void
    {
        $this->zone = Zone::fromArray(['identifier' => 'zone']);
        $this->layout = Layout::fromArray(
            [
                'zones' => ZoneList::fromArray(
                    [
                        'zone' => $this->zone,
                    ],
                ),
            ],
        );

        $this->blocks = BlockList::fromArray([]);

        $this->view = new ZoneView(new ZoneReference($this->layout, 'zone'), $this->blocks);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('zone', 42);
    }

    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->view->getLayout());
    }

    public function testGetZone(): void
    {
        self::assertSame($this->zone, $this->view->getZone());
    }

    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'param' => 'value',
                'zone' => $this->zone,
                'layout' => $this->layout,
                'blocks' => $this->blocks,
            ],
            $this->view->getParameters(),
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('zone', $this->view::getIdentifier());
    }
}

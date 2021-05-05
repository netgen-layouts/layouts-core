<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\View\View\ZoneView;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\TestCase;

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
                'zones' => new ArrayCollection(
                    [
                        'zone' => $this->zone,
                    ],
                ),
            ],
        );

        $this->blocks = new BlockList();

        $this->view = new ZoneView(new ZoneReference($this->layout, 'zone'), $this->blocks);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('zone', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\ZoneView::__construct
     * @covers \Netgen\Layouts\View\View\ZoneView::getLayout
     */
    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->view->getLayout());
    }

    /**
     * @covers \Netgen\Layouts\View\View\ZoneView::getZone
     */
    public function testGetZone(): void
    {
        self::assertSame($this->zone, $this->view->getZone());
    }

    /**
     * @covers \Netgen\Layouts\View\View\ZoneView::getParameters
     */
    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'layout' => $this->layout,
                'zone' => $this->zone,
                'blocks' => $this->blocks,
                'param' => 'value',
            ],
            $this->view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\ZoneView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('zone', $this->view::getIdentifier());
    }
}

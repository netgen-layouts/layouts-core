<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Block\BlockList;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\View\View\ZoneView;
use Netgen\BlockManager\View\View\ZoneView\ZoneReference;
use PHPUnit\Framework\TestCase;

final class ZoneViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Zone
     */
    private $zone;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\BlockList
     */
    private $blocks;

    /**
     * @var \Netgen\BlockManager\View\View\ZoneViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $this->zone = Zone::fromArray(['identifier' => 'zone']);
        $this->layout = Layout::fromArray(
            [
                'zones' => new ArrayCollection(
                    [
                        'zone' => $this->zone,
                    ]
                ),
            ]
        );

        $this->blocks = new BlockList();

        $this->view = new ZoneView(new ZoneReference($this->layout, 'zone'), $this->blocks);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('zone', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ZoneView::__construct
     * @covers \Netgen\BlockManager\View\View\ZoneView::getLayout
     */
    public function testGetLayout(): void
    {
        self::assertSame($this->layout, $this->view->getLayout());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ZoneView::getZone
     */
    public function testGetZone(): void
    {
        self::assertSame($this->zone, $this->view->getZone());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ZoneView::getParameters
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
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ZoneView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('zone', $this->view::getIdentifier());
    }
}

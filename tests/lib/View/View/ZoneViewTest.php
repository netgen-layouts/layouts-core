<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\API\Values\Block\BlockList;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\View\View\ZoneView;
use PHPUnit\Framework\TestCase;

final class ZoneViewTest extends TestCase
{
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
        $this->zone = new Zone();
        $this->blocks = new BlockList();

        $this->view = new ZoneView($this->zone, $this->blocks);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('zone', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ZoneView::__construct
     * @covers \Netgen\BlockManager\View\View\ZoneView::getZone
     */
    public function testGetZone(): void
    {
        self::assertSame($this->zone, $this->view->getZone());
        self::assertSame(
            [
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

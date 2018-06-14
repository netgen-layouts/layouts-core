<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\View\View\LayoutTypeView;
use PHPUnit\Framework\TestCase;

final class LayoutTypeViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    private $layoutType;

    /**
     * @var \Netgen\BlockManager\View\View\LayoutTypeViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $this->layoutType = new LayoutType(['identifier' => 'layout']);

        $this->view = new LayoutTypeView(
            [
                'layoutType' => $this->layoutType,
            ]
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('layoutType', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutTypeView::__construct
     * @covers \Netgen\BlockManager\View\View\LayoutTypeView::getLayoutType
     */
    public function testGetLayoutType(): void
    {
        $this->assertEquals($this->layoutType, $this->view->getLayoutType());
        $this->assertEquals(
            [
                'param' => 'value',
                'layoutType' => $this->layoutType,
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutTypeView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('layout_view', $this->view->getIdentifier());
    }
}

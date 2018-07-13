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

        $this->view = new LayoutTypeView($this->layoutType);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('layout_type', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutTypeView::__construct
     * @covers \Netgen\BlockManager\View\View\LayoutTypeView::getLayoutType
     */
    public function testGetLayoutType(): void
    {
        $this->assertSame($this->layoutType, $this->view->getLayoutType());
        $this->assertSame(
            [
                'layout_type' => $this->layoutType,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutTypeView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('layout', $this->view::getIdentifier());
    }
}

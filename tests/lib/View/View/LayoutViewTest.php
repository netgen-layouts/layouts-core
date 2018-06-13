<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

final class LayoutViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    /**
     * @var \Netgen\BlockManager\View\View\LayoutViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->layout = new Layout(['id' => 42]);

        $this->view = new LayoutView(
            [
                'layout' => $this->layout,
            ]
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('layout', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutView::__construct
     * @covers \Netgen\BlockManager\View\View\LayoutView::getLayout
     */
    public function testGetLayout()
    {
        $this->assertEquals($this->layout, $this->view->getLayout());
        $this->assertEquals(
            [
                'param' => 'value',
                'layout' => $this->layout,
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\LayoutView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('layout_view', $this->view->getIdentifier());
    }
}
